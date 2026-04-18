<?php

namespace Tests\Feature\Web;

use App\Models\Conta;
use App\Models\ContaFinanceira;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\CategoriaFinanceira;
use App\Models\MovimentacaoFinanceira;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
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
            ->assertSee('Venda do dia')
            ->assertSee('Vendas');
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

    public function test_authenticated_user_can_create_product_with_new_category_and_brand(): void
    {
        [$user] = $this->criarContaComUsuario();

        $response = $this->actingAs($user)->post(route('admin.produtos.store'), [
            'nome' => 'Cafe Premium 500g',
            'nova_categoria_nome' => 'Mercearia',
            'nova_marca_nome' => 'Casa do Grao',
            'descricao' => 'Cafe especial para vitrine.',
            'especificacoes_texto' => "Torra media\n500g",
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
}
