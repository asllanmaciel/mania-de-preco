<?php

namespace App\Support\Inteligencia;

use App\Models\Conta;
use App\Models\Preco;
use App\Models\Produto;
use App\Support\Billing\ContaUsageMeter;

class ContaHealthAnalyzer
{
    public function __construct(private readonly ContaUsageMeter $usageMeter)
    {
    }

    public function analisar(Conta $conta): array
    {
        $usoPlano = $this->usageMeter->resumo($conta);
        $assinatura = $usoPlano['assinatura'];
        $lojasIds = $conta->lojas()->pluck('id');

        $metricas = [
            'usuarios_ativos' => $conta->usuarios()->wherePivot('ativo', true)->count(),
            'lojas' => $conta->lojas()->count(),
            'produtos' => Produto::whereHas('precos', fn ($query) => $query->whereIn('loja_id', $lojasIds))->count(),
            'precos' => Preco::whereIn('loja_id', $lojasIds)->count(),
            'movimentacoes' => $conta->movimentacoesFinanceiras()->count(),
            'receitas' => (float) $conta->movimentacoesFinanceiras()->where('tipo', 'receita')->where('status', 'realizada')->sum('valor'),
            'despesas' => (float) $conta->movimentacoesFinanceiras()->where('tipo', 'despesa')->where('status', 'realizada')->sum('valor'),
            'saldo_contas' => (float) $conta->contasFinanceiras()->sum('saldo_atual'),
            'titulos_criticos' => $this->titulosCriticos($conta),
            'logs_auditoria' => $conta->auditLogs()->count(),
            'configuracao_percentual' => $this->configuracaoPercentual($conta),
        ];

        $metricas['saldo_operacional'] = $metricas['receitas'] - $metricas['despesas'];

        $pilares = [
            'assinatura' => $this->pilarAssinatura($assinatura),
            'operacao' => $this->pilarOperacao($metricas),
            'financeiro' => $this->pilarFinanceiro($metricas),
            'governanca' => $this->pilarGovernanca($metricas, $usoPlano),
        ];

        $score = (int) round(collect($pilares)->avg('score'));
        $sinais = $this->sinais($metricas, $usoPlano, $assinatura);

        return [
            'score' => $score,
            'nivel' => $this->nivel($score),
            'pilares' => $pilares,
            'metricas' => $metricas,
            'sinais' => $sinais,
            'proxima_acao' => collect($sinais)->firstWhere('prioridade', 'alta')
                ?? collect($sinais)->firstWhere('prioridade', 'media')
                ?? $sinais[0]
                ?? null,
        ];
    }

    private function pilarAssinatura($assinatura): array
    {
        $score = match ($assinatura?->status) {
            'ativa' => 100,
            'trial' => 82,
            'inadimplente' => 38,
            'cancelada', 'encerrada' => 15,
            default => 45,
        };

        if ($assinatura?->expira_em && $assinatura->expira_em->isBefore(now()->addDays(7))) {
            $score = min($score, 65);
        }

        return [
            'nome' => 'Assinatura',
            'score' => $score,
            'descricao' => $score >= 80 ? 'Plano em boa condicao comercial.' : 'Conta pede atencao comercial.',
        ];
    }

    private function pilarOperacao(array $metricas): array
    {
        $score = 0;
        $score += $metricas['lojas'] > 0 ? 25 : 0;
        $score += $metricas['produtos'] > 0 ? 25 : 0;
        $score += $metricas['precos'] >= max(1, $metricas['produtos']) ? 25 : 0;
        $score += $metricas['usuarios_ativos'] > 1 ? 25 : 15;

        return [
            'nome' => 'Operacao',
            'score' => min(100, $score),
            'descricao' => $score >= 80 ? 'Base operacional consistente.' : 'Estrutura operacional ainda pode amadurecer.',
        ];
    }

    private function pilarFinanceiro(array $metricas): array
    {
        $score = 0;
        $score += $metricas['movimentacoes'] > 0 ? 30 : 0;
        $score += $metricas['receitas'] > 0 ? 25 : 0;
        $score += $metricas['saldo_operacional'] >= 0 ? 25 : 5;
        $score += $metricas['titulos_criticos'] === 0 ? 20 : max(0, 20 - ($metricas['titulos_criticos'] * 5));

        return [
            'nome' => 'Financeiro',
            'score' => min(100, $score),
            'descricao' => $score >= 80 ? 'Leitura financeira saudavel.' : 'Financeiro precisa de acompanhamento mais proximo.',
        ];
    }

    private function pilarGovernanca(array $metricas, array $usoPlano): array
    {
        $score = 0;
        $score += $metricas['usuarios_ativos'] > 1 ? 25 : 10;
        $score += $metricas['logs_auditoria'] > 0 ? 25 : 0;
        $score += $metricas['configuracao_percentual'] >= 80 ? 15 : 5;

        foreach ($usoPlano['metricas'] as $metrica) {
            if ($metrica['ilimitado']) {
                $score += 15;
                continue;
            }

            $score += $metrica['excedido'] ? 0 : ($metrica['em_alerta'] ? 10 : 15);
        }

        return [
            'nome' => 'Governanca',
            'score' => min(100, $score),
            'descricao' => $score >= 80 ? 'Conta bem controlada para escala.' : 'Ha sinais de governanca para reforcar.',
        ];
    }

