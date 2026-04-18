<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
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
            ->whereNotIn('status', ['pago', 'cancelado'])
            ->orderBy('vencimento')
            ->take(6)
            ->get();

        $contasReceberPendentes = $conta->contasReceber()
            ->with(['categoriaFinanceira', 'loja'])
            ->whereNotIn('status', ['recebido', 'cancelado'])
            ->orderBy('vencimento')
            ->take(6)
            ->get();

        $somaContasFinanceiras = $conta->contasFinanceiras()->sum('saldo_atual');
        $totalPagarAberto = $conta->contasPagar()
            ->whereNotIn('status', ['pago', 'cancelado'])
            ->sum('valor_total');

        $totalReceberAberto = $conta->contasReceber()
            ->whereNotIn('status', ['recebido', 'cancelado'])
            ->sum('valor_total');

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
        ], $conta);
    }
}
