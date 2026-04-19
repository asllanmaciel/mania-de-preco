<?php

namespace App\Support\Notificacoes;

use App\Models\AlertaPreco;
use App\Models\ChamadoSuporte;
use App\Models\Conta;
use App\Models\NotificacaoInteracao;
use App\Models\User;
use App\Support\Onboarding\ContaOnboardingChecklist;
use Illuminate\Support\Collection;

class CentralNotificacoes
{
    public function __construct(private readonly ContaOnboardingChecklist $onboardingChecklist)
    {
    }

    public function admin(Conta $conta, User $user, array $capacidades = []): Collection
    {
        $notificacoes = collect();
        $assinatura = $conta->assinaturas()->latest('id')->first();

        if (! $assinatura) {
            $notificacoes->push($this->item(
                'Assinatura pendente',
                'Defina um plano para liberar uma leitura comercial mais segura da conta.',
                'risco',
                'credit-card',
                $this->rotaPermitida($capacidades, 'gestao', 'admin.assinatura'),
                'Revisar assinatura',
                'assinatura',
                'admin.assinatura.pendente'
            ));
        } elseif (in_array($assinatura->status, ['inadimplente', 'cancelada'], true)) {
            $notificacoes->push($this->item(
                'Assinatura exige atencao',
                'O status atual pode afetar limites, cobranca ou continuidade da operacao.',
                'risco',
                'alert',
                $this->rotaPermitida($capacidades, 'gestao', 'admin.assinatura'),
                'Abrir assinatura',
                'assinatura',
                'admin.assinatura.risco'
            ));
        } elseif ($assinatura->expira_em && $assinatura->expira_em->isBefore(now()->addDays(7))) {
            $notificacoes->push($this->item(
                'Vigencia perto do fim',
                'A assinatura vence em ate sete dias. Antecipe a renovacao para evitar atrito.',
                'alerta',
                'credit-card',
                $this->rotaPermitida($capacidades, 'gestao', 'admin.assinatura'),
                'Ver cobranca',
                'assinatura',
                'admin.assinatura.vigencia'
            ));
        }

        if ($conta->trial_ends_at && $conta->trial_ends_at->isFuture() && $conta->trial_ends_at->isBefore(now()->addDays(5))) {
            $notificacoes->push($this->item(
                'Trial quase terminando',
                'A conta esta perto do fim do periodo de teste. Converta antes que a rotina esfrie.',
                'alerta',
                'spark',
                $this->rotaPermitida($capacidades, 'gestao', 'admin.assinatura'),
                'Converter conta',
                'assinatura',
                'admin.trial.proximo_fim'
            ));
        }

        $titulosPagarCriticos = $conta->contasPagar()
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->whereDate('vencimento', '<=', now()->addDays(7))
            ->count();

        if ($titulosPagarCriticos > 0) {
            $notificacoes->push($this->item(
                "{$titulosPagarCriticos} conta(s) a pagar no radar",
                'Existem compromissos vencidos ou vencendo em ate sete dias.',
                'risco',
                'wallet',
                $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.contas-pagar.index'),
                'Abrir contas a pagar',
                'financeiro',
                'admin.financeiro.contas_pagar_criticas'
            ));
        }

        $titulosReceberCriticos = $conta->contasReceber()
            ->whereNotIn('status', ['recebida', 'cancelada'])
            ->whereDate('vencimento', '<=', now()->addDays(7))
            ->count();

        if ($titulosReceberCriticos > 0) {
            $notificacoes->push($this->item(
                "{$titulosReceberCriticos} recebimento(s) precisam de acompanhamento",
                'Entradas vencidas ou muito proximas do vencimento merecem cobranca ativa.',
                'alerta',
                'trend',
                $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.contas-receber.index'),
                'Abrir contas a receber',
                'financeiro',
                'admin.financeiro.contas_receber_criticas'
            ));
        }

        $onboarding = $this->onboardingChecklist->build($conta, $capacidades);

        if ($onboarding['percentual'] < 100 && $onboarding['proxima_etapa']) {
            $notificacoes->push($this->item(
                'Proxima etapa do setup',
                $onboarding['proxima_etapa']['titulo'] . ': ' . $onboarding['proxima_etapa']['descricao'],
                'info',
                'compass',
                $onboarding['proxima_etapa']['rota'],
                $onboarding['proxima_etapa']['cta'],
                'onboarding',
                'admin.onboarding.' . $onboarding['proxima_etapa']['codigo']
            ));
        }

        $lojasIds = $conta->lojas()->pluck('id');
        $precosPublicados = $lojasIds->isEmpty()
            ? 0
            : \App\Models\Preco::query()->whereIn('loja_id', $lojasIds)->count();

        if ($conta->lojas()->count() > 0 && $precosPublicados === 0) {
            $notificacoes->push($this->item(
                'Vitrine sem precos publicados',
                'As lojas ja existem, mas o comparador ainda precisa de ofertas para gerar valor publico.',
                'alerta',
                'tag',
                $this->rotaPermitida($capacidades, 'precos', 'admin.precos.create'),
                'Publicar preco',
                'precos',
                'admin.precos.sem_publicacao'
            ));
        }

        $chamadosAbertos = $conta->chamadosSuporte()
            ->whereNotIn('status', ['resolvido', 'fechado'])
            ->count();

        if ($chamadosAbertos > 0) {
            $notificacoes->push($this->item(
                "{$chamadosAbertos} chamado(s) de suporte em aberto",
                'Acompanhe retornos pendentes para proteger a experiencia da conta.',
                'info',
                'bell',
                route('suporte'),
                'Ver suporte',
                'suporte',
                'admin.suporte.chamados_abertos'
            ));
        }

        if ($notificacoes->isEmpty()) {
            $notificacoes->push($this->item(
                'Operacao sem alertas criticos',
                'Continue acompanhando catalogo, financeiro, equipe e precos pela rotina do painel.',
                'sucesso',
                'check',
                route('admin.dashboard'),
                'Ver dashboard',
                'operacao',
                'admin.operacao.sem_alertas'
            ));
        }

        return $this->comEstado($this->ordenar($notificacoes), $user, 'admin', 'conta:' . $conta->id, $conta->id);
    }

