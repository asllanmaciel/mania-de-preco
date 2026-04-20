<?php

namespace Tests\Feature;

use App\Models\AlertaPreco;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Marca;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mobile_catalog_returns_offers_ready_for_app(): void
    {
        [$produto, $loja] = $this->seedProdutoComOfertas();

        $lojaInativa = Loja::create([
            'nome' => 'Loja Inativa',
            'cidade' => 'Curitiba',
            'uf' => 'PR',
            'tipo_loja' => 'fisica',
            'status' => 'inativo',
        ]);

        Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $lojaInativa->id,
            'preco' => 9.99,
            'tipo_preco' => 'pix',
        ]);

        $this->getJson('/api/mobile/v1/ofertas?cidade=Curitiba&ordenar=menor_preco')
            ->assertOk()
            ->assertJsonPath('data.0.nome', 'Café Premium 500g')
            ->assertJsonPath('data.0.resumo.menor_preco', 18.9)
            ->assertJsonPath('data.0.resumo.ofertas', 1)
            ->assertJsonPath('data.0.melhores_ofertas.0.loja.nome', $loja->nome)
            ->assertJsonPath('meta.total', 1);

        $this->assertDatabaseHas('analytics_events', [
            'evento' => 'mobile.catalog.filtered',
            'area' => 'mobile',
        ]);
    }

    public function test_mobile_product_detail_returns_sorted_offers(): void
    {
        [$produto, $loja] = $this->seedProdutoComOfertas();

        $lojaAtacado = Loja::create([
            'nome' => 'Atacado Norte',
            'cidade' => 'Curitiba',
            'uf' => 'PR',
            'tipo_loja' => 'mista',
            'status' => 'ativo',
        ]);

        Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $lojaAtacado->id,
            'preco' => 17.45,
            'tipo_preco' => 'pix',
        ]);

        $this->getJson("/api/mobile/v1/produtos/{$produto->id}")
            ->assertOk()
            ->assertJsonPath('data.nome', 'Café Premium 500g')
            ->assertJsonPath('data.resumo.menor_preco', 17.45)
            ->assertJsonPath('data.resumo.maior_preco', 18.9)
            ->assertJsonPath('data.ofertas.0.loja.nome', $lojaAtacado->nome)
            ->assertJsonPath('data.ofertas.1.loja.nome', $loja->nome);

        $this->assertDatabaseHas('analytics_events', [
            'evento' => 'mobile.product.viewed',
            'area' => 'mobile',
        ]);
    }

    public function test_mobile_store_detail_returns_storefront_payload(): void
    {
        [, $loja] = $this->seedProdutoComOfertas();

        $this->getJson("/api/mobile/v1/lojas/{$loja->id}")
            ->assertOk()
            ->assertJsonPath('data.nome', $loja->nome)
            ->assertJsonPath('data.contato.whatsapp', '41999990000')
            ->assertJsonPath('data.resumo.precos', 1)
            ->assertJsonPath('data.ofertas.0.produto.nome', 'Café Premium 500g');

        $this->assertDatabaseHas('analytics_events', [
            'evento' => 'mobile.store.viewed',
            'area' => 'mobile',
        ]);
    }

    public function test_mobile_customer_register_does_not_create_store_account(): void
    {
        $this->postJson('/api/mobile/v1/register', [
            'name' => 'Cliente Mobile',
            'email' => 'cliente.mobile@example.com',
            'password' => 'senha-segura',
            'password_confirmation' => 'senha-segura',
            'aceite_termos' => true,
        ])
            ->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'perfil']])
            ->assertJsonPath('user.perfil', 'cliente');

        $user = User::where('email', 'cliente.mobile@example.com')->firstOrFail();

        $this->assertSame(0, $user->contas()->count());
        $this->assertDatabaseHas('analytics_events', [
            'evento' => 'mobile.customer_registered',
            'area' => 'mobile',
        ]);
    }

    public function test_mobile_customer_can_manage_price_alerts(): void
    {
        [$produto, $loja] = $this->seedProdutoComOfertas();
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $alertaId = $this->postJson('/api/mobile/v1/alertas', [
            'produto_id' => $produto->id,
            'preco_desejado' => 18.00,
        ])
            ->assertCreated()
            ->assertJsonPath('data.status', 'ativo')
            ->assertJsonPath('data.preco_base', 18.9)
            ->assertJsonPath('data.ultimo_preco_menor', 18.9)
            ->assertJsonPath('data.loja_referencia.id', $loja->id)
            ->json('data.id');

        $this->getJson('/api/mobile/v1/alertas')
            ->assertOk()
            ->assertJsonPath('data.0.id', $alertaId)
            ->assertJsonPath('data.0.produto.nome', $produto->nome);

        $this->patchJson("/api/mobile/v1/alertas/{$alertaId}", [
            'preco_desejado' => 17.50,
            'status' => 'inativo',
        ])
            ->assertOk()
            ->assertJsonPath('data.status', 'inativo')
            ->assertJsonPath('data.preco_desejado', 17.5);

        $this->deleteJson("/api/mobile/v1/alertas/{$alertaId}")
            ->assertNoContent();

        $this->assertDatabaseMissing('alertas_precos', [
            'id' => $alertaId,
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'evento' => 'mobile.price_alert.created',
            'area' => 'mobile',
        ]);
    }

    private function seedProdutoComOfertas(): array
    {
        $categoria = Categoria::create([
            'nome' => 'Mercearia',
            'slug' => 'mercearia',
        ]);

        $marca = Marca::create([
            'nome' => 'Torra Boa',
        ]);

        $produto = Produto::create([
            'nome' => 'Café Premium 500g',
            'slug' => 'cafe-premium-500g',
            'categoria_id' => $categoria->id,
            'marca_id' => $marca->id,
            'descricao' => 'Café torrado e moído para comparar preço no app.',
            'imagem_principal' => 'images/demo/cafe-premium.jpg',
            'status' => 'ativo',
        ]);

        $loja = Loja::create([
            'nome' => 'Mercado Centro',
            'telefone' => '4133330000',
            'whatsapp' => '41999990000',
            'email' => 'contato@mercadocentro.test',
            'cidade' => 'Curitiba',
            'uf' => 'PR',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $preco = Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => 18.90,
            'tipo_preco' => 'dinheiro',
        ]);

        return [$produto, $loja, $preco];
    }
}
