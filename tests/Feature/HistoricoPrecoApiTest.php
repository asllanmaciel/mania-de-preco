<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoricoPrecoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_product_price_history_endpoint_returns_timeline(): void
    {
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
            'preco' => 18.90,
            'tipo_preco' => 'pix',
        ]);

        $preco->update([
            'preco' => 17.40,
            'tipo_preco' => 'cartao',
        ]);

        $this->getJson("/api/produtos/{$produto->id}/historico-precos")
            ->assertOk()
            ->assertJsonPath('produto.nome', 'Cafe Premium 500g')
            ->assertJsonPath('resumo.total_registros', 2)
            ->assertJsonPath('resumo.ultimo_preco', '17.40')
            ->assertJsonPath('timeline.0.evento', 'criado')
            ->assertJsonPath('timeline.1.evento', 'atualizado')
            ->assertJsonPath('timeline.1.variacao_valor', '-1.50');
    }
}
