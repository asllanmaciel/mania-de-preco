<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\Assinatura;
use App\Models\AnalyticsEvent;
use App\Models\ChamadoSuporte;
use App\Models\Conta;
use App\Models\HistoricoPreco;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Produto;
use App\Models\User;
use App\Support\Lancamento\PlatformLaunchReadiness;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, PlatformLaunchReadiness $readiness): View
    {
        return view('super-admin.dashboard', [
            'user' => $request->user(),
            'prontidaoLancamento' => $readiness->analisar(),
            'metricas' => [
                'contas' => Conta::count(),
                'lojas' => Loja::count(),
                'usuarios' => User::count(),
                'produtos' => Produto::count(),
                'alertas' => AlertaPreco::count(),
                'historicos_precos' => HistoricoPreco::count(),
                'planos_ativos' => Plano::where('status', 'ativo')->count(),
                'assinaturas_ativas' => Assinatura::whereIn('status', ['trial', 'ativa', 'inadimplente'])->count(),
                'chamados_abertos' => ChamadoSuporte::whereNotIn('status', ['resolvido', 'fechado'])->count(),
                'eventos_24h' => AnalyticsEvent::where('ocorreu_em', '>=', now()->subDay())->count(),
                'eventos_publicos_7d' => AnalyticsEvent::where('area', 'public')->where('ocorreu_em', '>=', now()->subDays(7))->count(),
                'mrr' => (float) Assinatura::query()
                    ->whereIn('status', ['ativa', 'inadimplente'])
                    ->get()
                    ->sum(function (Assinatura $assinatura) {
                        return $assinatura->ciclo_cobranca === 'anual'
                            ? ((float) $assinatura->valor / 12)
                            : (float) $assinatura->valor;
                    }),
            ],
            'contasRecentes' => Conta::query()->latest('id')->take(6)->get(),
            'usuariosRecentes' => User::query()->latest('id')->take(6)->get(),
            'assinaturasRecentes' => Assinatura::query()
                ->with(['conta', 'plano'])
                ->latest('id')
                ->take(6)
                ->get(),
            'eventosRecentes' => AnalyticsEvent::query()
                ->with(['usuario', 'conta'])
                ->latest('ocorreu_em')
                ->take(8)
                ->get(),
            'eventosPorTipo' => AnalyticsEvent::query()
                ->selectRaw('evento, count(*) as total')
                ->where('ocorreu_em', '>=', now()->subDays(7))
                ->groupBy('evento')
                ->orderByDesc('total')
                ->take(6)
                ->get(),
        ]);
    }
}