    public function cliente(User $user): Collection
    {
        $notificacoes = collect();

        $alertasAtendidos = AlertaPreco::query()
            ->with(['produto', 'lojaReferencia'])
            ->where('user_id', $user->id)
            ->where('status', 'atendido')
            ->latest('disparado_em')
            ->take(4)
            ->get();

        foreach ($alertasAtendidos as $alerta) {
            $notificacoes->push($this->item(
                'Preco bateu sua meta',
                ($alerta->produto?->nome ?? 'Produto monitorado') . ' chegou a R$ ' . number_format((float) $alerta->ultimo_preco_menor, 2, ',', '.') . ' em ' . ($alerta->lojaReferencia?->nome ?? 'uma loja ativa') . '.',
                'sucesso',
                'check',
                $alerta->produto ? route('produtos.public.show', $alerta->produto) : route('cliente.dashboard'),
                'Ver oferta',
                'alertas',
                'cliente.alerta_atendido.' . $alerta->id
            ));
        }

        $alertasAtivos = AlertaPreco::query()
            ->where('user_id', $user->id)
            ->where('status', 'ativo')
            ->count();

        if ($alertasAtivos > 0) {
            $notificacoes->push($this->item(
                "{$alertasAtivos} alerta(s) monitorando o mercado",
                'Seu radar segue acompanhando produtos ate que uma loja bata o valor desejado.',
                'info',
                'search',
                route('cliente.dashboard'),
                'Ver meus alertas',
                'alertas',
                'cliente.alertas_ativos'
            ));
        }

        if ($alertasAtivos === 0 && $alertasAtendidos->isEmpty()) {
            $notificacoes->push($this->item(
                'Crie seu primeiro alerta',
                'Escolha um produto recorrente e defina quanto voce quer pagar.',
                'alerta',
                'bell',
                route('cliente.dashboard'),
                'Criar alerta',
                'alertas',
                'cliente.primeiro_alerta'
            ));
        }

        return $this->comEstado($this->ordenar($notificacoes), $user, 'cliente', 'cliente');
    }

