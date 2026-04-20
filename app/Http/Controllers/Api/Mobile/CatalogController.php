<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use App\Support\Analytics\ProductAnalytics;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function ofertas(Request $request, ProductAnalytics $analytics): JsonResponse
    {
        $filtros = $this->filtros($request);
        $perPage = min(30, max(6, (int) $request->integer('per_page', 12)));

        $produtos = $this->produtosBase($filtros)
            ->tap(fn (Builder $query) => $this->ordenar($query, $filtros['ordenar']))
            ->paginate($perPage)
            ->withQueryString();

        $collection = $produtos->getCollection();
        $this->carregarOfertas($collection, $filtros);

        $analytics->track(
            $request,
            $this->temFiltroAtivo($filtros) ? 'mobile.catalog.filtered' : 'mobile.offers.listed',
            'mobile',
            [
                ...$filtros,
                'total' => $produtos->total(),
            ]
        );

        return response()->json([
            'data' => $collection->map(fn (Produto $produto) => $this->produtoCard($produto))->values(),
            'meta' => [
                'current_page' => $produtos->currentPage(),
                'last_page' => $produtos->lastPage(),
                'per_page' => $produtos->perPage(),
                'total' => $produtos->total(),
            ],
            'filters' => [
                'categorias' => Categoria::query()->orderBy('nome')->get(['id', 'nome', 'slug']),
                'cidades' => Loja::query()->where('status', 'ativo')->whereNotNull('cidade')->distinct()->orderBy('cidade')->pluck('cidade'),
                'tipos_preco' => Preco::query()
                    ->whereHas('loja', fn (Builder $query) => $query->where('status', 'ativo'))
                    ->distinct()
                    ->orderBy('tipo_preco')
                    ->pluck('tipo_preco'),
            ],
        ]);
    }

    public function produto(Request $request, Produto $produto, ProductAnalytics $analytics): JsonResponse
    {
        abort_unless($produto->status === 'ativo', 404);

        $produto->load([
            'categoria',
            'marca',
            'precos' => fn ($query) => $query->with('loja')->whereHas('loja', fn ($lojaQuery) => $lojaQuery->where('status', 'ativo'))->orderBy('preco'),
        ]);

        $ofertas = $produto->precos->values();
        $menor = (float) $ofertas->min('preco');
        $maior = (float) $ofertas->max('preco');

        $analytics->track($request, 'mobile.product.viewed', 'mobile', [
            'produto' => $produto->nome,
            'ofertas' => $ofertas->count(),
            'menor_preco' => $menor,
            'maior_preco' => $maior,
        ], $produto);

        return response()->json([
            'data' => [
                ...$this->produtoBase($produto),
                'descricao' => $produto->descricao,
                'especificacoes' => $produto->especificacoes ?? [],
                'galeria' => collect($produto->galeria_imagens ?? [])->map(fn ($imagem) => $this->imageUrl($imagem))->values(),
                'resumo' => [
                    'menor_preco' => $menor,
                    'maior_preco' => $maior,
                    'economia' => max(0, $maior - $menor),
                    'ofertas' => $ofertas->count(),
                    'cidades' => $ofertas->pluck('loja.cidade')->filter()->unique()->values(),
                ],
                'ofertas' => $ofertas->map(fn (Preco $preco) => $this->ofertaPayload($preco))->values(),
            ],
        ]);
    }

    public function loja(Request $request, Loja $loja, ProductAnalytics $analytics): JsonResponse
    {
        abort_unless($loja->status === 'ativo', 404);

        $loja->loadCount('precos');
        $loja->loadAvg('avaliacoes', 'nota');
        $loja->load([
            'precos' => fn ($query) => $query->with(['produto.categoria', 'produto.marca'])->orderBy('preco'),
        ]);

        $analytics->track($request, 'mobile.store.viewed', 'mobile', [
            'loja' => $loja->nome,
            'precos' => $loja->precos_count,
        ], $loja);

        return response()->json([
            'data' => [
                'id' => $loja->id,
                'nome' => $loja->nome,
                'logo' => $this->imageUrl($loja->logo),
                'cidade' => $loja->cidade,
                'uf' => $loja->uf,
                'tipo_loja' => $loja->tipo_loja,
                'contato' => [
                    'telefone' => $loja->telefone,
                    'whatsapp' => $loja->whatsapp,
                    'email' => $loja->email,
                    'site' => $loja->site,
                    'instagram' => $loja->instagram,
                ],
                'resumo' => [
                    'precos' => $loja->precos_count,
                    'preco_medio' => round((float) $loja->precos->avg('preco'), 2),
                    'avaliacao_media' => round((float) ($loja->avaliacoes_avg_nota ?? 0), 1),
                ],
                'ofertas' => $loja->precos->take(30)->map(fn (Preco $preco) => [
                    'id' => $preco->id,
                    'preco' => (float) $preco->preco,
                    'tipo_preco' => $preco->tipo_preco,
                    'produto' => $this->produtoBase($preco->produto),
                ])->values(),
            ],
        ]);
    }

    private function filtros(Request $request): array
    {
        return [
            'busca' => trim((string) $request->string('busca')),
            'categoria' => trim((string) $request->string('categoria')),
            'cidade' => trim((string) $request->string('cidade')),
            'tipo_preco' => trim((string) $request->string('tipo_preco')),
            'preco_ate' => $request->filled('preco_ate') ? (float) $request->input('preco_ate') : null,
            'ordenar' => in_array((string) $request->string('ordenar'), ['menor_preco', 'maior_economia', 'mais_ofertas', 'alfabetica'], true)
                ? (string) $request->string('ordenar')
                : 'menor_preco',
        ];
    }

    private function produtosBase(array $filtros): Builder
    {
        $escopoPrecoPublico = fn (Builder $query) => $query
            ->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('status', 'ativo'))
            ->when($filtros['cidade'] !== '', fn (Builder $precoQuery) => $precoQuery->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $filtros['cidade'])))
            ->when($filtros['tipo_preco'] !== '', fn (Builder $precoQuery) => $precoQuery->where('tipo_preco', $filtros['tipo_preco']))
            ->when($filtros['preco_ate'] !== null, fn (Builder $precoQuery) => $precoQuery->where('preco', '<=', $filtros['preco_ate']));

        return Produto::query()
            ->where('status', 'ativo')
            ->with(['categoria', 'marca'])
            ->withCount(['precos' => $escopoPrecoPublico])
            ->withMin(['precos as menor_preco' => $escopoPrecoPublico], 'preco')
            ->withMax(['precos as maior_preco' => $escopoPrecoPublico], 'preco')
            ->whereHas('precos', $escopoPrecoPublico)
            ->when($filtros['busca'] !== '', fn (Builder $query) => $query->where(fn (Builder $subquery) => $subquery
                ->where('nome', 'like', "%{$filtros['busca']}%")
                ->orWhere('descricao', 'like', "%{$filtros['busca']}%")
                ->orWhereHas('marca', fn (Builder $marcaQuery) => $marcaQuery->where('nome', 'like', "%{$filtros['busca']}%"))))
            ->when($filtros['categoria'] !== '', fn (Builder $query) => $query->whereHas('categoria', fn (Builder $categoriaQuery) => $categoriaQuery->where('slug', $filtros['categoria'])));
    }

    private function ordenar(Builder $query, string $ordenar): void
    {
        match ($ordenar) {
            'maior_economia' => $query->orderByRaw('(COALESCE(maior_preco, 0) - COALESCE(menor_preco, 0)) desc'),
            'mais_ofertas' => $query->orderByDesc('precos_count'),
            'alfabetica' => $query->orderBy('nome'),
            default => $query->orderBy('menor_preco'),
        };
    }

    private function carregarOfertas(Collection $produtos, array $filtros): void
    {
        $produtos->each(function (Produto $produto) use ($filtros) {
            $produto->setRelation('melhores_ofertas', $produto->precos()
                ->with('loja')
                ->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('status', 'ativo'))
                ->when($filtros['cidade'] !== '', fn (Builder $query) => $query->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $filtros['cidade'])))
                ->when($filtros['tipo_preco'] !== '', fn (Builder $query) => $query->where('tipo_preco', $filtros['tipo_preco']))
                ->when($filtros['preco_ate'] !== null, fn (Builder $query) => $query->where('preco', '<=', $filtros['preco_ate']))
                ->orderBy('preco')
                ->take(3)
                ->get());
        });
    }

    private function produtoCard(Produto $produto): array
    {
        $ofertas = $produto->getRelationValue('melhores_ofertas') ?? collect();

        return [
            ...$this->produtoBase($produto),
            'resumo' => [
                'menor_preco' => (float) ($produto->menor_preco ?? 0),
                'maior_preco' => (float) ($produto->maior_preco ?? 0),
                'economia' => max(0, (float) ($produto->maior_preco ?? 0) - (float) ($produto->menor_preco ?? 0)),
                'ofertas' => (int) ($produto->precos_count ?? 0),
            ],
            'melhores_ofertas' => $ofertas->map(fn (Preco $preco) => $this->ofertaPayload($preco))->values(),
        ];
    }

    private function produtoBase(?Produto $produto): ?array
    {
        if (! $produto) {
            return null;
        }

        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'slug' => $produto->slug,
            'imagem' => $produto->imagem_url,
            'categoria' => $produto->categoria ? [
                'id' => $produto->categoria->id,
                'nome' => $produto->categoria->nome,
                'slug' => $produto->categoria->slug,
            ] : null,
            'marca' => $produto->marca ? [
                'id' => $produto->marca->id,
                'nome' => $produto->marca->nome,
            ] : null,
        ];
    }

    private function ofertaPayload(Preco $preco): array
    {
        return [
            'id' => $preco->id,
            'preco' => (float) $preco->preco,
            'tipo_preco' => $preco->tipo_preco,
            'loja' => $preco->loja ? [
                'id' => $preco->loja->id,
                'nome' => $preco->loja->nome,
                'cidade' => $preco->loja->cidade,
                'uf' => $preco->loja->uf,
                'tipo_loja' => $preco->loja->tipo_loja,
            ] : null,
        ];
    }

    private function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:image'])) {
            return $path;
        }

        return Str::startsWith($path, '/') ? $path : asset($path);
    }

    private function temFiltroAtivo(array $filtros): bool
    {
        return $filtros['busca'] !== ''
            || $filtros['categoria'] !== ''
            || $filtros['cidade'] !== ''
            || $filtros['tipo_preco'] !== ''
            || $filtros['preco_ate'] !== null
            || $filtros['ordenar'] !== 'menor_preco';
    }
}
