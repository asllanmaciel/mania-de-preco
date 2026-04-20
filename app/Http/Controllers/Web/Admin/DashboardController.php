<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Preco;
use App\Models\Produto;
use App\Support\Billing\ContaUsageMeter;
use App\Support\Inteligencia\ContaHealthAnalyzer;
use App\Support\Onboarding\ContaOnboardingChecklist;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends AdminController
{
    public function __invoke(
        Request $request,
        ContaOnboardingChecklist $checklist,
        ContaUsageMeter $usageMeter,
        ContaHealthAnalyzer $healthAnalyzer
    ): View
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

        $saldoProjetado = $totalReceitas - $totalDespesas;

        $lojasIds = $conta->lojas()->pluck('id');

        $totalPrecosMonitorados = Preco::whereIn('loja_id', $lojasIds)->count();
        $totalProdutosCatalogo = Produto::whereHas('precos', fn ($query) => $query->whereIn('loja_id', $lojasIds))->count();

        $contasPagarPendentes = $conta->contasPagar()
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->count();

        $contasReceberPendentes = $conta->contasReceber()
            ->whereNotIn('status', ['recebida', 'cancelada'])
            ->count();

        $titulosCriticos = $conta->contasPagar()
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->whereDate('vencimento', '<=', now()->addDays(7))
            ->count()
            + $conta->contasReceber()
                ->whereNotIn('status', ['recebida', 'cancelada'])
                ->whereDate('vencimento', '<=', now()->addDays(7))
                ->count();

        $somaContasFinanceiras = $conta->contasFinanceiras()->sum('saldo_atual');
        $margemOperacional = $totalReceitas > 0 ? ($saldoProjetado / $totalReceitas) * 100 : 0;
        $coberturaCaixa = $totalDespesas > 0 ? ($somaContasFinanceiras / $totalDespesas) * 100 : 0;

        $serieMensal = $this->montarSerieMensal($conta);
        $maiorVolumeMensal = max(
            1,
            (float) $serieMensal->max('receitas'),
            (float) $serieMensal->max('despesas')
        );

        $rankingLojas = $conta->lojas()
            ->withCount(['precos', 'movimentacoesFinanceiras'])
            ->get()
            ->map(function ($loja) {
                return [
                    'nome' => $loja->nome,
                    'local' => trim(($loja->cidade ?: 'Cidade nao informada') . ($loja->uf ? ' / ' . $loja->uf : '')),
                    'precos_count' => $loja->precos_count,
                    'movimentacoes_count' => $loja->movimentacoes_financeiras_count,
                ];
            })
            ->sortByDesc('precos_count')
            ->values()
            ->take(4);

        $composicaoCategorias = $conta->movimentacoesFinanceiras()
            ->with('categoriaFinanceira')
            ->whereIn('tipo', ['receita', 'despesa'])
            ->where('status', 'realizada')
            ->get()
            ->groupBy(fn ($movimentacao) => $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria')
            ->map(function ($grupo) {
                return [
                    'nome' => $grupo->first()->categoriaFinanceira?->nome ?? 'Sem categoria',
                    'tipo' => $grupo->first()->tipo,
                    'total' => (float) $grupo->sum('valor'),
                ];
            })
            ->sortByDesc('total')
            ->values()
            ->take(5);

        $maiorCategoria = max(1, (float) $composicaoCategorias->max('total'));
        $onboarding = $checklist->build($conta, $request->user()->capacidadesNaConta($conta));
        $usoPlano = $usageMeter->resumo($conta);
        $saudeConta = $healthAnalyzer->analisar($conta);
        $capacidadesConta = $request->user()->capacidadesNaConta($conta);
        $planoDoDia = $this->montarPlanoDoDia(
            $onboarding,
            $saudeConta,
            $capacidadesConta,
            $titulosCriticos,
            $totalPrecosMonitorados,
            $totalProdutosCatalogo,
            $contasReceberPendentes
        );

        return $this->responder($request, 'admin.dashboard', [
            'conta' => $conta,
            'assinaturaAtual' => $assinaturaAtual,
            'lojas' => $lojas,
            'ultimasMovimentacoes' => $ultimasMovimentacoes,
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldoProjetado' => $saldoProjetado,
            'totalPrecosMonitorados' => $totalPrecosMonitorados,
            'totalProdutosCatalogo' => $totalProdutosCatalogo,
            'contasPagarPendentes' => $contasPagarPendentes,
            'contasReceberPendentes' => $contasReceberPendentes,
            'titulosCriticos' => $titulosCriticos,
            'margemOperacional' => $margemOperacional,
            'coberturaCaixa' => $coberturaCaixa,
            'serieMensal' => $serieMensal,
            'maiorVolumeMensal' => $maiorVolumeMensal,
            'rankingLojas' => $rankingLojas,
            'composicaoCategorias' => $composicaoCategorias,
            'maiorCategoria' => $maiorCategoria,
            'onboarding' => $onboarding,
            'usoPlano' => $usoPlano,
            'saudeConta' => $saudeConta,
            'planoDoDia' => $planoDoDia,
        ], $conta);
    }

    private function montarPlanoDoDia(
        array $onboarding,
        array $saudeConta,
        array $capacidadesConta,
        int $titulosCriticos,
        int $totalPrecosMonitorados,
        int $totalProdutosCatalogo,
        int $contasReceberPendentes
    ): Collection {
        $acoes = collect();

        if ($onboarding['percentual'] < 100 && in_array('onboarding', $capacidadesConta, true)) {
            $acoes->push([
                'impacto' => 'lancamento',
                'titulo' => 'Concluir a proxima etapa de prontidao',
                'descricao' => $onboarding['proxima_etapa']['titulo'] ?? 'Finalize os pontos pendentes para deixar a conta pronta para apresentacao.',
                'acao' => 'Abrir onboarding',
                'rota' => route('admin.onboarding'),
                'prioridade' => 100 - $onboarding['percentual'],
            ]);
        }

        if ($titulosCriticos > 0 && in_array('financeiro', $capacidadesConta, true)) {
            $acoes->push([
                'impacto' => 'caixa',
                'titulo' => "{$titulosCriticos} titulos pedem atencao esta semana",
                'descricao' => 'Resolva vencimentos proximos para proteger previsao de caixa e reduzir risco operacional.',
                'acao' => 'Abrir financeiro',
                'rota' => route('admin.financeiro.index'),
                'prioridade' => 90 + $titulosCriticos,
            ]);
        }

        if (($totalPrecosMonitorados < 12 || $totalProdutosCatalogo < 8) && in_array('precos', $capacidadesConta, true)) {
            $acoes->push([
                'impacto' => 'vitrine',
                'titulo' => 'Fortalecer vitrine de precos',
                'descricao' => 'Mais produtos com precos ativos melhoram o comparador publico e deixam o radar mais convincente.',
                'acao' => 'Revisar precos',
                'rota' => route('admin.precos.index'),
                'prioridade' => 82,
            ]);
        }

        if ($contasReceberPendentes > 0 && in_array('financeiro', $capacidadesConta, true)) {
            $acoes->push([
                'impacto' => 'recebimento',
                'titulo' => 'Acompanhar recebimentos em aberto',
                'descricao' => 'Entradas pendentes ajudam a entender capital disponivel e previsao para a operacao.',
                'acao' => 'Ver contas a receber',
                'rota' => route('admin.financeiro.contas-receber.index'),
                'prioridade' => 76,
            ]);
        }

        $proximaAcao = $saudeConta['proxima_acao'] ?? null;
        $rotaSaude = $proximaAcao['rota'] ?? null;
        $capacidadeSaude = [
            'admin.assinatura' => 'gestao',
            'admin.configuracoes.edit' => 'gestao',
            'admin.financeiro.index' => 'financeiro',
            'admin.precos.index' => 'precos',
            'admin.equipe.index' => 'equipe',
        ][$rotaSaude] ?? null;

        if ($proximaAcao && $rotaSaude && (! $capacidadeSaude || in_array($capacidadeSaude, $capacidadesConta, true))) {
            $acoes->push([
                'impacto' => 'saude',
                'titulo' => $proximaAcao['titulo'],
                'descricao' => $proximaAcao['descricao'],
                'acao' => $proximaAcao['acao'] ?? 'Resolver agora',
                'rota' => route($rotaSaude),
                'prioridade' => 72,
            ]);
        }

        if ($acoes->isEmpty()) {
            $acoes->push([
                'impacto' => 'crescimento',
                'titulo' => 'Conta pronta para demonstracao',
                'descricao' => 'A base principal esta saudavel. Use o centro de lancamento para acompanhar detalhes finos antes de apresentar.',
                'acao' => 'Ver prontidao',
                'rota' => route('admin.lancamento'),
                'prioridade' => 50,
            ]);
        }

        return $acoes
            ->sortByDesc('prioridade')
            ->values()
            ->take(3);
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
}