    private function sinais(array $metricas, array $usoPlano, $assinatura): array
    {
        $sinais = [];

        if (! in_array($assinatura?->status, ['ativa', 'trial'], true)) {
            $sinais[] = [
                'tipo' => 'risco',
                'prioridade' => 'alta',
                'titulo' => 'Assinatura pede acao comercial',
                'descricao' => 'A conta nao esta em status ativo ou trial, o que pode interromper expansao e uso continuo.',
                'acao' => 'Revisar assinatura',
            ];
        }

        foreach ($usoPlano['metricas'] as $metrica) {
            if ($metrica['excedido'] || $metrica['em_alerta']) {
                $sinais[] = [
                    'tipo' => $metrica['excedido'] ? 'risco' : 'alerta',
                    'prioridade' => $metrica['excedido'] ? 'alta' : 'media',
                    'titulo' => ucfirst($metrica['rotulo']) . ' perto do limite',
                    'descricao' => $metrica['excedido']
                        ? 'A conta ja consumiu todo o limite contratado para este recurso.'
                        : 'O uso passou de 80% do limite contratado e pode pedir upgrade em breve.',
                    'acao' => 'Avaliar upgrade do plano',
                ];
            }
        }

        if ($metricas['titulos_criticos'] > 0) {
            $sinais[] = [
                'tipo' => 'alerta',
                'prioridade' => 'media',
                'titulo' => 'Titulos exigem acompanhamento',
                'descricao' => "{$metricas['titulos_criticos']} titulo(s) vencem em ate sete dias ou estao em aberto no curto prazo.",
                'acao' => 'Abrir financeiro',
                'rota' => 'admin.financeiro.index',
            ];
        }

        if ($metricas['configuracao_percentual'] < 80) {
            $sinais[] = [
                'tipo' => 'oportunidade',
                'prioridade' => 'media',
                'titulo' => 'Dados da empresa incompletos',
                'descricao' => 'Completar identidade, contato e operacao melhora suporte, cobranca e leitura de maturidade da conta.',
                'acao' => 'Abrir configuracoes',
                'rota' => 'admin.configuracoes.edit',
            ];
        }

        if ($metricas['precos'] === 0 && $metricas['lojas'] > 0) {
            $sinais[] = [
                'tipo' => 'alerta',
                'prioridade' => 'media',
                'titulo' => 'Comparador ainda sem precos',
                'descricao' => 'As lojas existem, mas ainda nao alimentam o comparador publico com ofertas.',
                'acao' => 'Cadastrar precos',
                'rota' => 'admin.precos.index',
            ];
        }

        if ($metricas['usuarios_ativos'] === 1) {
            $sinais[] = [
                'tipo' => 'oportunidade',
                'prioridade' => 'baixa',
                'titulo' => 'Conta depende de um unico acesso',
                'descricao' => 'Adicionar pelo menos mais um perfil reduz risco operacional e melhora continuidade.',
                'acao' => 'Adicionar membro',
                'rota' => 'admin.equipe.index',
            ];
        }

        if ($sinais === []) {
            $sinais[] = [
                'tipo' => 'positivo',
                'prioridade' => 'baixa',
                'titulo' => 'Conta em boa cadencia',
                'descricao' => 'Os principais sinais estao equilibrados para continuar evoluindo operacao, precos e financeiro.',
                'acao' => 'Manter ritmo',
            ];
        }

        return $sinais;
    }

    private function nivel(int $score): array
    {
        return match (true) {
            $score >= 86 => [
                'nome' => 'Excelente',
                'descricao' => 'Conta madura, com boa base para expansao e uso continuo.',
            ],
            $score >= 70 => [
                'nome' => 'Saudavel',
                'descricao' => 'Conta consistente, com alguns pontos claros de evolucao.',
            ],
            $score >= 50 => [
                'nome' => 'Em desenvolvimento',
                'descricao' => 'Conta em construcao, ainda dependente de acoes operacionais.',
            ],
            default => [
                'nome' => 'Em risco',
                'descricao' => 'Conta precisa de atencao prioritaria para ativacao e retencao.',
            ],
        };
    }

    private function titulosCriticos(Conta $conta): int
    {
        return $conta->contasPagar()
            ->whereNotIn('status', ['paga', 'cancelada'])
            ->whereDate('vencimento', '<=', now()->addDays(7))
            ->count()
            + $conta->contasReceber()
                ->whereNotIn('status', ['recebida', 'cancelada'])
                ->whereDate('vencimento', '<=', now()->addDays(7))
                ->count();
    }

    private function configuracaoPercentual(Conta $conta): int
    {
        $campos = [
            'nome_fantasia',
            'razao_social',
            'documento',
            'email',
            'telefone',
            'segmento',
            'porte',
            'cidade',
            'uf',
            'timezone',
        ];

        $preenchidos = collect($campos)
            ->filter(fn (string $campo) => filled($conta->{$campo}))
            ->count();

        return (int) round(($preenchidos / count($campos)) * 100);
    }
}
