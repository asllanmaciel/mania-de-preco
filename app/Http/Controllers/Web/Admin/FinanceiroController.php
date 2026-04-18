<?php

namespace App\Http\Controllers\Web\Admin;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FinanceiroController extends AdminController
{
    public function __invoke(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $periodo = $this->resolverPeriodo((string) $request->string('periodo'));
        $movimentacoesBase = $this->aplicarJanelaMovimentacoes(
            $conta->movimentacoesFinanceiras()->where('status', 'realizada'),
            $periodo
        );

        $totalReceitas = (clone $movimentacoesBase)
            ->where('tipo', 'receita')
            ->sum('valor');

        $totalDespesas = (clone $movimentacoesBase)
            ->where('tipo', 'despesa')
            ->sum('valor');

        $categorias = $conta->categoriasFinanceiras()
            ->orderBy('nome')
            ->take(6)
            ->get();

        $contasFinanceiras = $conta->contasFinanceiras()
            ->with('loja')
            ->latest('id')
            ->take(5)
            ->get();

        $movimentacoesRecentes = $this->aplicarJanelaMovimentacoes(
            $conta->movimentacoesFinanceiras(),
            $periodo
        )
            ->with(['categoriaFinanceira', 'contaFinanceira', 'loja'])
            ->latest('data_movimentacao')
            ->take(6)
            ->get();

        $contasPagarPendentes = $conta->contasPagar()
            ->with(['categoriaFinanceira', 'loja'])
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->orderBy('vencimento')
            ->take(6)
            ->get();

        $contasReceberPendentes = $conta->contasReceber()
            ->with(['categoriaFinanceira', 'loja'])
            ->whereNotIn('status', ['recebida', 'cancelada'])
            ->orderBy('vencimento')
            ->take(6)
            ->get();

        $somaContasFinanceiras = $conta->contasFinanceiras()->sum('saldo_atual');
        $totalPagarAberto = $conta->contasPagar()
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->sum('valor_total');

        $totalReceberAberto = $conta->contasReceber()
            ->whereNotIn('status', ['recebida', 'cancelada'])
            ->sum('valor_total');

        $serieMensal = $this->montarSerieMensal($conta, $periodo);
        $maiorVolumeMensal = max(
            1,
            (float) $serieMensal->max('receitas'),
            (float) $serieMensal->max('despesas')
        );

        $composicaoReceitas = $this->montarComposicao($conta, 'receita', $periodo);
        $composicaoDespesas = $this->montarComposicao($conta, 'despesa', $periodo);

        $maiorReceitaCategoria = max(1, (float) $composicaoReceitas->max('total'));
        $maiorDespesaCategoria = max(1, (float) $composicaoDespesas->max('total'));

        $radarContas = $conta->contasFinanceiras()
            ->with('loja')
            ->get()
            ->sortByDesc('saldo_atual')
            ->values()
            ->take(5)
            ->map(function ($contaFinanceira) {
                return [
                    'nome' => $contaFinanceira->nome,
                    'tipo' => $contaFinanceira->tipo,
                    'saldo_atual' => (float) $contaFinanceira->saldo_atual,
                    'contexto' => $contaFinanceira->loja?->nome ?? ($contaFinanceira->instituicao ?: 'Conta geral'),
                ];
            });

        $maiorSaldoConta = max(1, (float) $radarContas->max('saldo_atual'));

        $proximosEventos = $contasPagarPendentes
            ->map(fn ($titulo) => [
                'tipo' => 'pagar',
                'descricao' => $titulo->descricao,
                'parceiro' => $titulo->fornecedor_nome ?: 'Fornecedor nao informado',
                'vencimento' => $titulo->vencimento,
                'valor' => (float) $titulo->valor_total,
            ])
            ->merge(
                $contasReceberPendentes->map(fn ($titulo) => [
                    'tipo' => 'receber',
                    'descricao' => $titulo->descricao,
                    'parceiro' => $titulo->cliente_nome ?: 'Cliente nao informado',
                    'vencimento' => $titulo->vencimento,
                    'valor' => (float) $titulo->valor_total,
                ])
            )
            ->sortBy(fn ($item) => optional($item['vencimento'])->timestamp ?? PHP_INT_MAX)
            ->values()
            ->take(6);

        $comprometimento = $totalReceitas > 0 ? ($totalDespesas / $totalReceitas) * 100 : 0;
        $coberturaTitulos = $totalPagarAberto > 0 ? ($somaContasFinanceiras / $totalPagarAberto) * 100 : 0;
        $relacaoEntradaSaida = $totalDespesas > 0 ? ($totalReceitas / $totalDespesas) * 100 : 0;
        $titulosComBaixa = $conta->contasPagar()->whereNotNull('movimentacao_financeira_id')->count()
            + $conta->contasReceber()->whereNotNull('movimentacao_financeira_id')->count();

        return $this->responder($request, 'admin.financeiro.index', [
            'categorias' => $categorias,
            'contasFinanceiras' => $contasFinanceiras,
            'movimentacoesRecentes' => $movimentacoesRecentes,
            'contasPagarPendentes' => $contasPagarPendentes,
            'contasReceberPendentes' => $contasReceberPendentes,
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldoProjetado' => $totalReceitas - $totalDespesas,
            'somaContasFinanceiras' => $somaContasFinanceiras,
            'totalPagarAberto' => $totalPagarAberto,
            'totalReceberAberto' => $totalReceberAberto,
            'serieMensal' => $serieMensal,
            'maiorVolumeMensal' => $maiorVolumeMensal,
            'composicaoReceitas' => $composicaoReceitas,
            'composicaoDespesas' => $composicaoDespesas,
            'maiorReceitaCategoria' => $maiorReceitaCategoria,
            'maiorDespesaCategoria' => $maiorDespesaCategoria,
            'radarContas' => $radarContas,
            'maiorSaldoConta' => $maiorSaldoConta,
            'proximosEventos' => $proximosEventos,
            'comprometimento' => $comprometimento,
            'coberturaTitulos' => $coberturaTitulos,
            'relacaoEntradaSaida' => $relacaoEntradaSaida,
            'titulosComBaixa' => $titulosComBaixa,
            'periodoAtual' => $periodo['slug'],
            'periodoLabel' => $periodo['label'],
            'periodosDisponiveis' => $this->periodosDisponiveis(),
        ], $conta);
    }

