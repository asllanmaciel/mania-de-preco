<?php

namespace App\Support\Lancamento;

use App\Models\Assinatura;
use App\Models\ChamadoSuporte;
use App\Models\Conta;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Preco;
use App\Models\Produto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LaunchRoadmap
{
    public function analisar(): array
    {
        $metricas = $this->metricas();

        $fases = collect([
            $this->faseMvpWeb($metricas),
            $this->faseCobranca($metricas),
            $this->faseProducao(),
            $this->faseConversao($metricas),
            $this->faseMobile(),
            $this->faseExpansao(),
        ])->map(fn (array $fase) => $this->completarFase($fase))->values();

        $pendencias = $this->pendencias($fases);
        $score = (int) round($fases->avg('score'));
        $proximaAcao = $pendencias->firstWhere('critica', true) ?? $pendencias->first();

        return [
            'score' => $score,
            'status' => $this->statusGeral($score, $pendencias),
            'fase_atual' => $fases->first(fn (array $fase) => $fase['score'] < 100) ?? $fases->last(),
            'proxima_acao' => $proximaAcao,
            'pendencias_criticas' => $pendencias->where('critica', true)->count(),
            'pendencias' => $pendencias,
            'fases' => $fases,
            'metricas' => $metricas,
        ];
    }

    private function metricas(): array
    {
        return [
            'contas_ativas' => $this->contar(Conta::class, fn ($query) => $query->whereIn('status', ['ativo', 'trial'])),
            'lojas_ativas' => $this->contar(Loja::class, fn ($query) => $query->where('status', 'ativo')),
            'produtos' => $this->contar(Produto::class),
            'produtos_com_imagem' => $this->contar(Produto::class, fn ($query) => $query->whereNotNull('imagem_principal')->where('imagem_principal', '!=', '')),
            'precos' => $this->contar(Preco::class),
            'planos_ativos' => $this->contar(Plano::class, fn ($query) => $query->where('status', 'ativo')),
            'assinaturas_operacionais' => $this->contar(Assinatura::class, fn ($query) => $query->whereIn('status', ['trial', 'ativa', 'inadimplente'])),
            'chamados_abertos' => $this->contar(ChamadoSuporte::class, fn ($query) => $query->whereNotIn('status', ['resolvido', 'fechado'])),
            'chamados_criticos_abertos' => $this->contar(ChamadoSuporte::class, fn ($query) => $query->where('prioridade', 'critica')->whereNotIn('status', ['resolvido', 'fechado'])),
        ];
    }

    private function faseMvpWeb(array $metricas): array
    {
        return [
            'codigo' => 'mvp-web',
            'titulo' => 'MVP web vendável',
            'descricao' => 'Base para apresentar, vender e operar o produto com super admin, lojista, cliente e vitrine pública.',
            'icone' => 'spark',
            'marco' => 'Lançamento controlado com contas piloto',
            'itens' => [
                $this->item('Painel lojista operacional', 'Dashboard, onboarding, financeiro, lojas, produtos, preços, equipe e assinatura disponíveis.', Route::has('admin.dashboard'), true, 'Abrir painel', $this->rota('admin.dashboard')),
                $this->item('Super admin operacional', 'Governança global com contas, planos, assinaturas e suporte.', Route::has('super-admin.dashboard'), true, 'Abrir super admin', $this->rota('super-admin.dashboard')),
                $this->item('Área do cliente disponível', 'Cliente final consegue acessar radar e alertas de preço.', Route::has('cliente.dashboard'), true, 'Abrir cliente', $this->rota('cliente.dashboard')),
                $this->item('Vitrine pública com dados', 'Landing e ofertas precisam sustentar a promessa com lojas, produtos e preços reais.', $metricas['lojas_ativas'] > 0 && $metricas['produtos'] > 0 && $metricas['precos'] > 0, true, 'Popular vitrine', $this->rota('home')),
                $this->item('Suporte em cards', 'Fila de chamados já organizada para triagem e detalhe.', Route::has('super-admin.suporte.show'), false, 'Ver suporte', $this->rota('super-admin.suporte.index')),
                $this->item('Analytics executivo', 'Leitura de sinais, funil e eventos para orientar o lançamento.', Route::has('super-admin.analytics'), false, 'Ver analytics', $this->rota('super-admin.analytics')),
            ],
        ];
    }

    private function faseCobranca(array $metricas): array
    {
        return [
            'codigo' => 'cobranca',
            'titulo' => 'Cobrança real com Mercado Pago',
            'descricao' => 'Plano comercial, assinatura recorrente, checkout e webhook do Mercado Pago funcionando antes de vender sem intervenção manual.',
            'icone' => 'credit-card',
            'marco' => 'Primeira assinatura Mercado Pago validada em sandbox',
            'itens' => [
                $this->item('Mercado Pago definido como provedor do lançamento', 'A decisão de produto agora é lançar o SaaS com Mercado Pago como gateway principal.', config('billing.providers.mercado_pago.status') === 'mvp_decidido', true, 'Manter decisão Mercado Pago'),
                $this->item('Gateway Mercado Pago implementado', 'Ainda falta criar o serviço que sincroniza cliente, assinatura, checkout e status de pagamento.', class_exists('App\\Services\\Billing\\MercadoPago\\MercadoPagoBillingGateway'), true, 'Implementar gateway Mercado Pago'),
                $this->item('Webhook Mercado Pago publicado', 'Ainda falta endpoint próprio para receber e auditar eventos de pagamento do Mercado Pago.', Route::has('billing.webhooks.mercado-pago'), true, 'Criar webhook Mercado Pago'),
                $this->item('Credenciais Mercado Pago configuradas', 'MERCADO_PAGO_ACCESS_TOKEN, public key e segredo de webhook precisam estar definidos no ambiente.', filled(config('billing.providers.mercado_pago.access_token')) && filled(config('billing.providers.mercado_pago.public_key')) && filled(config('billing.providers.mercado_pago.webhook_secret')), true, 'Configurar credenciais Mercado Pago'),
                $this->item('Planos ativos cadastrados', 'O portfólio comercial precisa estar pronto para venda assistida e demonstração.', $metricas['planos_ativos'] > 0, true, 'Abrir planos', $this->rota('super-admin.planos.index')),
                $this->item('Assinaturas operacionais', 'A base deve ter assinaturas em trial, ativa ou inadimplente para testar a gestão.', $metricas['assinaturas_operacionais'] > 0, false, 'Abrir contas', $this->rota('super-admin.contas.index')),
                $this->item('Base Asaas existente preservada', 'A integração Asaas continua como referência técnica e pode ser mantida como alternativa futura.', Route::has('billing.webhooks.asaas'), false, 'Preservar como legado'),
            ],
        ];
    }

    private function faseProducao(): array
    {
        $appUrl = (string) config('app.url');

        return [
            'codigo' => 'producao',
            'titulo' => 'Produção segura',
            'descricao' => 'Configurações que protegem o produto quando sair do ambiente local para domínio real.',
            'icone' => 'shield',
            'marco' => 'Ambiente pronto para tráfego real',
            'itens' => [
                $this->item('APP_ENV em production', 'O servidor final não deve rodar com ambiente local.', config('app.env') === 'production', true, 'Configurar ambiente'),
                $this->item('APP_DEBUG desativado', 'Erros internos não podem aparecer para usuários reais.', config('app.debug') === false, true, 'Desativar debug'),
                $this->item('Domínio com HTTPS', 'APP_URL precisa usar domínio final com SSL ativo.', Str::startsWith($appUrl, 'https://') && ! Str::contains($appUrl, ['localhost', '127.0.0.1']), true, 'Definir domínio'),
                $this->item('Health check disponível', 'Monitoramento consegue verificar aplicação e infraestrutura.', Route::has('health'), false, 'Abrir health', $this->rota('health')),
                $this->item('Fila assíncrona preparada', 'Notificações e jobs não devem depender da requisição em produção.', config('queue.default') !== 'sync', false, 'Configurar worker'),
                $this->item('E-mail transacional real', 'Senha, suporte e notificações precisam sair por provedor real.', ! in_array(config('mail.default'), ['log', 'array'], true), true, 'Configurar e-mail'),
            ],
        ];
    }

    private function faseConversao(array $metricas): array
    {
        return [
            'codigo' => 'conversao',
            'titulo' => 'Polimento visual e conversão',
            'descricao' => 'Camada de confiança, clareza e desejo para o produto parecer pronto para mercado desde o primeiro acesso.',
            'icone' => 'trend',
            'marco' => 'Experiência pública revisada em desktop e mobile',
            'itens' => [
                $this->item('Home pública disponível', 'A vitrine inicial precisa comunicar valor sem linguagem interna.', Route::has('home'), true, 'Ver home', $this->rota('home')),
                $this->item('Novidades publicadas', 'Changelog público ajuda a demonstrar evolução contínua do produto.', Route::has('novidades.index'), false, 'Ver novidades', $this->rota('novidades.index')),
                $this->item('Termos e privacidade no ar', 'Base legal precisa estar acessível antes de captação real.', Route::has('termos') && Route::has('privacidade'), true, 'Ver termos', $this->rota('termos')),
                $this->item('Produtos com imagens', 'Cards de produto com imagem elevam percepção de qualidade e confiança.', $metricas['produtos'] > 0 && $metricas['produtos_com_imagem'] >= min(6, $metricas['produtos']), false, 'Revisar ofertas', $this->rota('ofertas')),
                $this->item('Suporte público disponível', 'Canal de abertura de chamados precisa estar claro para clientes reais.', Route::has('suporte'), true, 'Ver suporte', $this->rota('suporte')),
                $this->item('Revisão visual final', 'Ajuste fino de tipografia, primeira dobra, cards, estados vazios e responsividade.', false, false, 'Revisar front'),
            ],
        ];
    }

    private function faseMobile(): array
    {
        return [
            'codigo' => 'mobile',
            'titulo' => 'App mobile cliente',
            'descricao' => 'Preparação para Android e iOS com foco no consumidor: ofertas, produtos, lojas e alertas.',
            'icone' => 'package',
            'marco' => 'Primeira versão Flutter conectada na API v1',
            'itens' => [
                $this->item('API de ofertas mobile', 'Endpoint de listagem para alimentar a home do app.', Route::has('api.mobile.ofertas.index'), true, 'Abrir API', $this->rota('api.mobile.ofertas.index')),
                $this->item('API de autenticação mobile', 'Cadastro, login, perfil e logout disponíveis para consumidor.', Route::has('api.mobile.login') && Route::has('api.mobile.register') && Route::has('api.mobile.me'), true, 'Revisar API'),
                $this->item('Alertas mobile na API', 'CRUD de alertas já permite recorrência de uso no app.', Route::has('api.mobile.alertas.index') && Route::has('api.mobile.alertas.store'), true, 'Revisar alertas'),
                $this->item('Documentação mobile v1', 'Contrato inicial da API registrado para guiar o app.', is_file(base_path('docs/api-mobile-v1.md')), false, 'Abrir documentação'),
                $this->item('Projeto Flutter criado', 'Base Android/iOS ainda precisa ser iniciada após fechar MVP web.', false, false, 'Criar app Flutter'),
            ],
        ];
    }

    private function faseExpansao(): array
    {
        return [
            'codigo' => 'expansao',
            'titulo' => 'Expansão pós-MVP',
            'descricao' => 'Frentes importantes, mas que não devem atrasar a primeira rodada vendável do produto.',
            'icone' => 'layers',
            'marco' => 'Produto validado com clientes reais',
            'itens' => [
                $this->item('Asaas como alternativa futura', 'Depois do Mercado Pago validado, Asaas pode permanecer como segundo provedor para cenários B2B específicos.', config('billing.providers.asaas.status') === 'legado_operacional', false, 'Manter alternativa'),
                $this->item('Importação por planilha', 'Entrada em massa de produtos e preços pode acelerar onboarding de lojistas.', false, false, 'Planejar importador'),
                $this->item('Push no app', 'Notificações mobile aumentam recorrência depois que o app existir.', false, false, 'Planejar push'),
                $this->item('Relatórios avançados', 'Camada premium para lojistas acompanharem margem, preço e oportunidade.', false, false, 'Planejar relatórios'),
            ],
        ];
    }

    private function item(string $titulo, string $descricao, bool $concluida, bool $critica, string $acao, ?string $rota = null): array
    {
        return compact('titulo', 'descricao', 'concluida', 'critica', 'acao', 'rota');
    }

    private function completarFase(array $fase): array
    {
        $itens = collect($fase['itens']);
        $concluidos = $itens->where('concluida', true)->count();
        $total = max(1, $itens->count());
        $criticosPendentes = $itens->where('concluida', false)->where('critica', true)->count();
        $score = (int) round(($concluidos / $total) * 100);

        return array_merge($fase, [
            'itens' => $itens,
            'concluidos' => $concluidos,
            'total' => $total,
            'score' => $score,
            'criticos_pendentes' => $criticosPendentes,
            'status' => $this->statusFase($score, $criticosPendentes),
        ]);
    }

    private function pendencias(Collection $fases): Collection
    {
        return $fases
            ->flatMap(fn (array $fase) => $fase['itens']
                ->where('concluida', false)
                ->map(fn (array $item) => array_merge($item, [
                    'fase' => $fase['titulo'],
                    'fase_codigo' => $fase['codigo'],
                    'icone' => $fase['icone'],
                ])))
            ->sortByDesc(fn (array $item) => $item['critica'] ? 1 : 0)
            ->values();
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

    private function rota(string $nome): ?string
    {
        return Route::has($nome) ? route($nome) : null;
    }

    private function statusFase(int $score, int $criticosPendentes): array
    {
        if ($score === 100) {
            return ['label' => 'concluída', 'classe' => ''];
        }

        if ($criticosPendentes > 0) {
            return ['label' => 'bloqueada', 'classe' => 'is-danger'];
        }

        if ($score >= 70) {
            return ['label' => 'em ajuste fino', 'classe' => 'is-warning'];
        }

        return ['label' => 'em construção', 'classe' => 'is-muted'];
    }

    private function statusGeral(int $score, Collection $pendencias): array
    {
        if ($score >= 90 && $pendencias->where('critica', true)->isEmpty()) {
            return [
                'label' => 'Pronto para lançamento controlado',
                'descricao' => 'A base crítica está resolvida; o foco passa a ser QA final, operação e comunicação.',
                'classe' => '',
            ];
        }

        if ($score >= 70) {
            return [
                'label' => 'Quase pronto',
                'descricao' => 'O produto já tem corpo de lançamento, mas ainda precisa fechar bloqueios críticos.',
                'classe' => 'is-warning',
            ];
        }

        return [
            'label' => 'Em preparação',
            'descricao' => 'A fundação está avançando; priorize cobrança, produção e QA antes de abrir tráfego.',
            'classe' => 'is-danger',
        ];
    }
}
