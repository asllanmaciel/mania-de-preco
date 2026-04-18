<?php

namespace Database\Seeders;

use App\Models\Assinatura;
use App\Models\CategoriaFinanceira;
use App\Models\Conta;
use App\Models\Loja;
use App\Models\Plano;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Conta Demo',
                'password' => Hash::make('password'),
            ]
        );

        $conta = Conta::firstOrCreate(
            ['slug' => 'conta-demo'],
            [
                'nome_fantasia' => 'Conta Demo',
                'email' => $user->email,
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]
        );

        $conta->usuarios()->syncWithoutDetaching([
            $user->id => [
                'papel' => 'owner',
                'ativo' => true,
                'ultimo_acesso_em' => now(),
            ],
        ]);

        $loja = Loja::firstOrCreate(
            ['nome' => 'Loja Demo', 'conta_id' => $conta->id],
            [
                'email' => 'loja-demo@maniadepreco.com.br',
                'tipo_loja' => 'mista',
                'status' => 'ativo',
                'cidade' => 'São Paulo',
                'uf' => 'SP',
            ]
        );

        $plano = Plano::firstOrCreate(
            ['slug' => 'starter'],
            [
                'nome' => 'Starter',
                'descricao' => 'Plano inicial para pequenas lojas.',
                'valor_mensal' => 49.90,
                'valor_anual' => 499.00,
                'limite_usuarios' => 3,
                'limite_lojas' => 1,
                'limite_produtos' => 500,
                'recursos' => [
                    'controle_financeiro',
                    'catalogo_publico',
                    'comparador_de_precos',
                ],
                'status' => 'ativo',
            ]
        );

        Assinatura::firstOrCreate(
            ['conta_id' => $conta->id, 'plano_id' => $plano->id],
            [
                'status' => 'trial',
                'ciclo_cobranca' => 'mensal',
                'valor' => 49.90,
                'inicia_em' => now()->toDateString(),
                'expira_em' => now()->addDays(14)->toDateString(),
            ]
        );

        foreach ([
            ['nome' => 'Vendas', 'slug' => 'vendas', 'tipo' => 'receita', 'cor' => '#15803d'],
            ['nome' => 'Despesas Operacionais', 'slug' => 'despesas-operacionais', 'tipo' => 'despesa', 'cor' => '#b91c1c'],
            ['nome' => 'Marketing', 'slug' => 'marketing', 'tipo' => 'despesa', 'cor' => '#7c3aed'],
        ] as $categoria) {
            CategoriaFinanceira::firstOrCreate(
                ['conta_id' => $conta->id, 'slug' => $categoria['slug']],
                $categoria
            );
        }
    }
}
