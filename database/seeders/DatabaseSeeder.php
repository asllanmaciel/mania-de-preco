<?php

namespace Database\Seeders;

use App\Models\Assinatura;
use App\Models\AvaliacaoLoja;
use App\Models\Categoria;
use App\Models\CategoriaFinanceira;
use App\Models\Conta;
use App\Models\ContaFinanceira;
use App\Models\ContaPagar;
use App\Models\ContaReceber;
use App\Models\Loja;
use App\Models\Marca;
use App\Models\MovimentacaoFinanceira;
use App\Models\Plano;
use App\Models\Preco;
use App\Models\Produto;
use App\Models\User;
use App\Services\Financeiro\TituloFinanceiroSynchronizer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $synchronizer = app(TituloFinanceiroSynchronizer::class);

            $owner = User::updateOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Conta Demo',
                    'password' => Hash::make('password'),
                ]
            );

            $cliente = User::updateOrCreate(
                ['email' => 'cliente.demo@maniadepreco.com.br'],
                [
                    'name' => 'Cliente Demo',
                    'password' => Hash::make('password'),
                ]
            );

            $clienteDois = User::updateOrCreate(
                ['email' => 'compradora.demo@maniadepreco.com.br'],
                [
                    'name' => 'Compradora Demo',
                    'password' => Hash::make('password'),
                ]
            );

            $clienteTres = User::updateOrCreate(
                ['email' => 'familia.demo@maniadepreco.com.br'],
                [
                    'name' => 'Familia Demo',
                    'password' => Hash::make('password'),
                ]
            );

            $conta = Conta::updateOrCreate(
                ['slug' => 'conta-demo'],
                [
                    'nome_fantasia' => 'Conta Demo',
                    'email' => $owner->email,
                    'telefone' => '(11) 4000-1122',
                    'status' => 'ativo',
                    'trial_ends_at' => now()->addDays(14),
                ]
            );

            $conta->usuarios()->syncWithoutDetaching([
                $owner->id => [
                    'papel' => 'owner',
                    'ativo' => true,
                    'ultimo_acesso_em' => now(),
                ],
            ]);

            $plano = Plano::updateOrCreate(
                ['slug' => 'starter'],
                [
                    'nome' => 'Starter',
                    'descricao' => 'Plano inicial para pequenas lojas.',
                    'valor_mensal' => 49.90,
                    'valor_anual' => 499.00,
                    'limite_usuarios' => 3,
                    'limite_lojas' => 3,
                    'limite_produtos' => 800,
                    'recursos' => [
                        'controle_financeiro',
                        'catalogo_publico',
                        'comparador_de_precos',
                        'operacao_web',
                    ],
                    'status' => 'ativo',
                ]
            );

            Assinatura::updateOrCreate(
                ['conta_id' => $conta->id, 'plano_id' => $plano->id],
                [
                    'status' => 'ativa',
                    'ciclo_cobranca' => 'mensal',
                    'valor' => 49.90,
                    'inicia_em' => now()->subDays(20)->toDateString(),
                    'expira_em' => now()->addDays(10)->toDateString(),
                    'observacoes' => 'Base demo para navegacao do painel administrativo.',
                ]
            );

            $categoriasFinanceiras = $this->seedCategoriasFinanceiras($conta);
            $lojas = $this->seedLojas($conta);
            $contasFinanceiras = $this->seedContasFinanceiras($conta, $lojas);
            $catalogo = $this->seedCatalogo();

            $this->seedProdutosEPrecos($catalogo, $lojas);
            $this->seedAvaliacoes($lojas, [$cliente, $clienteDois, $clienteTres]);
            $this->seedMovimentacoes($conta, $owner, $categoriasFinanceiras, $contasFinanceiras, $lojas);
            $this->seedTitulos($conta, $owner, $categoriasFinanceiras, $contasFinanceiras, $lojas, $synchronizer);
            $this->recalcularSaldos($conta);
        });
    }

    private function seedCategoriasFinanceiras(Conta $conta): array
    {
        $categorias = [];

        foreach ([
            ['nome' => 'Vendas em Loja', 'slug' => 'vendas-em-loja', 'tipo' => 'receita', 'cor' => '#15803d', 'descricao' => 'Entradas vindas do atendimento presencial.'],
            ['nome' => 'Receitas Online', 'slug' => 'receitas-online', 'tipo' => 'receita', 'cor' => '#0f766e', 'descricao' => 'Entradas vindas de canais digitais.'],
            ['nome' => 'Fornecedores', 'slug' => 'fornecedores', 'tipo' => 'despesa', 'cor' => '#b91c1c', 'descricao' => 'Pagamentos de reposicao e compra de mercadoria.'],
            ['nome' => 'Operacao Loja', 'slug' => 'operacao-loja', 'tipo' => 'despesa', 'cor' => '#f97316', 'descricao' => 'Custos operacionais do dia a dia.'],
            ['nome' => 'Marketing', 'slug' => 'marketing', 'tipo' => 'despesa', 'cor' => '#7c3aed', 'descricao' => 'Investimentos em campanhas e divulgacao.'],
        ] as $dados) {
            $categorias[$dados['slug']] = CategoriaFinanceira::updateOrCreate(
                ['conta_id' => $conta->id, 'slug' => $dados['slug']],
                [
                    'nome' => $dados['nome'],
                    'tipo' => $dados['tipo'],
                    'cor' => $dados['cor'],
                    'descricao' => $dados['descricao'],
                    'ativa' => true,
                ]
            );
        }

        return $categorias;
    }

    private function seedLojas(Conta $conta): array
    {
        $lojas = [];

        foreach ([
            [
                'key' => 'centro',
                'nome' => 'Loja Centro',
                'email' => 'centro@maniadepreco.com.br',
                'telefone' => '(11) 3232-1000',
                'whatsapp' => '(11) 99999-1000',
                'site' => 'https://centro.demo.maniadepreco.com.br',
                'instagram' => '@lojacentro.demo',
                'cidade' => 'Sao Paulo',
                'uf' => 'SP',
                'bairro' => 'Republica',
                'endereco' => 'Avenida Ipiranga',
                'numero' => '115',
                'cep' => '01046-010',
                'tipo_loja' => 'fisica',
                'status' => 'ativo',
            ],
            [
                'key' => 'express',
                'nome' => 'Emporio Express',
                'email' => 'express@maniadepreco.com.br',
                'telefone' => '(11) 3555-2200',
                'whatsapp' => '(11) 99999-2200',
                'site' => 'https://express.demo.maniadepreco.com.br',
                'instagram' => '@emporioexpress.demo',
                'cidade' => 'Sao Paulo',
                'uf' => 'SP',
                'bairro' => 'Pinheiros',
                'endereco' => 'Rua dos Pinheiros',
                'numero' => '848',
                'cep' => '05422-001',
                'tipo_loja' => 'mista',
                'status' => 'ativo',
            ],
            [
                'key' => 'online',
                'nome' => 'Mania Online',
                'email' => 'online@maniadepreco.com.br',
                'telefone' => '(11) 3777-3300',
                'whatsapp' => '(11) 99999-3300',
                'site' => 'https://online.demo.maniadepreco.com.br',
                'instagram' => '@maniaonline.demo',
                'cidade' => 'Barueri',
                'uf' => 'SP',
                'bairro' => 'Alphaville',
                'endereco' => 'Alameda Rio Negro',
                'numero' => '503',
                'cep' => '06454-000',
                'tipo_loja' => 'online',
                'status' => 'ativo',
            ],
            [
                'key' => 'bairro',
                'nome' => 'Mercado do Bairro',
                'email' => 'bairro@maniadepreco.com.br',
                'telefone' => '(11) 4111-4400',
                'whatsapp' => '(11) 99999-4400',
                'site' => 'https://bairro.demo.maniadepreco.com.br',
                'instagram' => '@mercadodobairro.demo',
                'cidade' => 'Osasco',
                'uf' => 'SP',
                'bairro' => 'Vila Yara',
                'endereco' => 'Avenida dos Autonomistas',
                'numero' => '2300',
                'cep' => '06020-012',
                'tipo_loja' => 'fisica',
                'status' => 'ativo',
            ],
        ] as $dados) {
            $lojas[$dados['key']] = Loja::updateOrCreate(
                ['conta_id' => $conta->id, 'nome' => $dados['nome']],
                [
                    'email' => $dados['email'],
                    'telefone' => $dados['telefone'],
                    'whatsapp' => $dados['whatsapp'],
                    'site' => $dados['site'],
                    'instagram' => $dados['instagram'],
                    'cidade' => $dados['cidade'],
                    'uf' => $dados['uf'],
                    'bairro' => $dados['bairro'],
                    'endereco' => $dados['endereco'],
                    'numero' => $dados['numero'],
                    'cep' => $dados['cep'],
                    'tipo_loja' => $dados['tipo_loja'],
                    'status' => $dados['status'],
                ]
            );
        }

        return $lojas;
    }

    private function seedContasFinanceiras(Conta $conta, array $lojas): array
    {
        $contas = [];

        foreach ([
            [
                'key' => 'caixa-centro',
                'nome' => 'Caixa Loja Centro',
                'tipo' => 'caixa',
                'loja_id' => $lojas['centro']->id,
                'saldo_inicial' => 800.00,
                'saldo_atual' => 800.00,
            ],
            [
                'key' => 'banco-operacional',
                'nome' => 'Banco Operacional',
                'tipo' => 'banco',
                'instituicao' => 'Banco Local',
                'saldo_inicial' => 4500.00,
                'saldo_atual' => 4500.00,
            ],
            [
                'key' => 'carteira-online',
                'nome' => 'Carteira Online',
                'tipo' => 'carteira_digital',
                'loja_id' => $lojas['online']->id,
                'instituicao' => 'Gateway Pay',
                'saldo_inicial' => 1200.00,
                'saldo_atual' => 1200.00,
            ],
        ] as $dados) {
            $contas[$dados['key']] = ContaFinanceira::updateOrCreate(
                ['conta_id' => $conta->id, 'nome' => $dados['nome']],
                [
                    'loja_id' => $dados['loja_id'] ?? null,
                    'tipo' => $dados['tipo'],
                    'instituicao' => $dados['instituicao'] ?? null,
                    'saldo_inicial' => $dados['saldo_inicial'],
                    'saldo_atual' => $dados['saldo_atual'],
                    'ativa' => true,
                ]
            );
        }

        return $contas;
    }

    private function seedCatalogo(): array
    {
        $categorias = [];
        $marcas = [];

        foreach ([
            ['nome' => 'Mercearia', 'slug' => 'mercearia'],
            ['nome' => 'Bebidas', 'slug' => 'bebidas'],
            ['nome' => 'Limpeza', 'slug' => 'limpeza'],
            ['nome' => 'Higiene', 'slug' => 'higiene'],
            ['nome' => 'Pet', 'slug' => 'pet'],
            ['nome' => 'Conveniencia', 'slug' => 'conveniencia'],
        ] as $dados) {
            $categorias[$dados['slug']] = Categoria::updateOrCreate(
                ['slug' => $dados['slug']],
                [
                    'nome' => $dados['nome'],
                    'descricao' => 'Categoria demo para navegação do painel.',
                ]
            );
        }

        foreach (['Casa do Grao', 'Serra Verde', 'Limpax', 'Brilho Max', 'Vita Care', 'Pet Feliz', 'Noite Leve'] as $nome) {
            $marcas[Str::slug($nome)] = Marca::updateOrCreate(
                ['nome' => $nome],
                ['logo' => null]
            );
        }

        return compact('categorias', 'marcas');
    }

    private function seedProdutosEPrecos(array $catalogo, array $lojas): void
    {
        $produtos = [];

        foreach ([
            [
                'nome' => 'Cafe Premium 500g',
                'slug' => 'cafe-premium-500g',
                'categoria' => 'mercearia',
                'marca' => 'casa-do-grao',
                'descricao' => 'Cafe especial para consumo diario com boa margem de revenda.',
                'especificacoes' => ['Torra media', 'Pacote 500g', 'Linha premium'],
                'imagem_principal' => '/images/demo/produtos/cafe-premium-500g.svg',
            ],
            [
                'nome' => 'Arroz Tipo 1 5kg',
                'slug' => 'arroz-tipo-1-5kg',
                'categoria' => 'mercearia',
                'marca' => 'serra-verde',
                'descricao' => 'Item de alto giro usado para comparação de preço entre lojas.',
                'especificacoes' => ['Pacote 5kg', 'Tipo 1', 'Linha economica'],
                'imagem_principal' => '/images/demo/produtos/arroz-tipo-1-5kg.svg',
            ],
            [
                'nome' => 'Detergente Neutro 500ml',
                'slug' => 'detergente-neutro-500ml',
                'categoria' => 'limpeza',
                'marca' => 'limpax',
                'descricao' => 'Produto recorrente na seção de limpeza.',
                'especificacoes' => ['500ml', 'Uso domestico'],
                'imagem_principal' => '/images/demo/produtos/detergente-neutro-500ml.svg',
            ],
            [
                'nome' => 'Agua Mineral 1,5L',
                'slug' => 'agua-mineral-1-5l',
                'categoria' => 'bebidas',
                'marca' => 'serra-verde',
                'descricao' => 'Produto simples para validar comparação rápida.',
                'especificacoes' => ['Garrafa 1,5L', 'Sem gas'],
                'imagem_principal' => '/images/demo/produtos/agua-mineral-1-5l.svg',
            ],
            [
                'nome' => 'Shampoo Uso Diario 400ml',
                'slug' => 'shampoo-uso-diario-400ml',
                'categoria' => 'higiene',
                'marca' => 'vita-care',
                'descricao' => 'Produto de higiene para compor o catálogo da conta demo.',
                'especificacoes' => ['400ml', 'Uso diario'],
                'imagem_principal' => '/images/demo/produtos/shampoo-uso-diario-400ml.svg',
            ],
            [
                'nome' => 'Desinfetante Lavanda 2L',
                'slug' => 'desinfetante-lavanda-2l',
                'categoria' => 'limpeza',
                'marca' => 'brilho-max',
                'descricao' => 'Exemplo de item com preço competitivo em dois canais.',
                'especificacoes' => ['2L', 'Fragrancia lavanda'],
                'imagem_principal' => '/images/demo/produtos/desinfetante-lavanda-2l.svg',
            ],
            [
                'nome' => 'Racao Premium Caes 10kg',
                'slug' => 'racao-premium-caes-10kg',
                'categoria' => 'pet',
                'marca' => 'pet-feliz',
                'descricao' => 'Produto de ticket mais alto para destacar variacao e economia.',
                'especificacoes' => ['10kg', 'Adultos', 'Sabor carne'],
                'imagem_principal' => '/images/demo/produtos/racao-premium-caes-10kg.svg',
            ],
            [
                'nome' => 'Biscoito Integral 140g',
                'slug' => 'biscoito-integral-140g',
                'categoria' => 'conveniencia',
                'marca' => 'noite-leve',
                'descricao' => 'Item de conveniencia com disputa de preco mais apertada.',
                'especificacoes' => ['140g', 'Integral'],
                'imagem_principal' => '/images/demo/produtos/biscoito-integral-140g.svg',
            ],
        ] as $dados) {
            $produtos[$dados['slug']] = Produto::updateOrCreate(
                ['slug' => $dados['slug']],
                [
                    'nome' => $dados['nome'],
                    'categoria_id' => $catalogo['categorias'][$dados['categoria']]->id,
                    'marca_id' => $catalogo['marcas'][$dados['marca']]->id,
                    'descricao' => $dados['descricao'],
                    'especificacoes' => $dados['especificacoes'],
                    'imagem_principal' => $dados['imagem_principal'],
                    'status' => 'ativo',
                ]
            );
        }

        foreach ([
            [$produtos['cafe-premium-500g'], $lojas['centro'], 18.90, 'pix'],
            [$produtos['cafe-premium-500g'], $lojas['express'], 19.50, 'cartao'],
            [$produtos['arroz-tipo-1-5kg'], $lojas['centro'], 29.90, 'dinheiro'],
            [$produtos['arroz-tipo-1-5kg'], $lojas['online'], 27.90, 'pix'],
            [$produtos['detergente-neutro-500ml'], $lojas['express'], 3.99, 'pix'],
            [$produtos['detergente-neutro-500ml'], $lojas['online'], 4.29, 'cartao'],
            [$produtos['agua-mineral-1-5l'], $lojas['centro'], 2.79, 'dinheiro'],
            [$produtos['agua-mineral-1-5l'], $lojas['express'], 3.19, 'pix'],
            [$produtos['shampoo-uso-diario-400ml'], $lojas['online'], 15.90, 'cartao'],
            [$produtos['shampoo-uso-diario-400ml'], $lojas['express'], 16.40, 'pix'],
            [$produtos['desinfetante-lavanda-2l'], $lojas['centro'], 8.49, 'pix'],
            [$produtos['desinfetante-lavanda-2l'], $lojas['online'], 7.99, 'boleto'],
            [$produtos['desinfetante-lavanda-2l'], $lojas['bairro'], 8.19, 'dinheiro'],
            [$produtos['cafe-premium-500g'], $lojas['bairro'], 18.40, 'dinheiro'],
            [$produtos['arroz-tipo-1-5kg'], $lojas['bairro'], 28.40, 'pix'],
            [$produtos['detergente-neutro-500ml'], $lojas['bairro'], 4.09, 'dinheiro'],
            [$produtos['agua-mineral-1-5l'], $lojas['online'], 2.69, 'boleto'],
            [$produtos['racao-premium-caes-10kg'], $lojas['online'], 109.90, 'cartao'],
            [$produtos['racao-premium-caes-10kg'], $lojas['bairro'], 104.90, 'pix'],
            [$produtos['racao-premium-caes-10kg'], $lojas['express'], 112.50, 'cartao'],
            [$produtos['biscoito-integral-140g'], $lojas['centro'], 6.20, 'pix'],
            [$produtos['biscoito-integral-140g'], $lojas['express'], 6.49, 'cartao'],
            [$produtos['biscoito-integral-140g'], $lojas['online'], 5.89, 'boleto'],
        ] as [$produto, $loja, $preco, $tipo]) {
            Preco::updateOrCreate(
                [
                    'produto_id' => $produto->id,
                    'loja_id' => $loja->id,
                    'tipo_preco' => $tipo,
                ],
                [
                    'preco' => $preco,
                    'url_produto' => 'https://demo.maniadepreco.com.br/' . $produto->slug,
                ]
            );
        }
    }

    private function seedAvaliacoes(array $lojas, array $clientes): void
    {
        foreach ([
            [$lojas['centro'], $clientes[0], 5, 'Loja organizada e com bom atendimento.'],
            [$lojas['express'], $clientes[0], 4, 'Entrega rapida e preco competitivo em itens basicos.'],
            [$lojas['online'], $clientes[1], 5, 'Fluxo de compra simples e bons precos no digital.'],
            [$lojas['bairro'], $clientes[2], 4, 'Boa variedade e precos honestos para compra rapida.'],
            [$lojas['centro'], $clientes[1], 4, 'Encontrei promocoes fortes em itens de giro.'],
        ] as [$loja, $cliente, $nota, $comentario]) {
            AvaliacaoLoja::updateOrCreate(
                [
                    'loja_id' => $loja->id,
                    'user_id' => $cliente->id,
                ],
                [
                    'nota' => $nota,
                    'comentario' => $comentario,
                ]
            );
        }
    }

    private function seedMovimentacoes(
        Conta $conta,
        User $owner,
        array $categoriasFinanceiras,
        array $contasFinanceiras,
        array $lojas
    ): void {
        foreach ([
            [
                'descricao' => 'Venda de balcão manha',
                'tipo' => 'receita',
                'origem' => 'venda',
                'valor' => 680.00,
                'data_movimentacao' => now()->subDays(4),
                'conta_financeira_id' => $contasFinanceiras['caixa-centro']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['vendas-em-loja']->id,
                'loja_id' => $lojas['centro']->id,
            ],
            [
                'descricao' => 'Receita marketplace semanal',
                'tipo' => 'receita',
                'origem' => 'venda',
                'valor' => 1240.00,
                'data_movimentacao' => now()->subDays(3),
                'conta_financeira_id' => $contasFinanceiras['carteira-online']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['receitas-online']->id,
                'loja_id' => $lojas['online']->id,
            ],
            [
                'descricao' => 'Campanha de bairro',
                'tipo' => 'despesa',
                'origem' => 'ajuste',
                'valor' => 180.00,
                'data_movimentacao' => now()->subDays(2),
                'conta_financeira_id' => $contasFinanceiras['banco-operacional']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['marketing']->id,
                'loja_id' => $lojas['express']->id,
            ],
            [
                'descricao' => 'Reposicao rapida de loja',
                'tipo' => 'despesa',
                'origem' => 'pagamento',
                'valor' => 320.00,
                'data_movimentacao' => now()->subDay(),
                'conta_financeira_id' => $contasFinanceiras['banco-operacional']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['operacao-loja']->id,
                'loja_id' => $lojas['centro']->id,
            ],
        ] as $dados) {
            MovimentacaoFinanceira::updateOrCreate(
                [
                    'conta_id' => $conta->id,
                    'descricao' => $dados['descricao'],
                    'tipo' => $dados['tipo'],
                ],
                [
                    'loja_id' => $dados['loja_id'],
                    'conta_financeira_id' => $dados['conta_financeira_id'],
                    'categoria_financeira_id' => $dados['categoria_financeira_id'],
                    'user_id' => $owner->id,
                    'origem' => $dados['origem'],
                    'valor' => $dados['valor'],
                    'data_movimentacao' => $dados['data_movimentacao'],
                    'status' => 'realizada',
                    'observacoes' => 'Movimentacao demo para o painel.',
                    'metadados' => ['seed_demo' => true],
                ]
            );
        }
    }

    private function seedTitulos(
        Conta $conta,
        User $owner,
        array $categoriasFinanceiras,
        array $contasFinanceiras,
        array $lojas,
        TituloFinanceiroSynchronizer $synchronizer
    ): void {
        $contaPagarAberta = ContaPagar::updateOrCreate(
            ['conta_id' => $conta->id, 'descricao' => 'Aluguel da unidade centro'],
            [
                'loja_id' => $lojas['centro']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['operacao-loja']->id,
                'fornecedor_nome' => 'Imobiliaria Centro',
                'valor_total' => 2500.00,
                'valor_pago' => 0,
                'vencimento' => now()->addDays(6)->toDateString(),
                'pagamento_previsto_em' => now()->addDays(5)->toDateString(),
                'status' => 'aberta',
                'observacoes' => 'Compromisso fixo do ponto comercial.',
                'conta_financeira_id' => null,
            ]
        );

        $contaPagarPaga = ContaPagar::updateOrCreate(
            ['conta_id' => $conta->id, 'descricao' => 'Pagamento fornecedor cafe'],
            [
                'loja_id' => $lojas['centro']->id,
                'conta_financeira_id' => $contasFinanceiras['banco-operacional']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['fornecedores']->id,
                'fornecedor_nome' => 'Distribuidora Azul',
                'valor_total' => 480.00,
                'valor_pago' => 480.00,
                'vencimento' => now()->subDays(2)->toDateString(),
                'pagamento_previsto_em' => now()->subDays(2)->toDateString(),
                'pago_em' => now()->subDays(2),
                'status' => 'paga',
                'observacoes' => 'Baixa automatica demo.',
            ]
        );

        $contaReceberParcial = ContaReceber::updateOrCreate(
            ['conta_id' => $conta->id, 'descricao' => 'Pedido corporativo mercado bairro'],
            [
                'loja_id' => $lojas['express']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['vendas-em-loja']->id,
                'cliente_nome' => 'Mercado Bairro',
                'valor_total' => 1200.00,
                'valor_recebido' => 300.00,
                'vencimento' => now()->addDays(4)->toDateString(),
                'recebimento_previsto_em' => now()->addDays(3)->toDateString(),
                'status' => 'parcial',
                'observacoes' => 'Primeira parcela recebida.',
                'conta_financeira_id' => null,
            ]
        );

        $contaReceberRecebida = ContaReceber::updateOrCreate(
            ['conta_id' => $conta->id, 'descricao' => 'Recebimento ecommerce semana'],
            [
                'loja_id' => $lojas['online']->id,
                'conta_financeira_id' => $contasFinanceiras['carteira-online']->id,
                'categoria_financeira_id' => $categoriasFinanceiras['receitas-online']->id,
                'cliente_nome' => 'Gateway Marketplace',
                'valor_total' => 860.00,
                'valor_recebido' => 860.00,
                'vencimento' => now()->subDay()->toDateString(),
                'recebimento_previsto_em' => now()->subDay()->toDateString(),
                'recebido_em' => now()->subDay(),
                'status' => 'recebida',
                'observacoes' => 'Baixa automatica demo.',
            ]
        );

        $synchronizer->syncContaPagar($contaPagarAberta, $owner->id);
        $synchronizer->syncContaPagar($contaPagarPaga, $owner->id);
        $synchronizer->syncContaReceber($contaReceberParcial, $owner->id);
        $synchronizer->syncContaReceber($contaReceberRecebida, $owner->id);
    }

    private function recalcularSaldos(Conta $conta): void
    {
        foreach ($conta->contasFinanceiras()->get() as $contaFinanceira) {
            $receitas = $contaFinanceira->movimentacoes()
                ->where('status', 'realizada')
                ->where('tipo', 'receita')
                ->sum('valor');

            $despesas = $contaFinanceira->movimentacoes()
                ->where('status', 'realizada')
                ->where('tipo', 'despesa')
                ->sum('valor');

            $contaFinanceira->forceFill([
                'saldo_atual' => $contaFinanceira->saldo_inicial + $receitas - $despesas,
            ])->saveQuietly();
        }
    }
}
