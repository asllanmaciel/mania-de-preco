<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Loja;
use App\Support\Analytics\ProductAnalytics;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Illuminate\Http\Request;

class PublicStoreController extends Controller
{
    public function __invoke(Request $request, Loja $loja, ProductAnalytics $analytics): View
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
                $precos = $grupo
                    ->sortBy('preco')
                    ->values()
                    ->map(fn ($preco) => [
                        'valor' => (float) $preco->preco,
                        'tipo' => $preco->tipo_preco,
                    ]);
                $faixaPercentual = $menor > 0 ? round((($maior - $menor) / $menor) * 100, 1) : 0;

                return [
                    'produto' => $produto,
                    'menor_preco' => $menor,
                    'maior_preco' => $maior,
                    'variacao' => max(0, $maior - $menor),
                    'faixa_percentual' => max(0, $faixaPercentual),
                    'quantidade_precos' => $grupo->count(),
                    'tipos' => $grupo->pluck('tipo_preco')->unique()->values(),
                    'precos' => $precos,
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
        $catalogoPublicado = $loja->precos_count > 0;
        $lojasRecomendadas = Loja::query()
            ->where('status', 'ativo')
            ->where('id', '!=', $loja->id)
            ->whereHas('precos')
            ->withCount('precos')
            ->orderByDesc('precos_count')
            ->take(4)
            ->get();

        $analytics->track($request, 'public.store.viewed', 'public', [
            'loja' => $loja->nome,
            'cidade' => $loja->cidade,
            'uf' => $loja->uf,
            'ofertas' => $ofertas->count(),
            'precos' => $loja->precos_count,
            'preco_medio' => $precoMedio,
            'avaliacao_media' => $avaliacaoMedia,
        ], $loja);

        return view('lojas.show', [
            'loja' => $loja,
            'ofertas' => $ofertas,
            'produtosDestaque' => $produtosDestaque,
            'categoriaChart' => $categoriaChart,
            'precoMedio' => $precoMedio,
            'avaliacaoMedia' => $avaliacaoMedia,
            'avaliacoesRecentes' => $avaliacoesRecentes,
            'catalogoPublicado' => $catalogoPublicado,
            'lojasRecomendadas' => $lojasRecomendadas,
        ]);
    }
}
