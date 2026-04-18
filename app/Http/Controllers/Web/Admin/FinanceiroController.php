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

        $movimentacoesBase = $conta->movimentacoesFinanceiras();

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

        $movimentacoesRecentes = $conta->movimentacoesFinanceiras()
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

        $serieMensal = $this->montarSerieMensal($conta);
        $maiorVolumeMensal = max(
            1,
            (float) $serieMensal->max('receitas'),
            (float) $serieMensal->max('despesas')
        );

        $composicaoReceitas = $this->montarComposicao($conta, 'receita');
        $composicaoDespesas = $this->montarComposicao($conta, 'despesa');

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
        ], $conta);
    }

    private function montarSerieMensal($conta): Collection
    {
        return collect(range(5, 0))->map(function ($offset) use ($conta) {
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

    private function montarComposicao($conta, string $tipo): Collection
    {
        return $conta->movimentacoesFinanceiras()
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
}
