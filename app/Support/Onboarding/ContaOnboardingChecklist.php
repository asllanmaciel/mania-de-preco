<?php

namespace App\Support\Onboarding;

use App\Models\Conta;
use App\Models\Preco;

class ContaOnboardingChecklist
{
    public function build(Conta $conta, array $capacidades = []): array
    {
        $lojasIds = $conta->lojas()->pluck('id');
        $totalPrecos = $lojasIds->isEmpty()
            ? 0
            : Preco::query()->whereIn('loja_id', $lojasIds)->count();

        $totais = [
            'lojas' => $conta->lojas()->count(),
            'categorias' => $conta->categoriasFinanceiras()->count(),
            'contas_financeiras' => $conta->contasFinanceiras()->count(),
            'movimentacoes' => $conta->movimentacoesFinanceiras()->count(),
            'titulos' => $conta->contasPagar()->count() + $conta->contasReceber()->count(),
            'precos' => $totalPrecos,
        ];

        $etapas = collect([
            [
                'codigo' => 'loja',
                'capacidade' => 'lojas',
                'grupo' => 'fundacao',
                'titulo' => 'Cadastrar a primeira loja',
                'descricao' => 'A loja e a ancora operacional da conta para financeiro, catalogo e comparador.',
                'rota' => $this->rotaPermitida($capacidades, 'lojas', 'admin.lojas.create'),
                'cta' => 'Criar loja',
                'concluida' => $totais['lojas'] > 0,
            ],
            [
                'codigo' => 'categoria_financeira',
                'capacidade' => 'financeiro',
                'grupo' => 'fundacao',
                'titulo' => 'Definir categorias financeiras',
                'descricao' => 'Categorias fortes deixam os relatorios mais confiaveis e a equipe mais consistente.',
                'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.categorias.create'),
                'cta' => 'Criar categoria',
                'concluida' => $totais['categorias'] > 0,
            ],
            [
                'codigo' => 'conta_financeira',
                'capacidade' => 'financeiro',
                'grupo' => 'fundacao',
                'titulo' => 'Estruturar contas financeiras',
                'descricao' => 'Caixa, banco e carteiras precisam existir para sustentar lancamentos e baixas.',
                'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.contas.create'),
                'cta' => 'Criar conta financeira',
                'concluida' => $totais['contas_financeiras'] > 0,
            ],
            [
                'codigo' => 'movimentacao',
                'capacidade' => 'financeiro',
                'grupo' => 'operacao',
                'titulo' => 'Lancar a primeira movimentacao',
                'descricao' => 'Essa etapa liga o financeiro a uma leitura real de entrada, saida e saldo.',
                'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.lancamentos.create'),
                'cta' => 'Criar lancamento',
                'concluida' => $totais['movimentacoes'] > 0,
            ],
            [
                'codigo' => 'titulo',
                'capacidade' => 'financeiro',
                'grupo' => 'operacao',
                'titulo' => 'Ativar previsao com titulos',
                'descricao' => 'Contas a pagar e a receber transformam o sistema em ferramenta de previsao, nao so historico.',
                'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.contas-pagar.create'),
                'cta' => 'Criar titulo',
                'concluida' => $totais['titulos'] > 0,
            ],
            [
                'codigo' => 'preco',
                'capacidade' => 'precos',
                'grupo' => 'go_to_market',
                'titulo' => 'Publicar o primeiro preco',
                'descricao' => 'Essa e a ponte entre o SaaS interno e a vitrine que encontra o melhor preco.',
                'rota' => $this->rotaPermitida($capacidades, 'precos', 'admin.precos.create'),
                'cta' => 'Publicar preco',
                'concluida' => $totais['precos'] > 0,
            ],
        ])->values();

        $concluidas = $etapas->where('concluida', true)->count();
        $total = max(1, $etapas->count());
        $percentual = (int) round(($concluidas / $total) * 100);
        $proximaEtapa = $etapas->firstWhere('concluida', false);

        $grupos = $etapas
            ->groupBy('grupo')
            ->map(function ($grupo, $codigo) {
                return [
                    'codigo' => $codigo,
                    'titulo' => match ($codigo) {
                        'fundacao' => 'Fundacao da conta',
                        'operacao' => 'Operacao financeira',
                        'go_to_market' => 'Go to market',
                        default => 'Evolucao',
                    },
                    'concluidas' => $grupo->where('concluida', true)->count(),
                    'total' => $grupo->count(),
                    'percentual' => (int) round(($grupo->where('concluida', true)->count() / max(1, $grupo->count())) * 100),
                    'etapas' => $grupo->values(),
                ];
            })
            ->values();

        return [
            'percentual' => $percentual,
            'concluidas' => $concluidas,
            'total' => $total,
            'etapas' => $etapas,
            'proxima_etapa' => $proximaEtapa,
            'grupos' => $grupos,
            'totais' => $totais,
            'pronta_para_operar' => $totais['lojas'] > 0 && $totais['contas_financeiras'] > 0 && $totais['movimentacoes'] > 0,
            'pronta_para_vitrine' => $totais['precos'] > 0,
        ];
    }

    private function rotaPermitida(array $capacidades, string $capacidade, string $rota): ?string
    {
        if ($capacidades !== [] && ! in_array($capacidade, $capacidades, true)) {
            return null;
        }

        return route($rota);
    }
}
