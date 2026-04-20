<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\HistoricoPreco;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PublicProductController extends Controller
{
    public function __invoke(Request $request, Produto $produto): View
    {
        abort_unless($produto->status === 'ativo', 404);

        $produto->load([
            'categoria',
            'marca',
            'precos' => fn ($query) => $query->with('loja')->whereHas('loja', fn ($lojaQuery) => $lojaQuery->where('status', 'ativo'))->orderBy('preco'),
        ]);

        $ofertas = $produto->precos->values();
        $melhorOferta = $ofertas->first();
        $menorPreco = (float) $ofertas->min('preco');
        $maiorPreco = (float) $ofertas->max('preco');
        $economia = max(0, $maiorPreco - $menorPreco);
        $cidades = $ofertas->pluck('loja.cidade')->filter()->unique()->values();
        $tiposPreco = $ofertas->pluck('tipo_preco')->unique()->values();
        $alertaDoUsuario = $request->user()
            ? AlertaPreco::query()
                ->where('user_id', $request->user()->id)
                ->where('produto_id', $produto->id)
                ->first()
            : null;
        $precoSugeridoAlerta = $menorPreco > 0 ? max(0.01, round($menorPreco * 0.95, 2)) : null;
        $historico = $produto->historicoPrecos()
            ->whereNotNull('preco_atual')
            ->orderBy('created_at')
            ->get();
        $historicoVisual = $this->montarHistoricoVisual($historico);
        $primeiroPrecoHistorico = (float) ($historico->first()?->preco_atual ?? $menorPreco);
        $ultimoPrecoHistorico = (float) ($historico->last()?->preco_atual ?? $menorPreco);
        $variacaoHistorica = round($ultimoPrecoHistorico - $primeiroPrecoHistorico, 2);
        $variacaoHistoricaPercentual = $primeiroPrecoHistorico > 0
            ? round(($variacaoHistorica / $primeiroPrecoHistorico) * 100, 1)
            : 0.0;
        $tendencia = $variacaoHistorica < 0 ? 'queda' : ($variacaoHistorica > 0 ? 'alta' : 'estavel');
        $economiaPercentual = $maiorPreco > 0 ? round(($economia / $maiorPreco) * 100, 1) : 0.0;

        $chart = $ofertas->map(function ($oferta) {
            return [
                'loja' => $oferta->loja?->nome ?? 'Loja nao informada',
                'cidade' => $oferta->loja?->cidade ?? 'Sem cidade',
                'preco' => (float) $oferta->preco,
                'tipo_preco' => $oferta->tipo_preco,
                'rota_loja' => $oferta->loja ? route('lojas.public.show', $oferta->loja) : null,
            ];
        });

        $categoriaRelacionados = Produto::query()
            ->where('status', 'ativo')
            ->where('categoria_id', $produto->categoria_id)
            ->where('id', '!=', $produto->id)
            ->withMin('precos as menor_preco', 'preco')
            ->take(4)
            ->get();

        return view('produtos.show', [
            'produto' => $produto,
            'ofertas' => $ofertas,
            'melhorOferta' => $melhorOferta,
            'menorPreco' => $menorPreco,
            'maiorPreco' => $maiorPreco,
            'economia' => $economia,
            'cidades' => $cidades,
            'tiposPreco' => $tiposPreco,
            'chart' => $chart,
            'categoriaRelacionados' => $categoriaRelacionados,
            'alertaDoUsuario' => $alertaDoUsuario,
            'precoSugeridoAlerta' => $precoSugeridoAlerta,
            'historico' => $historico,
            'historicoVisual' => $historicoVisual,
            'variacaoHistorica' => $variacaoHistorica,
            'variacaoHistoricaPercentual' => $variacaoHistoricaPercentual,
            'tendencia' => $tendencia,
            'economiaPercentual' => $economiaPercentual,
        ]);
    }

    private function montarHistoricoVisual(Collection $historico): array
    {
        $pontosBase = $historico
            ->take(-12)
            ->values();

        $pontos = $pontosBase
            ->map(function (HistoricoPreco $registro, int $indice) use ($pontosBase) {
                $min = max(0.01, (float) $pontosBase->min('preco_atual'));
                $max = max($min, (float) $pontosBase->max('preco_atual'));
                $range = max(0.01, $max - $min);
                $step = $pontosBase->count() > 1 ? 320 / ($pontosBase->count() - 1) : 0;
                $x = $pontosBase->count() > 1 ? $indice * $step : 160;
                $y = 92 - ((((float) $registro->preco_atual) - $min) / $range) * 72;

                return [
                    'x' => round($x, 2),
                    'y' => round($y, 2),
                    'preco' => (float) $registro->preco_atual,
                    'evento' => $registro->evento,
                    'data' => optional($registro->created_at)->format('d/m H:i'),
                ];
            });

        $ultimoPonto = $pontos->last();
        $path = $pontos->map(fn (array $ponto, int $indice) => ($indice === 0 ? 'M' : 'L') . $ponto['x'] . ',' . $ponto['y'])->implode(' ');

        return [
            'path' => $path,
            'area' => $path !== '' && $ultimoPonto ? "{$path} L{$ultimoPonto['x']},100 L0,100 Z" : '',
            'pontos' => $pontos,
            'menor' => (float) $pontosBase->min('preco_atual'),
            'maior' => (float) $pontosBase->max('preco_atual'),
        ];
    }
}