    public function superAdmin(User $user): Collection
    {
        $notificacoes = collect();

        if (! $user->ehSuperAdmin()) {
            return $notificacoes;
        }

        $criticos = ChamadoSuporte::query()
            ->where('prioridade', 'critica')
            ->whereNotIn('status', ['resolvido', 'fechado'])
            ->count();

        if ($criticos > 0) {
            $notificacoes->push($this->item(
                "{$criticos} chamado(s) critico(s)",
                'A fila de suporte tem prioridade critica aguardando analise.',
                'risco',
                'alert',
                route('super-admin.suporte.index'),
                'Abrir suporte',
                'suporte',
                'super-admin.suporte.criticos'
            ));
        }

        if ($notificacoes->isEmpty()) {
            $notificacoes->push($this->item(
                'Backoffice sem alertas criticos',
                'Contas, planos e suporte seguem disponiveis para acompanhamento.',
                'sucesso',
                'check',
                route('super-admin.dashboard'),
                'Ver visao geral',
                'governanca',
                'super-admin.sem_alertas'
            ));
        }

        return $this->comEstado($this->ordenar($notificacoes), $user, 'super-admin', 'global');
    }

    public function interagir(User $user, string $contexto, string $escopo, string $chave, string $acao, ?int $contaId = null): NotificacaoInteracao
    {
        $dados = [
            'lida_em' => now(),
            'dispensada_ate' => $acao === 'dispensar' ? now()->addDay() : null,
        ];

        return NotificacaoInteracao::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'contexto' => $contexto,
                'escopo' => $escopo,
                'chave' => $chave,
            ],
            $dados + ['conta_id' => $contaId]
        );
    }

    private function item(string $titulo, string $descricao, string $tipo, string $icone, ?string $rota, string $acao, string $area, string $chave): array
    {
        return compact('titulo', 'descricao', 'tipo', 'icone', 'rota', 'acao', 'area', 'chave');
    }

    private function rotaPermitida(array $capacidades, string $capacidade, string $rota): ?string
    {
        if ($capacidades !== [] && ! in_array($capacidade, $capacidades, true)) {
            return null;
        }

        return route($rota);
    }

    private function ordenar(Collection $notificacoes): Collection
    {
        $peso = [
            'risco' => 0,
            'alerta' => 1,
            'info' => 2,
            'sucesso' => 3,
        ];

        return $notificacoes
            ->sortBy(fn (array $item) => $peso[$item['tipo']] ?? 9)
            ->values();
    }

    private function comEstado(Collection $notificacoes, User $user, string $contexto, string $escopo, ?int $contaId = null): Collection
    {
        $interacoes = NotificacaoInteracao::query()
            ->where('user_id', $user->id)
            ->where('contexto', $contexto)
            ->where('escopo', $escopo)
            ->whereIn('chave', $notificacoes->pluck('chave')->all())
            ->get()
            ->keyBy('chave');

        return $notificacoes->map(function (array $item) use ($interacoes, $contexto, $escopo, $contaId) {
            $interacao = $interacoes->get($item['chave']);
            $dispensadaAte = $interacao?->dispensada_ate;

            return $item + [
                'contexto' => $contexto,
                'escopo' => $escopo,
                'conta_id' => $contaId,
                'lida' => (bool) $interacao?->lida_em,
                'lida_em' => $interacao?->lida_em,
                'dispensada' => $dispensadaAte?->isFuture() ?? false,
                'dispensada_ate' => $dispensadaAte,
            ];
        });
    }
}
