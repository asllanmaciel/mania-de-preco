<?php

namespace Tests\Feature\Web;

use App\Models\Categoria;
use App\Models\AvaliacaoLoja;
use App\Models\ChamadoSuporte;
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
            ->assertSee('Preço bom aparece antes de você perder tempo procurando.')
            ->assertSee('Radar de preços')
            ->assertSee('Janela de economia')
            ->assertSee('favicon.svg')
            ->assertSee('mania-de-preco-mark.svg')
            ->assertSee('data-radar-card', false)
            ->assertSee('radar-precos')
            ->assertSee('Cafe Premium 500g')
            ->assertSee('Loja Centro');
    }

    public function test_public_price_radar_endpoint_returns_market_snapshot(): void
    {
        $this->seedCatalogoDemo();

        $this->getJson(route('radar.precos', ['ordenar' => 'maior_economia']))
            ->assertOk()
            ->assertJsonPath('total_ofertas', 2)
            ->assertJsonPath('total_resultados', 1)
            ->assertJsonPath('lojas_ativas', 2)
            ->assertJsonPath('ranking', 'economia')
            ->assertJsonPath('radar_mercado.0.produto', 'Cafe Premium 500g')
            ->assertJsonStructure([
                'atualizado_em',
                'pulse' => ['path', 'pontos', 'menor', 'maior'],
                'radar_mercado' => [
                    '*' => ['produto', 'loja', 'cidade', 'economia', 'variacao', 'sinal'],
                ],
            ]);
    }

    public function test_public_catalog_can_filter_by_city(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('home', ['cidade' => 'Barueri']))
            ->assertOk()
            ->assertSee('Mania Online')
            ->assertDontSee('Loja Centro');
    }

    public function test_public_catalog_can_filter_by_payment_type(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('home', ['tipo_preco' => 'cartao']))
            ->assertOk()
            ->assertSee('Mania Online')
            ->assertDontSee('Loja Centro');
    }

    public function test_public_catalog_can_filter_by_max_price(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('home', ['preco_ate' => '18.00']))
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
            ->assertSee('O que esperar desta loja')
            ->assertSee('Loja Centro')
            ->assertSee('Excelente atendimento.');
    }

    public function test_public_store_page_handles_empty_catalog_with_recommendations(): void
    {
        [$lojaCentro] = $this->seedCatalogoDemo();

        $lojaSemOfertas = Loja::create([
            'nome' => 'Loja em Preparacao',
            'cidade' => 'Osasco',
            'uf' => 'SP',
            'tipo_loja' => 'fisica',
            'status' => 'ativo',
        ]);

        $this->get(route('lojas.public.show', $lojaSemOfertas))
            ->assertOk()
            ->assertSee('Esta loja ja esta visivel por aqui, mas ainda esta montando as primeiras ofertas para comparacao.')
            ->assertSee('Loja Centro')
            ->assertDontSee('Excelente atendimento.');
    }

    public function test_public_product_page_renders_price_comparison(): void
    {
        [, , $produto] = $this->seedCatalogoDemo();

        $this->get(route('produtos.public.show', $produto))
            ->assertOk()
            ->assertSee('Resumo rápido para decidir')
            ->assertSee('Cafe Premium 500g')
            ->assertSee('Onde comprar agora')
            ->assertSee('/images/demo/produtos/shared/contexto-prateleira.svg');
    }

    public function test_public_project_page_renders_live_product_overview(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('projeto'))
            ->assertOk()
            ->assertSee('Sua loja aparece melhor quando preço, vitrine e operação falam a mesma língua.')
            ->assertSee('lojas com vitrine ativa')
            ->assertSee('Recursos já no ar');
    }

    public function test_public_updates_page_renders_changelog_timeline(): void
    {
        $this->seedCatalogoDemo();

        $this->get(route('novidades.index'))
            ->assertOk()
            ->assertSee('Lançamentos que deixam a experiência melhor para comprar e vender.')
            ->assertSee('Seed demo ampliado e filtros publicos mais fortes');
    }

    public function test_public_updates_detail_page_renders_changelog_content(): void
    {
        $this->seedCatalogoDemo();

        $slug = '2026-04-18_155559-seed-demo-ampliado-e-filtros-publicos-mais-fortes';

        $this->get(route('novidades.show', $slug))
            ->assertOk()
            ->assertSee('Seed demo ampliado e filtros publicos mais fortes')
            ->assertSee('O ambiente demo foi fortalecido para que as novas camadas do produto possam ser vistas no navegador sem cadastro manual.');
    }

    public function test_public_trust_pages_render_launch_confidence_content(): void
    {
        $this->get(route('termos'))
            ->assertOk()
            ->assertSee('Termos de Uso')
            ->assertSee('Regras claras');

        $this->get(route('privacidade'))
            ->assertOk()
            ->assertSee('Privacidade')
            ->assertSee('LGPD');

        $this->get(route('suporte'))
            ->assertOk()
            ->assertSee('Suporte')
            ->assertSee('Quando algo trava');
    }

    public function test_public_support_page_can_open_ticket_with_protocol(): void
    {
        $this->post(route('suporte.chamados.store'), [
            'nome' => 'Cliente em Lancamento',
            'email' => 'cliente-suporte@example.com',
            'telefone' => '(11) 99999-0000',
            'empresa' => 'Mercado Modelo',
            'categoria' => 'catalogo',
            'prioridade' => 'alta',
            'assunto' => 'Preco divergente na vitrine',
            'mensagem' => 'Estou vendo um preco diferente no card publico e preciso de ajuda para corrigir rapidamente.',
            'origem_url' => route('suporte'),
        ])->assertRedirect(route('suporte'))
            ->assertSessionHas('status');

        $chamado = ChamadoSuporte::firstOrFail();

        $this->assertStringStartsWith('MP-', $chamado->protocolo);
        $this->assertSame('novo', $chamado->status);
        $this->assertSame('catalogo', $chamado->categoria);
        $this->assertSame('alta', $chamado->prioridade);
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
            'imagem_principal' => '/images/demo/produtos/cafe-premium-500g.svg',
            'galeria_imagens' => [
                '/images/demo/produtos/shared/contexto-prateleira.svg',
                '/images/demo/produtos/shared/selo-catalogo.svg',
            ],
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
