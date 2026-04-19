<?php

namespace Tests\Feature\Web;

use App\Models\Conta;
use App\Models\ContaFinanceira;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Assinatura;
use App\Models\CategoriaFinanceira;
use App\Models\HistoricoPreco;
use App\Models\MovimentacaoFinanceira;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Plano;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOperationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_store_for_active_account(): void
    {
        [$user] = $this->criarContaComUsuario();

        $response = $this->actingAs($user)->post(route('admin.lojas.store'), [
            'nome' => 'Loja Centro',
            'tipo_loja' => 'mista',
            'status' => 'ativo',
            'cidade' => 'Sao Paulo',
            'uf' => 'sp',
        ]);

        $response->assertRedirect(route('admin.lojas.index'));

        $this->assertDatabaseHas('lojas', [
            'nome' => 'Loja Centro',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'area' => 'lojas',
            'acao' => 'loja_criada',
        ]);
    }

    public function test_plan_limits_block_store_creation_when_store_limit_is_reached(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();
        $this->assinarContaComPlanoLimitado($conta, ['limite_lojas' => 1]);

        Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Matriz',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $response = $this->actingAs($user)->post(route('admin.lojas.store'), [
            'nome' => 'Loja Extra',
            'tipo_loja' => 'mista',
            'status' => 'ativo',
        ]);

        $response
            ->assertRedirect(route('admin.lojas.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('lojas', [
            'conta_id' => $conta->id,
            'nome' => 'Loja Extra',
        ]);
    }

    public function test_authenticated_user_can_open_store_index_page(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Centro',
            'tipo_loja' => 'mista',
            'status' => 'ativo',
        ]);

        $this->actingAs($user)
            ->get(route('admin.lojas.index'))
            ->assertOk()
            ->assertSee('Operacao por loja')
            ->assertSee('Loja Centro');
    }

    public function test_authenticated_user_can_open_finance_page(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Vendas',
            'slug' => 'vendas',
            'tipo' => 'receita',
            'ativa' => true,
        ]);

        $contaFinanceira = ContaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Caixa principal',
            'tipo' => 'caixa',
            'saldo_inicial' => 0,
            'saldo_atual' => 199.90,
            'ativa' => true,
        ]);

        MovimentacaoFinanceira::create([
            'conta_id' => $conta->id,
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'user_id' => $user->id,
            'tipo' => 'receita',
            'origem' => 'manual',
            'descricao' => 'Venda do dia',
            'valor' => 199.90,
            'data_movimentacao' => now(),
            'status' => 'realizada',
        ]);

        $this->actingAs($user)
            ->get(route('admin.financeiro.index'))
            ->assertOk()
            ->assertSee('Centro financeiro')
            ->assertSee('Atalhos de bolso')
            ->assertSee('Venda do dia')
            ->assertSee('Vendas');
    }

    public function test_authenticated_user_can_open_onboarding_page(): void
    {
        [$user] = $this->criarContaComUsuario();

        $this->actingAs($user)
            ->get(route('admin.onboarding'))
            ->assertOk()
            ->assertSee('Onboarding da conta')
            ->assertSee('Completar dados da empresa')
            ->assertSee('Cadastrar a primeira loja')
            ->assertSee('Publicar o primeiro preco');
    }

    public function test_owner_can_manage_account_settings(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $this->actingAs($user)
            ->get(route('admin.configuracoes.edit'))
            ->assertOk()
            ->assertSee('Configuracoes da conta')
            ->assertSee('Dados da empresa');

        $response = $this->actingAs($user)->put(route('admin.configuracoes.update'), [
            'nome_fantasia' => 'Conta Web Premium',
            'razao_social' => 'Conta Web Premium Ltda',
            'documento' => '98.765.432/0001-10',
            'email' => 'premium@example.com',
            'telefone' => '(11) 98888-7777',
            'site' => 'https://premium.example.com',
            'instagram' => '@premium.web',
            'segmento' => 'mercado',
            'porte' => 'media',
            'endereco' => 'Avenida Central',
            'numero' => '123',
            'bairro' => 'Centro',
            'cidade' => 'Sao Paulo',
            'uf' => 'sp',
            'cep' => '01000-000',
            'cor_marca' => '#0f9f8f',
            'descricao_publica' => 'Operacao premium para validacao das configuracoes da conta.',
            'timezone' => 'America/Sao_Paulo',
            'canal_suporte' => 'whatsapp',
            'frequencia_relatorio' => 'semanal',
            'receber_alertas_operacionais' => '1',
        ]);

        $response->assertRedirect(route('admin.configuracoes.edit'));

        $this->assertDatabaseHas('contas', [
            'id' => $conta->id,
            'nome_fantasia' => 'Conta Web Premium',
            'email' => 'premium@example.com',
            'uf' => 'SP',
            'segmento' => 'mercado',
            'porte' => 'media',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'configuracoes',
            'acao' => 'conta_atualizada',
        ]);
    }

    public function test_owner_can_open_subscription_area(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();
        $this->assinarContaComPlanoLimitado($conta, [
            'limite_usuarios' => 5,
            'limite_lojas' => 4,
            'limite_produtos' => 100,
        ]);

        $assinatura = Assinatura::where('conta_id', $conta->id)->firstOrFail();
        $assinatura->update([
            'billing_provider' => 'asaas',
            'billing_status' => 'ACTIVE',
            'billing_checkout_url' => 'https://asaas.test/fatura/123',
            'expira_em' => now()->addDays(20)->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.assinatura'))
            ->assertOk()
            ->assertSee('Assinatura e plano')
            ->assertSee('Plano Limitado')
            ->assertSee('Consumo do plano')
            ->assertSee('Abrir cobranca')
            ->assertSee('Historico comercial');
    }

    public function test_owner_can_manage_team_members(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $this->actingAs($user)
            ->get(route('admin.equipe.index'))
            ->assertOk()
            ->assertSee('Gestao da equipe');

        $response = $this->actingAs($user)->post(route('admin.equipe.store'), [
            'name' => 'Financeiro Conta',
            'email' => 'financeiro@conta-web.test',
            'password' => 'password',
            'papel' => 'financeiro',
            'ativo' => '1',
        ]);

        $response->assertRedirect(route('admin.equipe.index'));

        $membro = User::where('email', 'financeiro@conta-web.test')->firstOrFail();

        $this->assertDatabaseHas('conta_user', [
            'conta_id' => $conta->id,
            'user_id' => $membro->id,
            'papel' => 'financeiro',
            'ativo' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'equipe',
            'acao' => 'membro_adicionado',
        ]);

        $this->actingAs($user)
            ->put(route('admin.equipe.update', $membro), [
                'name' => 'Financeiro Lider',
                'email' => 'financeiro@conta-web.test',
                'papel' => 'gestor',
                'ativo' => '1',
            ])
            ->assertRedirect(route('admin.equipe.edit', $membro));

        $this->assertDatabaseHas('users', [
            'id' => $membro->id,
            'name' => 'Financeiro Lider',
        ]);

        $this->assertDatabaseHas('conta_user', [
            'conta_id' => $conta->id,
            'user_id' => $membro->id,
            'papel' => 'gestor',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'equipe',
            'acao' => 'membro_atualizado',
        ]);
    }

    public function test_plan_limits_block_team_member_creation_when_user_limit_is_reached(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();
        $this->assinarContaComPlanoLimitado($conta, ['limite_usuarios' => 1]);

        $response = $this->actingAs($user)->post(route('admin.equipe.store'), [
            'name' => 'Novo Financeiro',
            'email' => 'novo.financeiro@conta-web.test',
            'password' => 'password',
            'papel' => 'financeiro',
            'ativo' => '1',
        ]);

        $response
            ->assertRedirect(route('admin.equipe.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('users', [
            'email' => 'novo.financeiro@conta-web.test',
        ]);
    }

    public function test_operational_user_cannot_manage_team_members(): void
    {
        [$owner, $conta] = $this->criarContaComUsuario();

        $operador = User::create([
            'name' => 'Operador Conta',
            'email' => 'operador@conta-web.test',
            'password' => 'password',
        ]);

        $conta->usuarios()->attach($operador->id, [
            'papel' => 'operacao',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($operador)
            ->get(route('admin.equipe.index'))
            ->assertForbidden();

        $this->actingAs($operador)
            ->get(route('admin.auditoria'))
            ->assertForbidden();

        $this->actingAs($operador)
            ->get(route('admin.configuracoes.edit'))
            ->assertForbidden();

        $this->actingAs($operador)
            ->get(route('admin.assinatura'))
            ->assertForbidden();
    }

    public function test_owner_can_open_audit_center(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        \App\Models\AuditLog::create([
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'financeiro',
            'acao' => 'lancamento_criado',
            'descricao' => 'Lancamento de teste registrado.',
            'ip' => '127.0.0.1',
        ]);

        $this->actingAs($user)
            ->get(route('admin.auditoria'))
            ->assertOk()
            ->assertSee('Auditoria da conta')
            ->assertSee('Lancamento de teste registrado.')
            ->assertSee('financeiro');
    }

    public function test_financial_user_can_access_finance_but_not_catalog(): void
    {
        [$owner, $conta] = $this->criarContaComUsuario();

        $financeiro = User::create([
            'name' => 'Financeiro Conta',
            'email' => 'financeiro-permissao@conta-web.test',
            'password' => 'password',
        ]);

        $conta->usuarios()->attach($financeiro->id, [
            'papel' => 'financeiro',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($financeiro)
            ->get(route('admin.financeiro.index'))
            ->assertOk()
            ->assertSee('Centro financeiro');

        $this->actingAs($financeiro)
            ->get(route('admin.produtos.index'))
            ->assertForbidden();

        $this->actingAs($financeiro)
            ->get(route('admin.precos.index'))
            ->assertForbidden();
    }

    public function test_catalog_user_can_access_catalog_but_not_finance(): void
    {
        [$owner, $conta] = $this->criarContaComUsuario();

        $catalogo = User::create([
            'name' => 'Catalogo Conta',
            'email' => 'catalogo-permissao@conta-web.test',
            'password' => 'password',
        ]);

        $conta->usuarios()->attach($catalogo->id, [
            'papel' => 'catalogo',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($catalogo)
            ->get(route('admin.produtos.index'))
            ->assertOk()
            ->assertSee('Catalogo de produtos');

        $this->actingAs($catalogo)
            ->get(route('admin.precos.index'))
            ->assertOk()
            ->assertSee('Tabela de precos');

        $this->actingAs($catalogo)
            ->get(route('admin.financeiro.index'))
            ->assertForbidden();
    }

    public function test_viewer_user_only_keeps_dashboard_access(): void
    {
        [$owner, $conta] = $this->criarContaComUsuario();

        $viewer = User::create([
            'name' => 'Viewer Conta',
            'email' => 'viewer-permissao@conta-web.test',
            'password' => 'password',
        ]);

        $conta->usuarios()->attach($viewer->id, [
            'papel' => 'viewer',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        $this->actingAs($viewer)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Abrir financeiro')
            ->assertDontSee('Gerir catalogo');

        $this->actingAs($viewer)
            ->get(route('admin.onboarding'))
            ->assertForbidden();

        $this->actingAs($viewer)
            ->get(route('admin.equipe.index'))
            ->assertForbidden();
    }

    public function test_dashboard_shows_onboarding_banner_when_setup_is_incomplete(): void
    {
        [$user] = $this->criarContaComUsuario();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Onboarding da conta')
            ->assertSee('Saude da conta')
            ->assertSee('Score executivo')
            ->assertSee('Abrir onboarding');
    }

    public function test_finance_page_applies_selected_period_to_recent_movements(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Vendas',
            'slug' => 'vendas',
            'tipo' => 'receita',
            'ativa' => true,
        ]);

        $contaFinanceira = ContaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Banco principal',
            'tipo' => 'banco',
            'saldo_inicial' => 0,
            'saldo_atual' => 0,
            'ativa' => true,
        ]);

        MovimentacaoFinanceira::create([
            'conta_id' => $conta->id,
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'user_id' => $user->id,
            'tipo' => 'receita',
            'origem' => 'manual',
            'descricao' => 'Receita recente',
            'valor' => 350,
            'data_movimentacao' => now()->subMonth(),
            'status' => 'realizada',
        ]);

        MovimentacaoFinanceira::create([
            'conta_id' => $conta->id,
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'user_id' => $user->id,
            'tipo' => 'receita',
            'origem' => 'manual',
            'descricao' => 'Receita historica',
            'valor' => 900,
            'data_movimentacao' => now()->subMonths(10),
            'status' => 'realizada',
        ]);

        $this->actingAs($user)
            ->get(route('admin.financeiro.index', ['periodo' => '3m']))
            ->assertOk()
            ->assertSee('ultimos 3 meses')
            ->assertSee('Receita recente')
            ->assertDontSee('Receita historica');
    }

    public function test_authenticated_user_can_create_financial_account(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $loja = Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Sul',
            'tipo_loja' => 'mista',
            'status' => 'ativo',
        ]);

        $response = $this->actingAs($user)->post(route('admin.financeiro.contas.store'), [
            'loja_id' => $loja->id,
            'nome' => 'Banco principal',
            'tipo' => 'banco',
            'instituicao' => 'Banco Local',
            'saldo_inicial' => 500,
            'saldo_atual' => 650,
            'ativa' => '1',
        ]);

        $response->assertRedirect(route('admin.financeiro.contas.index'));

        $this->assertDatabaseHas('contas_financeiras', [
            'conta_id' => $conta->id,
            'nome' => 'Banco principal',
            'tipo' => 'banco',
            'instituicao' => 'Banco Local',
        ]);
    }

    public function test_authenticated_user_can_create_financial_category(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $response = $this->actingAs($user)->post(route('admin.financeiro.categorias.store'), [
            'nome' => 'Marketing',
            'tipo' => 'despesa',
            'cor' => '#f97316',
            'icone' => 'megaphone',
            'descricao' => 'Investimentos em campanhas e divulgacao.',
            'ativa' => '1',
        ]);

        $response->assertRedirect(route('admin.financeiro.categorias.index'));

        $this->assertDatabaseHas('categorias_financeiras', [
            'conta_id' => $conta->id,
            'nome' => 'Marketing',
            'tipo' => 'despesa',
            'icone' => 'megaphone',
        ]);
    }

    public function test_authenticated_user_can_create_financial_movement(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $contaFinanceira = ContaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Caixa principal',
            'tipo' => 'caixa',
            'saldo_inicial' => 100,
            'saldo_atual' => 100,
            'ativa' => true,
        ]);

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Operacao',
            'slug' => 'operacao',
            'tipo' => 'despesa',
            'ativa' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.financeiro.lancamentos.store'), [
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'tipo' => 'despesa',
            'origem' => 'manual',
            'descricao' => 'Compra de estoque',
            'valor' => 80.50,
            'data_movimentacao' => now()->format('Y-m-d H:i:s'),
            'status' => 'realizada',
        ]);

        $response->assertRedirect(route('admin.financeiro.lancamentos.index'));

        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'conta_id' => $conta->id,
            'conta_financeira_id' => $contaFinanceira->id,
            'descricao' => 'Compra de estoque',
            'tipo' => 'despesa',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'financeiro',
            'acao' => 'lancamento_criado',
        ]);
    }

    public function test_authenticated_user_can_create_account_payable(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Fornecedores',
            'slug' => 'fornecedores',
            'tipo' => 'despesa',
            'ativa' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.financeiro.contas-pagar.store'), [
            'categoria_financeira_id' => $categoria->id,
            'fornecedor_nome' => 'Distribuidora Azul',
            'descricao' => 'Boleto de reposicao',
            'valor_total' => 320.50,
            'valor_pago' => 0,
            'vencimento' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'aberta',
        ]);

        $response->assertRedirect(route('admin.financeiro.contas-pagar.index'));

        $this->assertDatabaseHas('contas_pagar', [
            'conta_id' => $conta->id,
            'descricao' => 'Boleto de reposicao',
            'fornecedor_nome' => 'Distribuidora Azul',
            'status' => 'aberta',
        ]);
    }

    public function test_authenticated_user_can_create_account_receivable(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Cobrancas',
            'slug' => 'cobrancas',
            'tipo' => 'receita',
            'ativa' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.financeiro.contas-receber.store'), [
            'categoria_financeira_id' => $categoria->id,
            'cliente_nome' => 'Mercado Bairro',
            'descricao' => 'Fatura de abril',
            'valor_total' => 890.90,
            'valor_recebido' => 100,
            'vencimento' => now()->addDays(12)->format('Y-m-d'),
            'status' => 'parcial',
        ]);

        $response->assertRedirect(route('admin.financeiro.contas-receber.index'));

        $this->assertDatabaseHas('contas_receber', [
            'conta_id' => $conta->id,
            'descricao' => 'Fatura de abril',
            'cliente_nome' => 'Mercado Bairro',
            'status' => 'parcial',
        ]);
    }

    public function test_paid_account_payable_creates_movement_and_updates_balance(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $contaFinanceira = ContaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Banco operacional',
            'tipo' => 'banco',
            'saldo_inicial' => 1000,
            'saldo_atual' => 1000,
            'ativa' => true,
        ]);

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Fornecedores',
            'slug' => 'fornecedores',
            'tipo' => 'despesa',
            'ativa' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.financeiro.contas-pagar.store'), [
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'fornecedor_nome' => 'Distribuidora Azul',
            'descricao' => 'Pagamento de fornecedor',
            'valor_total' => 250,
            'valor_pago' => 250,
            'vencimento' => now()->format('Y-m-d'),
            'pago_em' => now()->format('Y-m-d H:i:s'),
            'status' => 'paga',
        ]);

        $response->assertRedirect(route('admin.financeiro.contas-pagar.index'));

        $titulo = ContaPagar::first();

        $this->assertNotNull($titulo->movimentacao_financeira_id);
        $this->assertDatabaseHas('movimentacoes_financeiras', [
            'id' => $titulo->movimentacao_financeira_id,
            'conta_financeira_id' => $contaFinanceira->id,
            'tipo' => 'despesa',
            'descricao' => 'Pagamento de fornecedor',
        ]);

        $this->assertEquals('750.00', ContaFinanceira::find($contaFinanceira->id)->saldo_atual);
    }

    public function test_reopening_account_receivable_removes_automatic_movement_and_restores_balance(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $contaFinanceira = ContaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Caixa loja',
            'tipo' => 'caixa',
            'saldo_inicial' => 100,
            'saldo_atual' => 100,
            'ativa' => true,
        ]);

        $categoria = CategoriaFinanceira::create([
            'conta_id' => $conta->id,
            'nome' => 'Recebimentos',
            'slug' => 'recebimentos',
            'tipo' => 'receita',
            'ativa' => true,
        ]);

        $this->actingAs($user)->post(route('admin.financeiro.contas-receber.store'), [
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'cliente_nome' => 'Cliente Ouro',
            'descricao' => 'Recebimento da semana',
            'valor_total' => 300,
            'valor_recebido' => 300,
            'vencimento' => now()->format('Y-m-d'),
            'recebido_em' => now()->format('Y-m-d H:i:s'),
            'status' => 'recebida',
        ]);

        $titulo = ContaReceber::first();
        $movimentacaoId = $titulo->movimentacao_financeira_id;

        $this->assertNotNull($movimentacaoId);
        $this->assertEquals('400.00', ContaFinanceira::find($contaFinanceira->id)->saldo_atual);

        $response = $this->actingAs($user)->put(route('admin.financeiro.contas-receber.update', $titulo), [
            'conta_financeira_id' => $contaFinanceira->id,
            'categoria_financeira_id' => $categoria->id,
            'cliente_nome' => 'Cliente Ouro',
            'descricao' => 'Recebimento da semana',
            'valor_total' => 300,
            'valor_recebido' => 0,
            'vencimento' => now()->format('Y-m-d'),
            'status' => 'aberta',
        ]);

        $response->assertRedirect(route('admin.financeiro.contas-receber.edit', $titulo));

        $this->assertDatabaseMissing('movimentacoes_financeiras', [
            'id' => $movimentacaoId,
        ]);

        $this->assertNull($titulo->fresh()->movimentacao_financeira_id);
        $this->assertEquals('100.00', ContaFinanceira::find($contaFinanceira->id)->saldo_atual);
    }

    public function test_authenticated_user_can_create_product_with_new_category_and_brand(): void
    {
        [$user] = $this->criarContaComUsuario();

        $response = $this->actingAs($user)->post(route('admin.produtos.store'), [
            'nome' => 'Cafe Premium 500g',
            'nova_categoria_nome' => 'Mercearia',
            'nova_marca_nome' => 'Casa do Grao',
            'descricao' => 'Cafe especial para vitrine.',
            'especificacoes_texto' => "Torra media\n500g",
            'imagem_upload' => UploadedFile::fake()->image('cafe.jpg', 600, 600),
            'status' => 'ativo',
        ]);

        $produto = Produto::first();

        $response->assertRedirect(route('admin.produtos.edit', $produto));

        $this->assertDatabaseHas('categorias', [
            'nome' => 'Mercearia',
        ]);

        $this->assertDatabaseHas('marcas', [
            'nome' => 'Casa do Grao',
        ]);

        $this->assertDatabaseHas('produtos', [
            'nome' => 'Cafe Premium 500g',
            'status' => 'ativo',
        ]);

        $this->assertNotNull($produto?->imagem_principal);
        $this->assertStringStartsWith('/images/uploads/produtos/', $produto->imagem_principal);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'area' => 'catalogo',
            'acao' => 'produto_criado',
        ]);
    }

    public function test_authenticated_user_can_create_price_for_own_store(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $loja = Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Norte',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $produto = Produto::create([
            'nome' => 'Arroz Tipo 1',
            'slug' => 'arroz-tipo-1',
            'categoria_id' => \App\Models\Categoria::create([
                'nome' => 'Alimentos',
                'slug' => 'alimentos',
            ])->id,
            'status' => 'ativo',
        ]);

        $response = $this->actingAs($user)->post(route('admin.precos.store'), [
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => 29.90,
            'tipo_preco' => 'pix',
            'url_produto' => 'https://example.com/arroz',
        ]);

        $response->assertRedirect(route('admin.precos.index'));

        $this->assertDatabaseHas('precos', [
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'tipo_preco' => 'pix',
        ]);

        $this->assertDatabaseHas('historicos_precos', [
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'evento' => 'criado',
            'preco_atual' => 29.90,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'conta_id' => $conta->id,
            'user_id' => $user->id,
            'area' => 'precos',
            'acao' => 'preco_criado',
        ]);
    }

    public function test_plan_limits_block_new_product_price_when_product_limit_is_reached(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();
        $this->assinarContaComPlanoLimitado($conta, ['limite_produtos' => 1]);

        $loja = Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Norte',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $categoriaId = \App\Models\Categoria::create([
            'nome' => 'Alimentos',
            'slug' => 'alimentos',
        ])->id;

        $produtoPermitido = Produto::create([
            'nome' => 'Arroz Tipo 1',
            'slug' => 'arroz-tipo-1',
            'categoria_id' => $categoriaId,
            'status' => 'ativo',
        ]);

        $produtoBloqueado = Produto::create([
            'nome' => 'Feijao Tipo 1',
            'slug' => 'feijao-tipo-1',
            'categoria_id' => $categoriaId,
            'status' => 'ativo',
        ]);

        Preco::create([
            'produto_id' => $produtoPermitido->id,
            'loja_id' => $loja->id,
            'preco' => 29.90,
            'tipo_preco' => 'pix',
        ]);

        $response = $this->actingAs($user)->post(route('admin.precos.store'), [
            'produto_id' => $produtoBloqueado->id,
            'loja_id' => $loja->id,
            'preco' => 8.90,
            'tipo_preco' => 'pix',
        ]);

        $response
            ->assertRedirect(route('admin.precos.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('precos', [
            'produto_id' => $produtoBloqueado->id,
            'loja_id' => $loja->id,
        ]);
    }

    public function test_updating_price_registers_history_with_variation(): void
    {
        [$user, $conta] = $this->criarContaComUsuario();

        $loja = Loja::create([
            'conta_id' => $conta->id,
            'nome' => 'Loja Oeste',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $produto = Produto::create([
            'nome' => 'Feijao Carioca',
            'slug' => 'feijao-carioca',
            'categoria_id' => \App\Models\Categoria::create([
                'nome' => 'Mercearia',
                'slug' => 'mercearia',
            ])->id,
            'status' => 'ativo',
        ]);

        $preco = Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => 8.50,
            'tipo_preco' => 'dinheiro',
        ]);

        $response = $this->actingAs($user)->put(route('admin.precos.update', $preco), [
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => 9.20,
            'tipo_preco' => 'pix',
            'url_produto' => 'https://example.com/feijao',
        ]);

        $response->assertRedirect(route('admin.precos.edit', $preco));

        $this->assertSame(2, HistoricoPreco::count());

        $this->assertDatabaseHas('historicos_precos', [
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'evento' => 'atualizado',
            'preco_anterior' => 8.50,
            'preco_atual' => 9.20,
            'variacao_valor' => 0.70,
            'tipo_preco' => 'pix',
        ]);
    }

    private function criarContaComUsuario(): array
    {
        $user = User::create([
            'name' => 'Conta Web',
            'email' => 'web@example.com',
            'password' => 'password',
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

        return [$user, $conta];
    }

    private function assinarContaComPlanoLimitado(Conta $conta, array $limites = []): Plano
    {
        $plano = Plano::create([
            'nome' => 'Plano Limitado',
            'slug' => 'plano-limitado-' . $conta->id,
            'descricao' => 'Plano usado para validar limites operacionais.',
            'valor_mensal' => 49.90,
            'valor_anual' => 499.00,
            'limite_usuarios' => $limites['limite_usuarios'] ?? 3,
            'limite_lojas' => $limites['limite_lojas'] ?? 3,
            'limite_produtos' => $limites['limite_produtos'] ?? 3,
            'status' => 'ativo',
        ]);

        Assinatura::create([
            'conta_id' => $conta->id,
            'plano_id' => $plano->id,
            'status' => 'ativa',
            'ciclo_cobranca' => 'mensal',
            'valor' => 49.90,
            'inicia_em' => now()->toDateString(),
        ]);

        return $plano;
    }
}
