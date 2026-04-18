<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Preco;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function __invoke(Request $request): View
    {
        $conta = $request->user()->contas()
            ->wherePivot('ativo', true)
            ->withCount([
                'lojas',
                'categoriasFinanceiras',
                'movimentacoesFinanceiras',
                'contasPagar',
                'contasReceber',
            ])
            ->firstOrFail();

        $assinaturaAtual = $conta->assinaturas()->latest('id')->first();
        $lojas = $conta->lojas()->latest('id')->take(4)->get();
        $ultimasMovimentacoes = $conta->movimentacoesFinanceiras()
            ->with(['categoriaFinanceira', 'loja'])
            ->latest('data_movimentacao')
            ->take(5)
            ->get();

        $movimentacoesBase = $conta->movimentacoesFinanceiras();

        $totalReceitas = (clone $movimentacoesBase)
            ->where('tipo', 'receita')
            ->sum('valor');

        $totalDespesas = (clone $movimentacoesBase)
            ->where('tipo', 'despesa')
            ->sum('valor');

        $totalPrecosMonitorados = Preco::whereIn('loja_id', $conta->lojas()->select('id'))->count();

        $contasPagarPendentes = $conta->contasPagar()
            ->whereNotIn('status', ['pago', 'cancelado'])
            ->count();

        $contasReceberPendentes = $conta->contasReceber()
            ->whereNotIn('status', ['recebido', 'cancelado'])
            ->count();

        return $this->responder($request, 'admin.dashboard', [
            'conta' => $conta,
            'assinaturaAtual' => $assinaturaAtual,
            'lojas' => $lojas,
            'ultimasMovimentacoes' => $ultimasMovimentacoes,
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldoProjetado' => $totalReceitas - $totalDespesas,
            'totalPrecosMonitorados' => $totalPrecosMonitorados,
            'contasPagarPendentes' => $contasPagarPendentes,
            'contasReceberPendentes' => $contasReceberPendentes,
        ], $conta);
    }
}
