<?php

namespace Tests\Feature\Web;

use App\Models\Categoria;
use App\Models\AvaliacaoLoja;
use App\Models\Loja;
use App\Models\Marca;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_page_renders_with_market_copy(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Encontre o melhor preco com leitura de mercado.')
            ->assertSee('Cafe Premium 500g')
            ->assertSee('Loja Centro');
    }

    public function test_public_catalog_can_filter_by_city(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('home', ['cidade' => 'Barueri']))
            ->assertOk()
            ->assertSee('Mania Online')
            ->assertDontSee('Loja Centro');
    }

    public function test_public_store_page_renders_context_and_offers(): void
    {
        [$lojaCentro] = $this->seedCatalogoDemo();

        $cliente = User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => 'password',
        ]);

        AvaliacaoLoja::create([
            'loja_id' => $lojaCentro->id,
            'user_id' => $cliente->id,
            'nota' => 5,
            'comentario' => 'Excelente atendimento.',
        ]);

        $this->get(route('lojas.public.show', $lojaCentro))
            ->assertOk()
            ->assertSee('Resumo rapido da loja')
            ->assertSee('Loja Centro')
            ->assertSee('Excelente atendimento.');
    }

    private function seedCatalogoDemo(): array
    {
        $categoria = Categoria::create([
            'nome' => 'Mercearia',
            'slug' => 'mercearia',
        ]);

        $marca = Marca::create([
            'nome' => 'Casa do Grao',
        ]);

        $produto = Produto::create([
            'nome' => 'Cafe Premium 500g',
            'slug' => 'cafe-premium-500g',
            'categoria_id' => $categoria->id,
            'marca_id' => $marca->id,
            'descricao' => 'Cafe especial para comparacao.',
            'status' => 'ativo',
        ]);

        $lojaCentro = Loja::create([
            'nome' => 'Loja Centro',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $lojaOnline = Loja::create([
            'nome' => 'Mania Online',
            'cidade' => 'Barueri',
            'uf' => 'SP',
            'tipo_loja' => 'online',
            'status' => 'ativo',
        ]);

        Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $lojaCentro->id,
            'preco' => 18.90,
            'tipo_preco' => 'pix',
        ]);

        Preco::create([
            'produto_id' => $produto->id,
            'loja_id' => $lojaOnline->id,
            'preco' => 17.50,
            'tipo_preco' => 'cartao',
        ]);

        return [$lojaCentro, $lojaOnline, $produto];
    }
}
