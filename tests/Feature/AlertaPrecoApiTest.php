<?php

namespace Tests\Feature;

use App\Models\AlertaPreco;
use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertaPrecoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_alert_captures_current_market_reference(): void
    {
        [$user, $produto, $loja] = $this->seedProdutoComOferta(18.90);

        $this->actingAs($user)
            ->postJson('/api/alertas', [
                'produto_id' => $produto->id,
                'preco_desejado' => 18.00,
            ])
            ->assertCreated()
            ->assertJsonPath('status', 'ativo')
            ->assertJsonPath('preco_base', '18.90')
            ->assertJsonPath('ultimo_preco_menor', '18.90')
            ->assertJsonPath('loja_id_referencia', $loja->id);
    }

    public function test_alert_becomes_attended_when_price_reaches_target(): void
    {
        [$user, $produto, $loja, $preco] = $this->seedProdutoComOferta(19.90);

        $alerta = AlertaPreco::create([
            'user_id' => $user->id,
            'produto_id' => $produto->id,
            'preco_desejado' => 18.50,
            'status' => 'ativo',
        ]);

        $preco->update([
            'preco' => 17.80,
            'tipo_preco' => 'pix',
        ]);

        $this->assertDatabaseHas('alertas_precos', [
            'id' => $alerta->id,
            'status' => 'atendido',
            'loja_id_referencia' => $loja->id,
            'ultimo_preco_menor' => 17.80,
            'menor_preco_historico' => 17.80,
            'variacao_desde_ativacao' => -2.10,
            'variacao_percentual_desde_ativacao' => -10.55,
        ]);

        $this->assertNotNull($alerta->fresh()->disparado_em);
    }

    public function test_attended_alert_returns_to_active_when_market_rises_again(): void
    {
        [$user, $produto, , $preco] = $this->seedProdutoComOferta(17.40);

        $alerta = $this->actingAs($user)
            ->postJson('/api/alertas', [
                'produto_id' => $produto->id,
                'preco_desejado' => 18.00,
            ])
            ->assertCreated()
            ->json();

        $this->assertSame('atendido', $alerta['status']);

        $preco->update([
            'preco' => 19.30,
            'tipo_preco' => 'cartao',
        ]);

        $this->assertDatabaseHas('alertas_precos', [
            'id' => $alerta['id'],
            'status' => 'ativo',
            'ultimo_preco_menor' => 19.30,
            'menor_preco_historico' => 17.40,
        ]);
    }

    private function seedProdutoComOferta(float $precoValor): array
    {
        $user = User::create([
            'name' => 'Cliente Alertas',
            'email' => 'alertas@example.com',
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

        $preco = Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $loja->id,
            'preco' => $precoValor,
            'tipo_preco' => 'dinheiro',
        ]);

        return [$user, $produto, $loja, $preco];
    }
}
