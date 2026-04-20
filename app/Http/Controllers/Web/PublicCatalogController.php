<?php

namespace App\Http\Controllers\Web;

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
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    public function __invoke(Request $request, ProductAnalytics $analytics): View
    {
        $filtros = $this->normalizarFiltros($request);
        $produtos = $this->ordenarProdutos($this->produtosBase($filtros), $filtros['ordenar'])
            ->paginate(9)
            ->withQueryString();

        $this->carregarMelhoresOfertas($produtos->getCollection(), $filtros);

        $precosFiltrados = $this->precosFiltrados($filtros)->get();
        $snapshot = $this->montarSnapshotMercado(
            $produtos->getCollection(),
            $precosFiltrados,
            $filtros['ordenar'],
            $produtos->total()
        );

        $categorias = Categoria::query()
            ->orderBy('nome')
            ->get();

        $cidades = Loja::query()
            ->where('status', 'ativo')
            ->whereNotNull('cidade')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $tiposPreco = Preco::query()
            ->distinct()
            ->orderBy('tipo_preco')
            ->pluck('tipo_preco');

        if ($this->temFiltroAtivo($filtros)) {
            $analytics->track($request, 'public.catalog.filtered', 'public', [
                'busca' => $filtros['busca'],
                'categoria' => $filtros['categoriaSlug'],
                'cidade' => $filtros['cidade'],
                'tipo_preco' => $filtros['tipoPreco'],
                'preco_ate' => $filtros['precoAte'],
                'ordenar' => $filtros['ordenar'],
                'total_resultados' => $snapshot['total_resultados'],
                'total_ofertas' => $snapshot['total_ofertas'],
            ]);
        }

        return view('welcome', [
            'busca' => $filtros['busca'],
            'categoriaSlug' => $filtros['categoriaSlug'],
            'cidade' => $filtros['cidade'],
            'tipoPreco' => $filtros['tipoPreco'],
            'precoAte' => $filtros['precoAte'],
            'ordenar' => $filtros['ordenar'],
            'categorias' => $categorias,
            'cidades' => $cidades,
            'tiposPreco' => $tiposPreco,
            'produtos' => $produtos,
            'totalResultados' => $snapshot['total_resultados'],
            'totalOfertas' => $snapshot['total_ofertas'],
            'lojasAtivas' => $snapshot['lojas_ativas'],
            'faixaMedia' => $snapshot['faixa_media'],
            'categoriaChart' => $this->montarCategoriasChart($precosFiltrados),
            'cidadeChart' => $this->montarCidadesChart($precosFiltrados),
            'spreadChart' => $this->montarSpreadChart($produtos->getCollection()),
            'pulse' => $snapshot['pulse'],
            'radarMercado' => $snapshot['radar_mercado'],
        ]);
    }

    private function temFiltroAtivo(array $filtros): bool
    {
        return $filtros['busca'] !== ''
            || $filtros['categoriaSlug'] !== ''
            || $filtros['cidade'] !== ''
            || $filtros['tipoPreco'] !== ''
            || $filtros['precoAte'] !== null
            || $filtros['ordenar'] !== 'menor_preco';
    }

    public function radar(Request $request): JsonResponse
    {
        $filtros = $this->normalizarFiltros($request);
        $produtos = $this->ordenarProdutos($this->produtosBase($filtros), $filtros['ordenar'])
            ->take(9)
            ->get();

        $this->carregarMelhoresOfertas($produtos, $filtros);

        $snapshot = $this->montarSnapshotMercado(
            $produtos,
            $this->precosFiltrados($filtros)->get(),
            $filtros['ordenar'],
            $this->produtosBase($filtros)->count()
        );

        return response()->json($snapshot);
    }

    private function normalizarFiltros(Request $request): array
    {
        return [
            'busca' => trim((string) $request->string('busca')),
            'categoriaSlug' => trim((string) $request->string('categoria')),
            'cidade' => trim((string) $request->string('cidade')),
            'tipoPreco' => trim((string) $request->string('tipo_preco')),
            'precoAte' => $request->filled('preco_ate') ? (float) $request->input('preco_ate') : null,
            'ordenar' => trim((string) $request->string('ordenar', 'menor_preco')),
        ];
    }

    private function produtosBase(array $filtros): Builder
    {
        return Produto::query()
            ->where('status', 'ativo')
            ->with(['categoria', 'marca'])
            ->withCount('precos')
            ->withMin('precos as menor_preco', 'preco')
            ->withMax('precos as maior_preco', 'preco')
            ->whereHas('precos.loja', fn (Builder $query) => $query->where('status', 'ativo'))
            ->when($filtros['busca'] !== '', function (Builder $query) use ($filtros) {
                $query->where(function (Builder $subquery) use ($filtros) {
                    $subquery
                        ->where('nome', 'like', "%{$filtros['busca']}%")
                        ->orWhere('descricao', 'like', "%{$filtros['busca']}%")
                        ->orWhereHas('marca', fn (Builder $marcaQuery) => $marcaQuery->where('nome', 'like', "%{$filtros['busca']}%"));
                });
            })
            ->when($filtros['categoriaSlug'] !== '', fn (Builder $query) => $query->whereHas('categoria', fn (Builder $categoriaQuery) => $categoriaQuery->where('slug', $filtros['categoriaSlug'])))
            ->when($filtros['cidade'] !== '', fn (Builder $query) => $query->whereHas('precos.loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $filtros['cidade'])))
            ->when($filtros['tipoPreco'] !== '', fn (Builder $query) => $query->whereHas('precos', fn (Builder $precoQuery) => $precoQuery->where('tipo_preco', $filtros['tipoPreco'])))
            ->when($filtros['precoAte'] !== null, fn (Builder $query) => $query->whereHas('precos', fn (Builder $precoQuery) => $precoQuery->where('preco', '<=', $filtros['precoAte'])));
    }

    private function ordenarProdutos(Builder $query, string $ordenar): Builder
    {
        return $query
            ->when($ordenar === 'maior_economia', fn (Builder $query) => $query->orderByRaw('(COALESCE(maior_preco, 0) - COALESCE(menor_preco, 0)) desc'))
            ->when($ordenar === 'mais_ofertas', fn (Builder $query) => $query->orderByDesc('precos_count'))
            ->when($ordenar === 'alfabetica', fn (Builder $query) => $query->orderBy('nome'))
            ->when(! in_array($ordenar, ['maior_economia', 'mais_ofertas', 'alfabetica'], true), fn (Builder $query) => $query->orderBy('menor_preco'));
    }

    private function carregarMelhoresOfertas(Collection $produtos, array $filtros): void
    {
        $produtos->transform(function (Produto $produto) use ($filtros) {
            $melhoresOfertas = $produto->precos()
                ->with('loja')
                ->when($filtros['cidade'] !== '', fn (Builder $query) => $query->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $filtros['cidade'])))
                ->when($filtros['tipoPreco'] !== '', fn (Builder $query) => $query->where('tipo_preco', $filtros['tipoPreco']))
                ->when($filtros['precoAte'] !== null, fn (Builder $query) => $query->where('preco', '<=', $filtros['precoAte']))
                ->orderBy('preco')
                ->take(3)
                ->get();

            $produto->setRelation('melhores_ofertas', $melhoresOfertas);

            return $produto;
        });
    }

    private function precosFiltrados(array $filtros): Builder
    {
        return Preco::query()
            ->select('precos.*')
            ->join('produtos', 'produtos.id', '=', 'precos.produto_id')
            ->join('lojas', 'lojas.id', '=', 'precos.loja_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->where('produtos.status', 'ativo')
            ->where('lojas.status', 'ativo')
            ->when($filtros['busca'] !== '', function ($query) use ($filtros) {
                $query->where(function ($subquery) use ($filtros) {
                    $subquery
                        ->where('produtos.nome', 'like', "%{$filtros['busca']}%")
                        ->orWhere('produtos.descricao', 'like', "%{$filtros['busca']}%");
                });
            })
            ->when($filtros['categoriaSlug'] !== '', fn ($query) => $query->where('categorias.slug', $filtros['categoriaSlug']))
            ->when($filtros['cidade'] !== '', fn ($query) => $query->where('lojas.cidade', $filtros['cidade']))
            ->when($filtros['tipoPreco'] !== '', fn ($query) => $query->where('precos.tipo_preco', $filtros['tipoPreco']))
            ->when($filtros['precoAte'] !== null, fn ($query) => $query->where('precos.preco', '<=', $filtros['precoAte']));
    }

    private function montarSnapshotMercado(Collection $produtos, Collection $precos, string $ordenar, int $totalResultados): array
    {
        $radarMercado = $this->montarRadarMercado($produtos);

        return [
            'atualizado_em' => now()->format('H:i:s'),
            'total_resultados' => $totalResultados,
            'total_ofertas' => $precos->count(),
            'lojas_ativas' => $precos->pluck('loja_id')->filter()->unique()->count(),
            'faixa_media' => round((float) $produtos->avg(fn (Produto $produto) => ((float) $produto->maior_preco - (float) $produto->menor_preco)), 2),
            'ranking' => $ordenar === 'maior_economia' ? 'economia' : ($ordenar === 'mais_ofertas' ? 'volume' : 'preço'),
            'pulse' => $this->montarPulse($precos),
            'radar_mercado' => $radarMercado,
            'maior_economia' => (float) $radarMercado->max('economia'),
        ];
    }

    private function montarCategoriasChart(Collection $precos): Collection
    {
        return $precos
            ->loadMissing('produto.categoria')
            ->groupBy(fn (Preco $preco) => $preco->produto?->categoria?->nome ?? 'Sem categoria')
            ->map(fn (Collection $grupo, string $nome) => [
                'nome' => $nome,
                'total' => $grupo->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(5);
    }

    private function montarCidadesChart(Collection $precos): Collection
    {
        return $precos
            ->loadMissing('loja')
            ->groupBy(fn (Preco $preco) => $preco->loja?->cidade ?? 'Sem cidade')
            ->map(fn (Collection $grupo, string $nome) => [
                'nome' => $nome,
                'total' => $grupo->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(5);
    }

    private function montarSpreadChart(Collection $produtos): Collection
    {
        return $produtos
            ->map(function (Produto $produto) {
                $menor = (float) ($produto->menor_preco ?? 0);
                $maior = (float) ($produto->maior_preco ?? 0);

                return [
                    'nome' => $produto->nome,
                    'menor' => $menor,
                    'maior' => $maior,
                    'economia' => max(0, $maior - $menor),
                ];
            })
            ->sortByDesc('economia')
            ->values()
            ->take(5);
    }

    private function montarPulse(Collection $precos): array
    {
        $ofertasOrdenadas = $precos
            ->sortBy('preco')
            ->values();

        $pontos = $ofertasOrdenadas
            ->take(12)
            ->values()
            ->map(function (Preco $preco, int $indice) use ($ofertasOrdenadas) {
                $min = max(1, (float) $ofertasOrdenadas->min('preco'));
                $max = max($min, (float) $ofertasOrdenadas->max('preco'));
                $range = max(0.01, $max - $min);
                $x = $indice * 28;
                $y = 92 - ((((float) $preco->preco) - $min) / $range) * 72;

                return [
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                    'valor' => (float) $preco->preco,
                ];
            });

        return [
            'path' => $pontos->map(fn (array $ponto, int $indice) => ($indice === 0 ? 'M' : 'L') . $ponto['x'] . ',' . $ponto['y'])->implode(' '),
            'pontos' => $pontos,
            'menor' => (float) $ofertasOrdenadas->min('preco'),
            'maior' => (float) $ofertasOrdenadas->max('preco'),
        ];
    }

    private function montarRadarMercado(Collection $produtos): Collection
    {
        return $produtos
            ->map(function (Produto $produto) {
                $menor = (float) ($produto->menor_preco ?? 0);
                $maior = (float) ($produto->maior_preco ?? 0);
                $economia = max(0, $maior - $menor);
                $variacao = $maior > 0 ? round(($economia / $maior) * 100, 1) : 0.0;
                $melhorOferta = $produto->getRelationValue('melhores_ofertas')?->first();

                return [
                    'produto' => $produto->nome,
                    'loja' => $melhorOferta?->loja?->nome ?? 'Melhor oferta',
                    'cidade' => $melhorOferta?->loja?->cidade ?? 'cidade aberta',
                    'menor' => $menor,
                    'maior' => $maior,
                    'economia' => $economia,
                    'variacao' => $variacao,
                    'ofertas' => (int) ($produto->precos_count ?? 0),
                    'sinal' => $variacao >= 12 ? 'queda forte' : ($variacao >= 6 ? 'boa janela' : 'estavel'),
                ];
            })
            ->sortByDesc('economia')
            ->values()
            ->take(5);
    }
}
