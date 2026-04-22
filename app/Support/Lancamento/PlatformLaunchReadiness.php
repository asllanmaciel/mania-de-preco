<?php

namespace App\Support\Lancamento;

use App\Models\Assinatura;
use App\Models\AnalyticsEvent;
use App\Models\ChamadoSuporte;
use App\Models\Conta;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use App\Notifications\ChamadoSuporteAbertoNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PlatformLaunchReadiness
{
    public function analisar(): array
    {
        $metricas = [
            'contas_ativas' => Conta::whereIn('status', ['ativo', 'trial'])->count(),
            'lojas_ativas' => Loja::where('status', 'ativo')->count(),
            'produtos' => Produto::count(),
            'produtos_com_imagem' => Produto::whereNotNull('imagem_principal')->where('imagem_principal', '!=', '')->count(),
            'precos' => Preco::count(),
            'usuarios' => User::count(),
            'usuarios_com_consentimento' => User::whereNotNull('termos_aceitos_em')->count(),
            'eventos_analytics' => AnalyticsEvent::count(),
            'super_admins' => User::where('is_super_admin', true)->count(),
            'planos_ativos' => Plano::where('status', 'ativo')->count(),
            'assinaturas_operacionais' => Assinatura::whereIn('status', ['trial', 'ativa'])->count(),
            'chamados_criticos_abertos' => ChamadoSuporte::where('prioridade', 'critica')
                ->whereNotIn('status', ['resolvido', 'fechado'])
                ->count(),
        ];

        $grupos = collect([
            $this->grupoInfraestrutura(),
            $this->grupoReceita($metricas),
            $this->grupoExperiencia($metricas),
            $this->grupoOperacao($metricas),
        ])->map(function (array $grupo) {
            $concluidas = collect($grupo['etapas'])->where('concluida', true)->count();
            $total = max(1, count($grupo['etapas']));

            return array_merge($grupo, [
                'concluidas' => $concluidas,
                'total' => $total,
                'score' => (int) round(($concluidas / $total) * 100),
            ]);
        })->values();

        $score = (int) round($grupos->avg('score'));
        $pendencias = $this->pendencias($grupos);

        return [
            'score' => $score,
            'nivel' => $this->nivel($score, $pendencias),
            'pronta' => $score >= 90 && $pendencias->where('critica', true)->isEmpty(),
            'grupos' => $grupos,
            'metricas' => $metricas,
            'pendencias_criticas' => $pendencias->where('critica', true)->count(),
            'proximas_acoes' => $pendencias
                ->sortByDesc(fn (array $etapa) => $etapa['critica'] ? 1 : 0)
                ->take(5)
                ->values(),
        ];
    }

    private function grupoInfraestrutura(): array
    {
        $appUrl = (string) config('app.url');

        return [
            'codigo' => 'infraestrutura',
            'titulo' => 'Produção e infraestrutura',
            'descricao' => 'Configurações que evitam vazamento de erro, links quebrados e operação frágil depois do deploy.',
            'icone' => 'settings',
            'etapas' => [
                [
                    'titulo' => 'Ambiente em produção',
                    'descricao' => 'APP_ENV deve estar como production no servidor final.',
                    'concluida' => config('app.env') === 'production',
                    'critica' => true,
                    'acao' => 'Configurar APP_ENV=production',
                ],
                [
                    'titulo' => 'Debug desativado',
                    'descricao' => 'APP_DEBUG=false impede que detalhes internos apareçam para usuários reais.',
                    'concluida' => config('app.debug') === false,
                    'critica' => true,
                    'acao' => 'Desativar APP_DEBUG',
                ],
                [
                    'titulo' => 'URL pública com HTTPS',
                    'descricao' => 'APP_URL precisa apontar para o domínio real com certificado SSL ativo.',
                    'concluida' => Str::startsWith($appUrl, 'https://') && ! Str::contains($appUrl, ['localhost', '127.0.0.1']),
                    'critica' => true,
                    'acao' => 'Definir domínio final',
                ],
                [
                    'titulo' => 'Fila assíncrona configurada',
                    'descricao' => 'Jobs e notificações não devem depender da requisição do usuário em produção.',
                    'concluida' => config('queue.default') !== 'sync',
                    'critica' => false,
                    'acao' => 'Configurar worker de filas',
                ],
                [
                    'titulo' => 'Localização brasileira aplicada',
                    'descricao' => 'Fuso e idioma precisam estar alinhados com o mercado inicial do produto.',
                    'concluida' => config('app.locale') === 'pt_BR' && config('app.timezone') === 'America/Sao_Paulo',
                    'critica' => false,
                    'acao' => 'Ajustar locale e timezone',
                ],
                [
                    'titulo' => 'Health check operacional',
                    'descricao' => 'Um endpoint de saúde permite monitorar aplicação, banco, cache e storage em produção.',
                    'concluida' => Route::has('health'),
                    'critica' => false,
                    'acao' => 'Validar /health',
                    'rota' => route('health'),
                ],
                [
                    'titulo' => 'Limites de abuso em rotas sensíveis',
                    'descricao' => 'Login, cadastro, senha, suporte e radar precisam de rate limit antes de receber tráfego real.',
                    'concluida' => $this->rotasComThrottle([
                        'login.store',
                        'register.store',
                        'password.email',
                        'password.update',
                        'suporte.chamados.store',
                        'radar.precos',
                    ]),
                    'critica' => true,
                    'acao' => 'Revisar limites de rotas',
                ],
            ],
        ];
    }

    private function grupoReceita(array $metricas): array
    {
        return [
            'codigo' => 'receita',
            'titulo' => 'Receita e cobrança',
            'descricao' => 'Base mínima para vender planos, ativar assinaturas e receber eventos do provedor de pagamento.',
            'icone' => 'credit-card',
            'etapas' => [
                [
                    'titulo' => 'Planos ativos cadastrados',
                    'descricao' => 'O portfólio comercial precisa existir antes da venda assistida ou self-service.',
                    'concluida' => $metricas['planos_ativos'] > 0,
                    'critica' => true,
                    'acao' => 'Cadastrar planos',
                    'rota' => route('super-admin.planos.index'),
                ],
                [
                    'titulo' => 'Provedor de cobrança definido',
                    'descricao' => 'Mercado Pago será o provedor principal do MVP; a implementação operacional ainda precisa ser concluída.',
                    'concluida' => config('billing.providers.mercado_pago.status') === 'mvp_decidido',
                    'critica' => true,
                    'acao' => 'Manter Mercado Pago como decisão',
                ],
                [
                    'titulo' => 'Gateway Mercado Pago implementado',
                    'descricao' => 'A plataforma precisa criar cliente, assinatura, checkout e atualizar status via Mercado Pago.',
                    'concluida' => class_exists('App\\Services\\Billing\\MercadoPago\\MercadoPagoBillingGateway'),
                    'critica' => true,
                    'acao' => 'Implementar Mercado Pago',
                ],
                [
                    'titulo' => 'Credenciais Mercado Pago configuradas',
                    'descricao' => 'Access token, public key e segredo de webhook habilitam checkout e validação de eventos.',
                    'concluida' => filled(config('billing.providers.mercado_pago.access_token'))
                        && filled(config('billing.providers.mercado_pago.public_key'))
                        && filled(config('billing.providers.mercado_pago.webhook_secret')),
                    'critica' => true,
                    'acao' => 'Configurar credenciais Mercado Pago',
                ],
                [
                    'titulo' => 'Webhook Mercado Pago publicado',
                    'descricao' => 'O endpoint de webhook precisa receber e auditar eventos de pagamento do Mercado Pago.',
                    'concluida' => Route::has('billing.webhooks.mercado-pago'),
                    'critica' => true,
                    'acao' => 'Criar webhook Mercado Pago',
                ],
                [
                    'titulo' => 'Assinaturas operacionais',
                    'descricao' => 'Ter assinaturas em trial ou ativas valida a esteira comercial no backoffice.',
                    'concluida' => $metricas['assinaturas_operacionais'] > 0,
                    'critica' => false,
                    'acao' => 'Criar assinatura teste',
                    'rota' => route('super-admin.contas.index'),
                ],
            ],
        ];
    }

    private function grupoExperiencia(array $metricas): array
    {
        $rotasPublicas = collect(['home', 'ofertas', 'novidades.index', 'termos', 'privacidade', 'suporte', 'seo.robots', 'seo.sitemap'])
            ->every(fn (string $rota) => Route::has($rota));

        return [
            'codigo' => 'experiencia',
            'titulo' => 'Experiência pública',
            'descricao' => 'O comprador precisa encontrar valor, confiança e suporte antes de criar conta ou procurar uma loja.',
            'icone' => 'spark',
            'etapas' => [
                [
                    'titulo' => 'Rotas públicas essenciais',
                    'descricao' => 'Home, ofertas, novidades, termos, privacidade, suporte, robots e sitemap precisam estar disponíveis.',
                    'concluida' => $rotasPublicas,
                    'critica' => true,
                    'acao' => 'Revisar páginas públicas',
                    'rota' => route('home'),
                ],
                [
                    'titulo' => 'Vitrine com ofertas reais',
                    'descricao' => 'A landing precisa mostrar lojas ativas, produtos e preços para sustentar a proposta de valor.',
                    'concluida' => $metricas['lojas_ativas'] > 0 && $metricas['produtos'] > 0 && $metricas['precos'] > 0,
                    'critica' => true,
                    'acao' => 'Popular vitrine',
                    'rota' => route('super-admin.contas.index'),
                ],
                [
                    'titulo' => 'Produtos com imagem',
                    'descricao' => 'Imagens elevam confiança e tornam os cards mais próximos de uma experiência de varejo.',
                    'concluida' => $metricas['produtos'] > 0 && $metricas['produtos_com_imagem'] >= min(6, $metricas['produtos']),
                    'critica' => false,
                    'acao' => 'Revisar catálogo',
                    'rota' => route('home'),
                ],
                [
                    'titulo' => 'E-mail transacional fora do log',
                    'descricao' => 'Recuperação de senha, suporte e notificações precisam sair por um provedor real.',
                    'concluida' => ! in_array(config('mail.default'), ['log', 'array'], true),
                    'critica' => true,
                    'acao' => 'Configurar provedor de e-mail',
                ],
                [
                    'titulo' => 'Confirmação de protocolo por e-mail',
                    'descricao' => 'O cliente deve receber confirmação transacional quando abre um chamado público.',
                    'concluida' => class_exists(ChamadoSuporteAbertoNotification::class),
                    'critica' => false,
                    'acao' => 'Validar e-mail de suporte',
                    'rota' => route('suporte'),
                ],
                [
                    'titulo' => 'Consentimento legal nos formulários',
                    'descricao' => 'Cadastro e suporte registram aceite de Termos e Privacidade com versão, data e origem.',
                    'concluida' => Schema::hasColumn('users', 'termos_aceitos_em')
                        && Schema::hasColumn('chamados_suporte', 'termos_aceitos_em'),
                    'critica' => true,
                    'acao' => 'Validar consentimento LGPD',
                    'rota' => route('termos'),
                ],
                [
                    'titulo' => 'Analytics de produto ativo',
                    'descricao' => 'Busca, visualizacao, cadastro e suporte devem gerar sinais internos para orientar lancamento.',
                    'concluida' => Schema::hasTable('analytics_events'),
                    'critica' => false,
                    'acao' => 'Monitorar sinais',
                    'rota' => route('super-admin.dashboard'),
                ],
            ],
        ];
    }

    private function grupoOperacao(array $metricas): array
    {
        return [
            'codigo' => 'operacao',
            'titulo' => 'Operação e suporte',
            'descricao' => 'Governança mínima para atender clientes, operar contas e reduzir risco no lançamento.',
            'icone' => 'shield',
            'etapas' => [
                [
                    'titulo' => 'Super admin criado',
                    'descricao' => 'A plataforma precisa ter pelo menos um usuário com acesso de governança global.',
                    'concluida' => $metricas['super_admins'] > 0,
                    'critica' => true,
                    'acao' => 'Criar super admin',
                ],
                [
                    'titulo' => 'Base inicial de contas',
                    'descricao' => 'Contas ativas ou em trial ajudam a validar a jornada comercial e operacional.',
                    'concluida' => $metricas['contas_ativas'] > 0,
                    'critica' => false,
                    'acao' => 'Cadastrar contas piloto',
                    'rota' => route('super-admin.contas.index'),
                ],
                [
                    'titulo' => 'Fila crítica de suporte controlada',
                    'descricao' => 'Chamados críticos abertos precisam estar zerados ou acompanhados antes de abrir mais tráfego.',
                    'concluida' => $metricas['chamados_criticos_abertos'] === 0,
                    'critica' => false,
                    'acao' => 'Atender chamados críticos',
                    'rota' => route('super-admin.suporte.index'),
                ],
                [
                    'titulo' => 'Usuários reais na base',
                    'descricao' => 'A operação deve ter usuários suficientes para testar lojista, cliente e super admin.',
                    'concluida' => $metricas['usuarios'] >= 3,
                    'critica' => false,
                    'acao' => 'Revisar acessos',
                ],
            ],
        ];
    }

    private function pendencias(Collection $grupos): Collection
    {
        return $grupos
            ->flatMap(fn (array $grupo) => collect($grupo['etapas'])->map(fn (array $etapa) => array_merge($etapa, [
                'grupo' => $grupo['titulo'],
                'icone' => $grupo['icone'],
            ])))
            ->where('concluida', false)
            ->values();
    }

    private function rotasComThrottle(array $nomes): bool
    {
        return collect($nomes)->every(function (string $nome) {
            $rota = Route::getRoutes()->getByName($nome);

            return $rota && collect($rota->gatherMiddleware())->contains(fn (string $middleware) => str_starts_with($middleware, 'throttle:'));
        });
    }

    private function nivel(int $score, Collection $pendencias): array
    {
        if ($score >= 90 && $pendencias->where('critica', true)->isEmpty()) {
            return [
                'nome' => 'Pronto para lançamento',
                'descricao' => 'A plataforma tem os pilares críticos para operar em produção com confiança.',
            ];
        }

        if ($score >= 75) {
            return [
                'nome' => 'Quase pronto',
                'descricao' => 'A base está forte, mas ainda existem pontos que precisam ser fechados antes de ampliar tráfego.',
            ];
        }

        if ($score >= 50) {
            return [
                'nome' => 'Em preparação final',
                'descricao' => 'O produto já tem estrutura de MVP, mas ainda precisa fechar configurações críticas de produção.',
            ];
        }

        return [
            'nome' => 'Fundação de lançamento',
            'descricao' => 'Priorize os bloqueios críticos antes de tratar o lançamento como produção aberta.',
        ];
    }
}
