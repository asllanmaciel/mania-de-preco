<?php

namespace Tests\Feature\Web;

use App\Models\Conta;
use App\Models\AnalyticsEvent;
use App\Models\Categoria;
use App\Models\ChamadoSuporte;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\Produto;
use App\Models\Assinatura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_trying_to_access_admin(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_user_can_login_and_access_admin_dashboard(): void
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'web@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Web',
            'slug' => 'conta-web',
            'email' => 'web@example.com',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $conta->usuarios()->attach($user->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'web@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/painel');
        $this->assertAuthenticatedAs($user);

        $this->get('/painel')
            ->assertRedirect('/admin');

        $this->get('/admin')
            ->assertOk()
            ->assertSee('Painel administrativo')
            ->assertSee('Conta Web')
            ->assertSee('Centro de comando')
            ->assertSee('Inicio');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'reset@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Esqueci minha senha');

        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_login_page_shows_local_demo_accesses(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Acessos da base demo local')
            ->assertSee('test@example.com / password')
            ->assertSee('admin@maniadepreco.com.br / password')
            ->assertSee('cliente.demo@maniadepreco.com.br / password');
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'token-reset@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $token = Password::createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Defina uma nova senha');

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('novaSenha123', $user->fresh()->password));
    }

    public function test_super_admin_is_redirected_to_super_admin_dashboard_after_login(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/painel');
        $this->assertAuthenticatedAs($user);

        $this->get('/painel')
            ->assertRedirect('/super-admin');

        $this->get('/super-admin')
            ->assertOk()
            ->assertSee('Super admin da plataforma')
            ->assertSee('Prontidao global de lancamento')
            ->assertSee('Produção e infraestrutura')
            ->assertSee('Receita e cobrança')
            ->assertSee('Experiência pública')
            ->assertSee('Operação e suporte')
            ->assertSee('Limites de abuso em rotas sensíveis')
            ->assertSee('Provedor de cobrança definido')
            ->assertSee('Analytics de produto ativo')
            ->assertSee('Sinais de produto')
            ->assertSee('contas monitoradas');
    }

    public function test_super_admin_can_manage_support_ticket_queue(): void
    {
        $user = User::create([
            'name' => 'Super Admin Suporte',
            'email' => 'admin-suporte@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $chamado = ChamadoSuporte::create([
            'protocolo' => 'MP-20260419-ABC123',
            'nome' => 'Cliente Suporte',
            'email' => 'cliente-suporte@example.com',
            'empresa' => 'Mercado Modelo',
            'categoria' => 'cobranca',
            'prioridade' => 'critica',
            'status' => 'novo',
            'assunto' => 'Cobranca bloqueada',
            'mensagem' => 'Nao consigo regularizar a assinatura e preciso de ajuda para manter a loja ativa.',
        ]);

        $this->actingAs($user)
            ->get(route('super-admin.suporte.index'))
            ->assertOk()
            ->assertSee('Central de suporte')
            ->assertSee('Fila operacional em cards')
            ->assertSee('MP-20260419-ABC123')
            ->assertSee('Cobranca bloqueada');

        $this->actingAs($user)
            ->get(route('super-admin.suporte.show', $chamado))
            ->assertOk()
            ->assertSee('Mensagem do cliente')
            ->assertSee('Atualizar chamado')
            ->assertSee('Cobranca bloqueada');

        $this->actingAs($user)
            ->patch(route('super-admin.suporte.update', $chamado), [
                'status' => 'resolvido',
                'prioridade' => 'alta',
                'observacao_interna' => 'Cliente orientado e cobranca regularizada.',
            ])
            ->assertRedirect(route('super-admin.suporte.show', $chamado));

        $chamado->refresh();

        $this->assertSame('resolvido', $chamado->status);
        $this->assertSame('alta', $chamado->prioridade);
        $this->assertNotNull($chamado->resolvido_em);
    }

    public function test_super_admin_can_open_analytics_center(): void
    {
        $user = User::create([
            'name' => 'Super Admin Analytics',
            'email' => 'admin-analytics@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $categoria = Categoria::create([
            'nome' => 'Mercearia',
            'slug' => 'mercearia-analytics',
        ]);

        $produto = Produto::create([
            'nome' => 'Cafe Analytics 500g',
            'slug' => 'cafe-analytics-500g',
            'categoria_id' => $categoria->id,
            'status' => 'ativo',
        ]);

        $loja = Loja::create([
            'nome' => 'Loja Analytics',
            'cidade' => 'Curitiba',
            'uf' => 'PR',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'evento' => 'mobile.catalog.filtered',
            'area' => 'mobile',
            'metadata' => ['cidade' => 'Curitiba'],
            'ip' => '127.0.0.1',
            'ocorreu_em' => now(),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'evento' => 'mobile.product.viewed',
            'area' => 'mobile',
            'sujeito_type' => Produto::class,
            'sujeito_id' => $produto->id,
            'metadata' => ['produto' => $produto->nome],
            'ip' => '127.0.0.1',
            'ocorreu_em' => now(),
        ]);

        AnalyticsEvent::create([
            'evento' => 'public.store.viewed',
            'area' => 'public',
            'sujeito_type' => Loja::class,
            'sujeito_id' => $loja->id,
            'metadata' => ['loja' => $loja->nome],
            'ip' => '127.0.0.2',
            'ocorreu_em' => now(),
        ]);

        AnalyticsEvent::create([
            'user_id' => $user->id,
            'evento' => 'mobile.customer_registered',
            'area' => 'mobile',
            'ip' => '127.0.0.1',
            'ocorreu_em' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('super-admin.analytics', ['periodo' => 30]))
            ->assertOk()
            ->assertSee('Analytics para saber onde o Mania de Pre')
            ->assertSee('Funil de convers')
            ->assertSee('mobile.product.viewed')
            ->assertSee('Cafe Analytics 500g')
            ->assertSee('Loja Analytics')
            ->assertSee('sinais mobile');
    }

    public function test_super_admin_can_open_launch_roadmap_panel(): void
    {
        $user = User::create([
            'name' => 'Super Admin Roadmap',
            'email' => 'admin-roadmap@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($user)
            ->get(route('super-admin.roadmap'))
            ->assertOk()
            ->assertSee('Roadmap vivo')
            ->assertSee('Checklist operacional')
            ->assertSee('MVP web')
            ->assertSee('Cobran')
            ->assertSee('App mobile cliente')
            ->assertSee('Mercado Pago')
            ->assertSee('Próximas ações');
    }

    public function test_super_admin_can_open_accounts_index_and_account_detail(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin2@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Estrutural',
            'slug' => 'conta-estrutural',
            'email' => 'conta@example.com',
            'status' => 'ativo',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $conta->usuarios()->attach($user->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($user)
            ->get('/super-admin/contas')
            ->assertOk()
            ->assertSee('Gestao de contas')
            ->assertSee('Conta Estrutural');

        $this->actingAs($user)
            ->get("/super-admin/contas/{$conta->id}")
            ->assertOk()
            ->assertSee('Conta Estrutural')
            ->assertSee('Saude da conta')
            ->assertSee('Assinaturas')
            ->assertSee('Usuarios da conta');
    }

    public function test_super_admin_can_manage_plans(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin-planos@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $this->actingAs($user)
            ->get('/super-admin/planos')
            ->assertOk()
            ->assertSee('Catalogo de planos');

        $this->actingAs($user)
            ->post('/super-admin/planos', [
                'nome' => 'Enterprise',
                'slug' => 'enterprise',
                'descricao' => 'Plano enterprise',
                'valor_mensal' => 499.90,
                'valor_anual' => 4999.00,
                'limite_usuarios' => 50,
                'limite_lojas' => 20,
                'limite_produtos' => 50000,
                'status' => 'ativo',
                'recursos_texto' => "suporte prioritario\nexpansao nacional",
            ])
            ->assertRedirect('/super-admin/planos');

        $plano = Plano::where('slug', 'enterprise')->firstOrFail();

        $this->assertSame(['suporte prioritario', 'expansao nacional'], $plano->recursos);

        $this->actingAs($user)
            ->put("/super-admin/planos/{$plano->id}", [
                'nome' => 'Enterprise Plus',
                'slug' => 'enterprise-plus',
                'descricao' => 'Plano enterprise atualizado',
                'valor_mensal' => 599.90,
                'valor_anual' => 5999.00,
                'limite_usuarios' => 80,
                'limite_lojas' => 30,
                'limite_produtos' => 70000,
                'status' => 'ativo',
                'recursos_texto' => "suporte prioritario\nbilling dedicado",
            ])
            ->assertRedirect("/super-admin/planos/{$plano->id}/edit");

        $plano->refresh();

        $this->assertSame('Enterprise Plus', $plano->nome);
        $this->assertSame('enterprise-plus', $plano->slug);
        $this->assertSame(['suporte prioritario', 'billing dedicado'], $plano->recursos);
    }

    public function test_super_admin_can_create_and_update_account_subscription(): void
    {
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin-assinaturas@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Comercial',
            'slug' => 'conta-comercial',
            'documento' => '12.345.678/0001-99',
            'email' => 'comercial@example.com',
            'status' => 'ativo',
            'trial_ends_at' => now()->addDays(10),
        ]);

        $planoStarter = Plano::create([
            'nome' => 'Starter',
            'slug' => 'starter-admin-access',
            'descricao' => 'Plano starter',
            'valor_mensal' => 49.90,
            'valor_anual' => 499.00,
            'status' => 'ativo',
        ]);

        $planoGrowth = Plano::create([
            'nome' => 'Growth',
            'slug' => 'growth-admin-access',
            'descricao' => 'Plano growth',
            'valor_mensal' => 149.90,
            'valor_anual' => 1499.00,
            'status' => 'ativo',
        ]);

        $this->actingAs($user)
            ->post("/super-admin/contas/{$conta->id}/assinaturas", [
                'plano_id' => $planoStarter->id,
                'status' => 'ativa',
                'ciclo_cobranca' => 'mensal',
                'inicia_em' => now()->toDateString(),
                'expira_em' => now()->addMonth()->toDateString(),
                'billing_provider' => 'asaas',
                'observacoes' => 'Primeira entrada comercial',
            ])
            ->assertRedirect("/super-admin/contas/{$conta->id}");

        $assinatura = Assinatura::where('conta_id', $conta->id)->firstOrFail();

        $this->assertSame($planoStarter->id, $assinatura->plano_id);
        $this->assertSame('ativa', $assinatura->status);
        $this->assertSame('asaas', $assinatura->billing_provider);
        $this->assertSame('49.90', number_format((float) $assinatura->valor, 2, '.', ''));

        $this->actingAs($user)
            ->put("/super-admin/contas/{$conta->id}/assinaturas/{$assinatura->id}", [
                'plano_id' => $planoGrowth->id,
                'status' => 'inadimplente',
                'ciclo_cobranca' => 'anual',
                'valor' => 1299.00,
                'inicia_em' => now()->subMonth()->toDateString(),
                'expira_em' => now()->addYear()->toDateString(),
                'billing_provider' => 'asaas',
                'observacoes' => 'Upgrade anual',
            ])
            ->assertRedirect("/super-admin/contas/{$conta->id}");

        $assinatura->refresh();

        $this->assertSame($planoGrowth->id, $assinatura->plano_id);
        $this->assertSame('inadimplente', $assinatura->status);
        $this->assertSame('anual', $assinatura->ciclo_cobranca);
        $this->assertSame('1299.00', number_format((float) $assinatura->valor, 2, '.', ''));
    }

    public function test_super_admin_can_sync_subscription_with_billing_provider(): void
    {
        config()->set('billing.providers.asaas.api_key', 'asaas_test_key');

        Http::fake([
            'https://api-sandbox.asaas.com/v3/customers' => Http::response([
                'id' => 'cus_123456',
                'name' => 'Conta Estrutural LTDA',
            ]),
            'https://api-sandbox.asaas.com/v3/subscriptions' => Http::response([
                'id' => 'sub_123456',
                'status' => 'ACTIVE',
            ]),
            'https://api-sandbox.asaas.com/v3/subscriptions/sub_123456/payments*' => Http::response([
                'data' => [
                    [
                        'id' => 'pay_123456',
                        'invoiceUrl' => 'https://asaas.test/faturas/pay_123456',
                    ],
                ],
            ]),
        ]);

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'admin-sync@example.com',
            'password' => 'password',
            'is_super_admin' => true,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Estrutural',
            'razao_social' => 'Conta Estrutural LTDA',
            'slug' => 'conta-estrutural-sync',
            'documento' => '12.345.678/0001-99',
            'email' => 'conta-sync@example.com',
            'telefone' => '(11) 4000-1122',
            'status' => 'ativo',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $plano = Plano::create([
            'nome' => 'Pro',
            'slug' => 'pro',
            'descricao' => 'Plano profissional',
            'valor_mensal' => 99.90,
            'valor_anual' => 999.00,
            'status' => 'ativo',
        ]);

        $assinatura = Assinatura::create([
            'conta_id' => $conta->id,
            'plano_id' => $plano->id,
            'status' => 'ativa',
            'ciclo_cobranca' => 'mensal',
            'valor' => 99.90,
            'inicia_em' => now()->subDays(20)->toDateString(),
            'expira_em' => now()->addDays(10)->toDateString(),
        ]);

        $this->actingAs($user)
            ->post("/super-admin/contas/{$conta->id}/assinaturas/{$assinatura->id}/sincronizar")
            ->assertRedirect();

        $conta->refresh();
        $assinatura->refresh();

        $this->assertSame('asaas', $conta->billing_provider);
        $this->assertSame('cus_123456', $conta->billing_customer_id);
        $this->assertSame('asaas', $assinatura->billing_provider);
        $this->assertSame('sub_123456', $assinatura->billing_subscription_id);
        $this->assertSame('ACTIVE', $assinatura->billing_status);
        $this->assertSame('https://asaas.test/faturas/pay_123456', $assinatura->billing_checkout_url);
    }

    public function test_cliente_without_admin_access_is_redirected_to_cliente_area(): void
    {
        $user = User::create([
            'name' => 'Cliente Web',
            'email' => 'cliente@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'cliente@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/painel');
        $this->assertAuthenticatedAs($user);

        $this->get('/painel')
            ->assertRedirect('/cliente');

        $this->get('/cliente')
            ->assertOk()
            ->assertSee('Area do cliente')
            ->assertSee('Meus alertas');
    }

    public function test_cliente_cannot_access_admin_panel(): void
    {
        $user = User::create([
            'name' => 'Cliente Web',
            'email' => 'cliente2@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect('/cliente');
    }

    public function test_admin_user_cannot_access_super_admin_panel(): void
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'web2@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Web',
            'slug' => 'conta-web-2',
            'email' => 'web2@example.com',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $conta->usuarios()->attach($user->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($user)
            ->get('/super-admin')
            ->assertRedirect('/admin');
    }

    public function test_panel_post_requests_still_forbid_wrong_profile(): void
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'web-post@example.com',
            'password' => 'password',
            'is_super_admin' => false,
        ]);

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Web Post',
            'slug' => 'conta-web-post',
            'email' => 'web-post@example.com',
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $conta->usuarios()->attach($user->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($user)
            ->post('/super-admin/planos', [
                'nome' => 'Plano Indevido',
                'slug' => 'plano-indevido',
                'valor_mensal' => 10,
                'valor_anual' => 100,
                'status' => 'ativo',
            ])
            ->assertForbidden();
    }
}
