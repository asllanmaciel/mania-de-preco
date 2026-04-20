<?php

namespace Tests\Feature\Web;

use App\Models\AlertaPreco;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_as_customer_and_open_customer_area(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Conta gratuita de consumidor')
            ->assertSee('Termos de Uso')
            ->assertSee('Política de Privacidade');

        $this->post(route('register.store'), [
            'name' => 'Cliente Lancamento',
            'email' => 'cliente-lancamento@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'aceite_termos' => '1',
        ])->assertRedirect(route('cliente.dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'cliente-lancamento@example.com',
            'is_super_admin' => false,
        ]);

        $user = User::where('email', 'cliente-lancamento@example.com')->firstOrFail();

        $this->assertNotNull($user->termos_aceitos_em);
        $this->assertSame(config('legal.termos_versao'), $user->termos_versao);
        $this->assertSame(config('legal.privacidade_versao'), $user->privacidade_versao);

        $this->get(route('cliente.dashboard'))
            ->assertOk()
            ->assertSee('Seu radar pessoal de bons precos')
            ->assertSee('Criar alerta');
    }

    public function test_customer_registration_requires_terms_acceptance(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Cliente Sem Aceite',
            'email' => 'cliente-sem-aceite@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('aceite_termos');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'cliente-sem-aceite@example.com',
        ]);
    }

    public function test_customer_can_create_update_and_remove_price_alert_from_web_panel(): void
    {
        [$user, $produto, $loja] = $this->seedProdutoComOferta(18.90);

        $this->actingAs($user)
            ->post(route('cliente.alertas.store'), [
                'produto_id' => $produto->id,
                'preco_desejado' => 18.00,
            ])
            ->assertRedirect(route('cliente.dashboard'));

        $alerta = AlertaPreco::firstOrFail();

        $this->assertSame($user->id, $alerta->user_id);
        $this->assertSame($produto->id, $alerta->produto_id);
        $this->assertSame($loja->id, $alerta->loja_id_referencia);
        $this->assertSame('18.90', number_format((float) $alerta->ultimo_preco_menor, 2, '.', ''));

        $this->actingAs($user)
            ->patch(route('cliente.alertas.update', $alerta), [
                'preco_desejado' => 17.50,
                'status' => 'inativo',
            ])
            ->assertRedirect(route('cliente.dashboard'));

        $alerta->refresh();

        $this->assertSame('inativo', $alerta->status);
        $this->assertSame('17.50', number_format((float) $alerta->preco_desejado, 2, '.', ''));

        $this->actingAs($user)
            ->delete(route('cliente.alertas.destroy', $alerta))
            ->assertRedirect(route('cliente.dashboard'));

        $this->assertDatabaseMissing('alertas_precos', [
            'id' => $alerta->id,
        ]);
    }

    public function test_customer_can_open_notifications_center_with_price_alerts(): void
    {
        [$user, $produto] = $this->seedProdutoComOferta(17.90);

        AlertaPreco::create([
            'user_id' => $user->id,
            'produto_id' => $produto->id,
            'preco_desejado' => 18.00,
            'status' => 'ativo',
        ]);

        $this->actingAs($user)
            ->get(route('cliente.notificacoes'))
            ->assertOk()
            ->assertSee('Alertas que merecem sua atencao')
            ->assertSee('Preco bateu sua meta')
            ->assertSee('Marcar vista');

        $alerta = AlertaPreco::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)
            ->patch(route('cliente.notificacoes.interagir'), [
                'chave' => 'cliente.alerta_atendido.' . $alerta->id,
                'acao' => 'ler',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('notificacao_interacoes', [
            'user_id' => $user->id,
            'contexto' => 'cliente',
            'chave' => 'cliente.alerta_atendido.' . $alerta->id,
        ]);
    }

    public function test_public_product_page_connects_to_customer_alert_flow(): void
    {
        [$user, $produto] = $this->seedProdutoComOferta(24.90);

        $this->get(route('produtos.public.show', $produto))
            ->assertOk()
            ->assertSee('Criar conta e ativar alerta');

        $this->actingAs($user)
            ->get(route('produtos.public.show', $produto))
            ->assertOk()
            ->assertSee('Criar alerta de preco')
            ->assertSee(route('cliente.alertas.store'), false);
    }

    public function test_customer_cannot_update_alert_from_another_user(): void
    {
        [$owner, $produto] = $this->seedProdutoComOferta(18.90);
        $intruso = User::create([
            'name' => 'Cliente Intruso',
            'email' => 'intruso@example.com',
            'password' => 'password',
        ]);

        $alerta = AlertaPreco::create([
            'user_id' => $owner->id,
            'produto_id' => $produto->id,
            'preco_desejado' => 18.00,
            'status' => 'ativo',
        ]);

        $this->actingAs($intruso)
            ->patch(route('cliente.alertas.update', $alerta), [
                'preco_desejado' => 17.00,
                'status' => 'ativo',
            ])
            ->assertForbidden();
    }

    private function seedProdutoComOferta(float $precoValor): array
    {
        $user = User::create([
            'name' => 'Cliente Alertas',
            'email' => 'cliente-alertas@example.com',
            'password' => 'password',
        ]);

        $produto = Produto::create([
            'nome' => 'Cafe Premium 500g',
            'slug' => 'cafe-premium-500g',
            'categoria_id' => Categoria::create([
                'nome' => 'Mercearia',
                'slug' => 'mercearia',
            ])->id,
            'status' => 'ativo',
        ]);

        $loja = Loja::create([
            'nome' => 'Loja Centro',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => $precoValor,
            'tipo_preco' => 'dinheiro',
        ]);

        return [$user, $produto, $loja];
    }
}