    private function montarSerieMensal($conta, array $periodo): Collection
    {
        return collect(range($periodo['months'] - 1, 0))->map(function ($offset) use ($conta) {
            $mes = CarbonImmutable::now()->subMonths($offset);
            $inicio = $mes->startOfMonth();
            $fim = $mes->endOfMonth();

            $receitas = $conta->movimentacoesFinanceiras()
                ->whereBetween('data_movimentacao', [$inicio, $fim])
                ->where('tipo', 'receita')
                ->where('status', 'realizada')
                ->sum('valor');

            $despesas = $conta->movimentacoesFinanceiras()
                ->whereBetween('data_movimentacao', [$inicio, $fim])
                ->where('tipo', 'despesa')
                ->where('status', 'realizada')
                ->sum('valor');

            return [
                'label' => $mes->locale('pt_BR')->isoFormat('MMM/YY'),
                'receitas' => (float) $receitas,
                'despesas' => (float) $despesas,
                'saldo' => (float) $receitas - (float) $despesas,
            ];
        });
    }

    private function montarComposicao($conta, string $tipo, array $periodo): Collection
    {
        return $this->aplicarJanelaMovimentacoes(
            $conta->movimentacoesFinanceiras(),
            $periodo
        )
            ->with('categoriaFinanceira')
            ->where('tipo', $tipo)
            ->where('status', 'realizada')
            ->get()
            ->groupBy(fn ($movimentacao) => $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria')
            ->map(function ($grupo) {
                return [
                    'nome' => $grupo->first()->categoriaFinanceira?->nome ?? 'Sem categoria',
                    'total' => (float) $grupo->sum('valor'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(5);
    }

    private function resolverPeriodo(string $slug): array
    {
        return $this->periodosDisponiveis()[$slug] ?? $this->periodosDisponiveis()['6m'];
    }

    private function periodosDisponiveis(): array
    {
        return [
            '3m' => [
                'slug' => '3m',
                'label' => 'ultimos 3 meses',
                'months' => 3,
                'start' => CarbonImmutable::now()->startOfMonth()->subMonths(2),
                'end' => CarbonImmutable::now()->endOfMonth(),
            ],
            '6m' => [
                'slug' => '6m',
                'label' => 'ultimos 6 meses',
                'months' => 6,
                'start' => CarbonImmutable::now()->startOfMonth()->subMonths(5),
                'end' => CarbonImmutable::now()->endOfMonth(),
            ],
            '12m' => [
                'slug' => '12m',
                'label' => 'ultimos 12 meses',
                'months' => 12,
                'start' => CarbonImmutable::now()->startOfMonth()->subMonths(11),
                'end' => CarbonImmutable::now()->endOfMonth(),
            ],
            '24m' => [
                'slug' => '24m',
                'label' => 'ultimos 24 meses',
                'months' => 24,
                'start' => CarbonImmutable::now()->startOfMonth()->subMonths(23),
                'end' => CarbonImmutable::now()->endOfMonth(),
            ],
        ];
    }

    private function aplicarJanelaMovimentacoes($query, array $periodo)
    {
        return $query->whereBetween('data_movimentacao', [
            $periodo['start'],
            $periodo['end'],
        ]);
    }
}
