<?php

namespace App\Support\Lancamento;

use App\Models\Assinatura;
use App\Models\ChamadoSuporte;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Preco;
use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class LaunchPreflight
{
    public function analisar(): array
    {
        $metricas = $this->metricas();

        $grupos = collect([
            $this->grupoAmbiente(),
            $this->grupoInfraestrutura(),
            $this->grupoCobranca($metricas),
            $this->grupoProduto($metricas),
            $this->grupoOperacao($metricas),
        ])->map(fn (array $grupo) => $this->completarGrupo($grupo))->values();

        $pendencias = $this->pendencias($grupos);
        $score = (int) round($grupos->avg('score'));
        $bloqueios = $pendencias->where('critica', true)->count();

        return [
            'score' => $score,
            'pronto' => $bloqueios === 0,
            'status' => $this->status($score, $bloqueios),
            'bloqueios_criticos' => $bloqueios,
            'proxima_acao' => $pendencias->firstWhere('critica', true) ?? $pendencias->first(),
            'grupos' => $grupos,
            'pendencias' => $pendencias,
            'metricas' => $metricas,
        ];
    }

    private function grupoAmbiente(): array
    {
        $appUrl = (string) config('app.url');

        return [
            'codigo' => 'ambiente',
            'titulo' => 'Ambiente',
            'descricao' => 'Chaves que definem se a aplicação está segura para sair do local.',
            'icone' => 'settings',
            'itens' => [
                $this->item('APP_ENV em production', 'Servidor final deve rodar com APP_ENV=production.', config('app.env') === 'production', true, 'Configurar APP_ENV', 'APP_ENV', (string) config('app.env')),
                $this->item('APP_DEBUG desativado', 'Detalhes internos de erro não devem vazar para usuários reais.', config('app.debug') === false, true, 'Definir APP_DEBUG=false', 'APP_DEBUG', config('app.debug') ? 'true' : 'false'),
                $this->item('APP_KEY definida', 'Criptografia de sessões, cookies e tokens depende dessa chave.', filled(config('app.key')), true, 'Gerar APP_KEY', 'APP_KEY', filled(config('app.key')) ? 'definida' : 'ausente'),
                $this->item('Domínio final com HTTPS', 'APP_URL precisa apontar para o domínio real com SSL ativo.', Str::startsWith($appUrl, 'https://') && ! Str::contains($appUrl, ['localhost', '127.0.0.1']), true, 'Definir APP_URL', 'APP_URL', $appUrl),
            ],
        ];
    }

    private function grupoInfraestrutura(): array
    {
        return [
            'codigo' => 'infraestrutura',
            'titulo' => 'Infraestrutura',
            'descricao' => 'Banco, cache, filas e endpoints básicos para operar sem fragilidade.',
            'icone' => 'shield',
            'itens' => [
                $this->item('Banco respondendo', 'A aplicação consegue executar uma consulta simples no banco.', $this->bancoRespondendo(), true, 'Validar DB_*', 'DB_CONNECTION', (string) config('database.default')),
                $this->item('Health check registrado', 'Monitoramento externo consegue validar se a aplicação está viva.', Route::has('health'), false, 'Validar /health', null, Route::has('health') ? route('health') : 'ausente', $this->rota('health')),
                $this->item('Fila assíncrona', 'Jobs e notificações não devem depender do tempo da requisição.', config('queue.default') !== 'sync', false, 'Configurar QUEUE_CONNECTION', 'QUEUE_CONNECTION', (string) config('queue.default')),
                $this->item('Cache fora de array', 'Cache em produção deve persistir entre requisições.', config('cache.default') !== 'array', false, 'Configurar CACHE_STORE', 'CACHE_STORE', (string) config('cache.default')),
                $this->item('Sessão persistente', 'Sessões precisam usar driver adequado para produção.', ! in_array(config('session.driver'), ['array'], true), true, 'Configurar SESSION_DRIVER', 'SESSION_DRIVER', (string) config('session.driver')),
            ],
        ];
    }

    private function grupoCobranca(array $metricas): array
    {
        return [
            'codigo' => 'cobranca',
            'titulo' => 'Cobrança',
            'descricao' => 'Itens mínimos para vender planos e refletir eventos do Mercado Pago.',
            'icone' => 'credit-card',
            'itens' => [
                $this->item('Mercado Pago como decisão do MVP', 'O provedor de pagamento do lançamento foi definido como Mercado Pago.', config('billing.providers.mercado_pago.status') === 'mvp_decidido', true, 'Manter Mercado Pago no MVP', null, 'mercado_pago'),
                $this->item('Gateway Mercado Pago implementado', 'Ainda falta o serviço que cria/sincroniza cliente, assinatura e checkout no Mercado Pago.', class_exists('App\\Services\\Billing\\MercadoPago\\MercadoPagoBillingGateway'), true, 'Implementar gateway Mercado Pago', null, 'ausente'),
                $this->item('MERCADO_PAGO_ACCESS_TOKEN configurado', 'Necessário para consumir a API do Mercado Pago.', filled(config('billing.providers.mercado_pago.access_token')), true, 'Configurar MERCADO_PAGO_ACCESS_TOKEN', 'MERCADO_PAGO_ACCESS_TOKEN', filled(config('billing.providers.mercado_pago.access_token')) ? 'definido' : 'ausente'),
                $this->item('MERCADO_PAGO_PUBLIC_KEY configurada', 'Necessária para checkout e identificação pública do provedor.', filled(config('billing.providers.mercado_pago.public_key')), true, 'Configurar MERCADO_PAGO_PUBLIC_KEY', 'MERCADO_PAGO_PUBLIC_KEY', filled(config('billing.providers.mercado_pago.public_key')) ? 'definida' : 'ausente'),
                $this->item('MERCADO_PAGO_WEBHOOK_SECRET configurado', 'Necessário para validar eventos recebidos do Mercado Pago.', filled(config('billing.providers.mercado_pago.webhook_secret')), true, 'Configurar MERCADO_PAGO_WEBHOOK_SECRET', 'MERCADO_PAGO_WEBHOOK_SECRET', filled(config('billing.providers.mercado_pago.webhook_secret')) ? 'definido' : 'ausente'),
                $this->item('Webhook Mercado Pago registrado', 'Ainda falta endpoint para receber e auditar eventos de cobrança.', Route::has('billing.webhooks.mercado-pago'), true, 'Criar rota de webhook Mercado Pago', null, Route::has('billing.webhooks.mercado-pago') ? route('billing.webhooks.mercado-pago') : 'ausente'),
                $this->item('Planos ativos', 'O portfólio comercial precisa existir antes da venda.', $metricas['planos_ativos'] > 0, true, 'Cadastrar planos', null, (string) $metricas['planos_ativos'], $this->rota('super-admin.planos.index')),
                $this->item('Assinaturas de teste', 'Ajuda a validar gestão, inadimplência e checkout no backoffice.', $metricas['assinaturas_operacionais'] > 0, false, 'Criar assinatura teste', null, (string) $metricas['assinaturas_operacionais'], $this->rota('super-admin.contas.index')),
            ],
        ];
    }

    private function grupoProduto(array $metricas): array
    {
        return [
            'codigo' => 'produto',
            'titulo' => 'Produto',
            'descricao' => 'Base pública e conteúdo suficientes para o produto demonstrar valor.',
            'icone' => 'spark',
            'itens' => [
                $this->item('Rotas públicas essenciais', 'Home, ofertas, novidades, suporte, termos e privacidade precisam estar no ar.', $this->rotasExistem(['home', 'ofertas', 'novidades.index', 'suporte', 'termos', 'privacidade']), true, 'Revisar rotas públicas', null, 'web', $this->rota('home')),
                $this->item('Vitrine com ofertas', 'Usuários precisam ver lojas, produtos e preços reais ou demo.', $metricas['lojas_ativas'] > 0 && $metricas['produtos'] > 0 && $metricas['precos'] > 0, true, 'Popular vitrine', null, "{$metricas['produtos']} produtos / {$metricas['precos']} preços", $this->rota('ofertas')),
                $this->item('Produtos com imagem', 'Cards com imagem elevam confiança e reduzem sensação de protótipo.', $metricas['produtos'] > 0 && $metricas['produtos_com_imagem'] >= min(6, $metricas['produtos']), false, 'Revisar catálogo', null, "{$metricas['produtos_com_imagem']} com imagem", $this->rota('ofertas')),
                $this->item('API mobile v1', 'Base para Android/iOS já precisa responder para o app cliente.', $this->rotasExistem(['api.mobile.ofertas.index', 'api.mobile.login', 'api.mobile.alertas.index']), false, 'Validar API mobile', null, 'mobile/v1'),
            ],
        ];
    }

    private function grupoOperacao(array $metricas): array
    {
        return [
            'codigo' => 'operacao',
            'titulo' => 'Operação',
            'descricao' => 'Suporte, e-mail e risco operacional antes de receber tráfego real.',
            'icone' => 'bell',
            'itens' => [
                $this->item('E-mail transacional real', 'Recuperação de senha, suporte e notificações precisam sair por provedor real.', ! in_array(config('mail.default'), ['log', 'array'], true), true, 'Configurar MAIL_MAILER', 'MAIL_MAILER', (string) config('mail.default')),
                $this->item('Suporte público aberto', 'Visitantes e clientes precisam conseguir abrir chamado.', Route::has('suporte.chamados.store'), true, 'Validar suporte', null, Route::has('suporte') ? route('suporte') : 'ausente', $this->rota('suporte')),
                $this->item('Chamados críticos controlados', 'Chamados críticos abertos precisam estar zerados ou acompanhados.', $metricas['chamados_criticos_abertos'] === 0, false, 'Atender chamados', null, (string) $metricas['chamados_criticos_abertos'], $this->rota('super-admin.suporte.index')),
                $this->item('Documentação de roadmap criada', 'O roteiro precisa estar registrado para alinhar produto, operação e comercial.', is_file(base_path('docs/roadmap-lancamento.md')), false, 'Revisar roadmap', null, 'docs/roadmap-lancamento.md'),
            ],
        ];
    }

    private function item(string $titulo, string $descricao, bool $concluida, bool $critica, string $acao, ?string $env = null, ?string $valor = null, ?string $rota = null): array
    {
        return compact('titulo', 'descricao', 'concluida', 'critica', 'acao', 'env', 'valor', 'rota');
    }

    private function metricas(): array
    {
        return [
            'lojas_ativas' => $this->contar(Loja::class, fn ($query) => $query->where('status', 'ativo')),
            'produtos' => $this->contar(Produto::class),
            'produtos_com_imagem' => $this->contar(Produto::class, fn ($query) => $query->whereNotNull('imagem_principal')->where('imagem_principal', '!=', '')),
            'precos' => $this->contar(Preco::class),
            'planos_ativos' => $this->contar(Plano::class, fn ($query) => $query->where('status', 'ativo')),
            'assinaturas_operacionais' => $this->contar(Assinatura::class, fn ($query) => $query->whereIn('status', ['trial', 'ativa', 'inadimplente'])),
            'chamados_criticos_abertos' => $this->contar(ChamadoSuporte::class, fn ($query) => $query->where('prioridade', 'critica')->whereNotIn('status', ['resolvido', 'fechado'])),
        ];
    }

    private function completarGrupo(array $grupo): array
    {
        $itens = collect($grupo['itens']);
        $concluidos = $itens->where('concluida', true)->count();
        $total = max(1, $itens->count());
        $criticosPendentes = $itens->where('concluida', false)->where('critica', true)->count();
        $score = (int) round(($concluidos / $total) * 100);

        return array_merge($grupo, [
            'itens' => $itens,
            'concluidos' => $concluidos,
            'total' => $total,
            'score' => $score,
            'criticos_pendentes' => $criticosPendentes,
            'status' => $this->statusGrupo($score, $criticosPendentes),
        ]);
    }

    private function pendencias(Collection $grupos): Collection
    {
        return $grupos
            ->flatMap(fn (array $grupo) => $grupo['itens']
                ->where('concluida', false)
                ->map(fn (array $item) => array_merge($item, [
                    'grupo' => $grupo['titulo'],
                    'grupo_codigo' => $grupo['codigo'],
                    'icone' => $grupo['icone'],
                ])))
            ->sortByDesc(fn (array $item) => $item['critica'] ? 1 : 0)
            ->values();
    }

    private function bancoRespondendo(): bool
    {
        try {
            DB::select('select 1');

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    private function contar(string $model, ?callable $callback = null): int
    {
        $instancia = new $model();

        if (! Schema::hasTable($instancia->getTable())) {
            return 0;
        }

        $query = $model::query();

        if ($callback) {
            $callback($query);
        }

        return $query->count();
    }

    private function rotasExistem(array $rotas): bool
    {
        return collect($rotas)->every(fn (string $rota) => Route::has($rota));
    }

    private function rota(string $nome): ?string
    {
        return Route::has($nome) ? route($nome) : null;
    }

    private function statusGrupo(int $score, int $criticosPendentes): array
    {
        if ($score === 100) {
            return ['label' => 'pronto', 'classe' => ''];
        }

        if ($criticosPendentes > 0) {
            return ['label' => 'atenção crítica', 'classe' => 'is-danger'];
        }

        return ['label' => 'ajuste recomendado', 'classe' => 'is-warning'];
    }

    private function status(int $score, int $bloqueios): array
    {
        if ($bloqueios === 0) {
            return [
                'label' => 'Apto para QA final',
                'descricao' => 'Não há bloqueios críticos no pré-flight. O próximo passo é validar jornada real e observabilidade.',
                'classe' => '',
            ];
        }

        if ($score >= 70) {
            return [
                'label' => 'Quase pronto, com bloqueios',
                'descricao' => 'A base está forte, mas ainda há itens críticos que não deveriam ir para produção.',
                'classe' => 'is-warning',
            ];
        }

        return [
            'label' => 'Ainda não lançar',
            'descricao' => 'Resolva os bloqueios críticos antes de tratar o ambiente como pronto para clientes reais.',
            'classe' => 'is-danger',
        ];
    }
}
