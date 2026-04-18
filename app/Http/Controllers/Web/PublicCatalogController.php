<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $busca = trim((string) $request->string('busca'));
        $categoriaSlug = trim((string) $request->string('categoria'));
        $cidade = trim((string) $request->string('cidade'));
        $ordenar = trim((string) $request->string('ordenar', 'menor_preco'));

        $produtosBase = Produto::query()
            ->where('status', 'ativo')
            ->with(['categoria', 'marca'])
            ->withCount('precos')
            ->withMin('precos as menor_preco', 'preco')
            ->withMax('precos as maior_preco', 'preco')
            ->whereHas('precos.loja', fn (Builder $query) => $query->where('status', 'ativo'))
            ->when($busca !== '', function (Builder $query) use ($busca) {
                $query->where(function (Builder $subquery) use ($busca) {
                    $subquery
                        ->where('nome', 'like', "%{$busca}%")
                        ->orWhere('descricao', 'like', "%{$busca}%")
                        ->orWhereHas('marca', fn (Builder $marcaQuery) => $marcaQuery->where('nome', 'like', "%{$busca}%"));
                });
            })
            ->when($categoriaSlug !== '', fn (Builder $query) => $query->whereHas('categoria', fn (Builder $categoriaQuery) => $categoriaQuery->where('slug', $categoriaSlug)))
            ->when($cidade !== '', fn (Builder $query) => $query->whereHas('precos.loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $cidade)));

        $produtos = (clone $produtosBase)
            ->when($ordenar === 'maior_economia', fn (Builder $query) => $query->orderByRaw('(COALESCE(maior_preco, 0) - COALESCE(menor_preco, 0)) desc'))
            ->when($ordenar === 'mais_ofertas', fn (Builder $query) => $query->orderByDesc('precos_count'))
            ->when($ordenar === 'alfabetica', fn (Builder $query) => $query->orderBy('nome'))
            ->when(! in_array($ordenar, ['maior_economia', 'mais_ofertas', 'alfabetica'], true), fn (Builder $query) => $query->orderBy('menor_preco'))
            ->paginate(9)
            ->withQueryString();

        $produtos->getCollection()->transform(function (Produto $produto) use ($cidade) {
            $melhoresOfertas = $produto->precos()
                ->with('loja')
                ->when($cidade !== '', fn (Builder $query) => $query->whereHas('loja', fn (Builder $lojaQuery) => $lojaQuery->where('cidade', $cidade)))
                ->orderBy('preco')
                ->take(3)
                ->get();

            $produto->setRelation('melhores_ofertas', $melhoresOfertas);

            return $produto;
        });

        $precosFiltrados = Preco::query()
            ->select('precos.*')
            ->join('produtos', 'produtos.id', '=', 'precos.produto_id')
            ->join('lojas', 'lojas.id', '=', 'precos.loja_id')
            ->leftJoin('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery
                        ->where('produtos.nome', 'like', "%{$busca}%")
                        ->orWhere('produtos.descricao', 'like', "%{$busca}%");
                });
            })
            ->when($categoriaSlug !== '', fn ($query) => $query->where('categorias.slug', $categoriaSlug))
            ->when($cidade !== '', fn ($query) => $query->where('lojas.cidade', $cidade))
            ->get();

        $categorias = Categoria::query()
            ->orderBy('nome')
            ->get();

        $cidades = Loja::query()
            ->where('status', 'ativo')
            ->whereNotNull('cidade')
            ->distinct()
            ->orderBy('cidade')
            ->pluck('cidade');

        $totalResultados = $produtos->total();
        $totalOfertas = $precosFiltrados->count();
        $lojasAtivas = $precosFiltrados->pluck('loja_id')->filter()->unique()->count();
        $faixaMedia = round((float) $produtos->getCollection()->avg(fn (Produto $produto) => ((float) $produto->maior_preco - (float) $produto->menor_preco)), 2);

        $categoriaChart = $this->montarCategoriasChart($precosFiltrados);
        $cidadeChart = $this->montarCidadesChart($precosFiltrados);
        $spreadChart = $this->montarSpreadChart($produtos->getCollection());
        $pulse = $this->montarPulse($precosFiltrados);

        return view('welcome', [
            'busca' => $busca,
            'categoriaSlug' => $categoriaSlug,
            'cidade' => $cidade,
            'ordenar' => $ordenar,
            'categorias' => $categorias,
            'cidades' => $cidades,
            'produtos' => $produtos,
            'totalResultados' => $totalResultados,
            'totalOfertas' => $totalOfertas,
            'lojasAtivas' => $lojasAtivas,
            'faixaMedia' => $faixaMedia,
            'categoriaChart' => $categoriaChart,
            'cidadeChart' => $cidadeChart,
            'spreadChart' => $spreadChart,
            'pulse' => $pulse,
        ]);
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
}
