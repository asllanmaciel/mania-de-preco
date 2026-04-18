<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicStoreController extends Controller
{
    public function __invoke(Loja $loja): View
    {
        abort_unless($loja->status === 'ativo', 404);

        $loja->loadCount('precos');
        $loja->loadAvg('avaliacoes', 'nota');
        $loja->load([
            'avaliacoes.user',
            'precos' => fn ($query) => $query->with(['produto.categoria', 'produto.marca'])->orderBy('preco'),
        ]);

        $ofertas = $loja->precos
            ->groupBy('produto_id')
            ->map(function (Collection $grupo) {
                $primeiro = $grupo->first();
                $produto = $primeiro?->produto;
                $menor = (float) $grupo->min('preco');
                $maior = (float) $grupo->max('preco');

                return [
                    'produto' => $produto,
                    'menor_preco' => $menor,
                    'maior_preco' => $maior,
                    'variacao' => max(0, $maior - $menor),
                    'tipos' => $grupo->pluck('tipo_preco')->unique()->values(),
                ];
            })
            ->sortBy('menor_preco')
            ->values();

        $categoriaChart = $ofertas
            ->groupBy(fn (array $item) => $item['produto']?->categoria?->nome ?? 'Sem categoria')
            ->map(fn (Collection $grupo, string $nome) => [
                'nome' => $nome,
                'total' => $grupo->count(),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(5);

        $precoMedio = round((float) $loja->precos->avg('preco'), 2);
        $avaliacaoMedia = round((float) ($loja->avaliacoes_avg_nota ?? 0), 1);
        $avaliacoesRecentes = $loja->avaliacoes->sortByDesc('id')->take(3)->values();
        $produtosDestaque = $ofertas->take(6);

        return view('lojas.show', [
            'loja' => $loja,
            'ofertas' => $ofertas,
            'produtosDestaque' => $produtosDestaque,
            'categoriaChart' => $categoriaChart,
            'precoMedio' => $precoMedio,
            'avaliacaoMedia' => $avaliacaoMedia,
            'avaliacoesRecentes' => $avaliacoesRecentes,
        ]);
    }
}
