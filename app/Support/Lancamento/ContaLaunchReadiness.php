<?php

namespace App\Support\Lancamento;

use App\Models\Conta;
use App\Models\Preco;
use App\Models\Produto;

class ContaLaunchReadiness
{
    public function analisar(Conta $conta, array $capacidades = []): array
    {
        $lojasIds = $conta->lojas()->pluck('id');
        $totalLojas = $conta->lojas()->count();
        $lojasAtivas = $conta->lojas()->where('status', 'ativo')->count();
        $totalPrecos = $lojasIds->isEmpty() ? 0 : Preco::whereIn('loja_id', $lojasIds)->count();
        $produtosPublicados = Produto::whereHas('precos', fn ($query) => $query->whereIn('loja_id', $lojasIds))->count();
        $produtosComImagem = Produto::whereHas('precos', fn ($query) => $query->whereIn('loja_id', $lojasIds))
            ->whereNotNull('imagem_principal')
            ->where('imagem_principal', '!=', '')
            ->count();

        $metricas = [
            'configuracao_percentual' => $this->configuracaoPercentual($conta),
            'total_lojas' => $totalLojas,
            'lojas_ativas' => $lojasAtivas,
            'produtos_publicados' => $produtosPublicados,
            'produtos_com_imagem' => $produtosComImagem,
            'precos_publicados' => $totalPrecos,
            'avaliacoes' => $conta->lojas()->withCount('avaliacoes')->get()->sum('avaliacoes_count'),
            'categorias_financeiras' => $conta->categoriasFinanceiras()->count(),
            'contas_financeiras' => $conta->contasFinanceiras()->count(),
            'movimentacoes' => $conta->movimentacoesFinanceiras()->count(),
            'titulos' => $conta->contasPagar()->count() + $conta->contasReceber()->count(),
            'usuarios_ativos' => $conta->usuarios()->wherePivot('ativo', true)->count(),
            'logs_auditoria' => $conta->auditLogs()->count(),
            'assinatura_status' => $conta->assinaturas()->latest('id')->first()?->status,
        ];

        $grupos = collect([
            $this->grupoIdentidade($conta, $metricas, $capacidades),
            $this->grupoVitrine($metricas, $capacidades),
            $this->grupoOperacao($metricas, $capacidades),
            $this->grupoGovernanca($metricas, $capacidades),
        ])->map(function (array $grupo) {
            $concluidas = collect($grupo['etapas'])->where('concluida', true)->count();
            $total = max(1, count($grupo['etapas']));

            return array_merge($grupo, [
                'concluidas' => $concluidas,
                'total' => $total,
                'score' => (int) round(($concluidas / $total) * 100),
            ]);
        })->values();

        $score = (int) round($grupos->avg('score'));
        $etapasPendentes = $grupos
            ->flatMap(fn (array $grupo) => collect($grupo['etapas'])->map(fn (array $etapa) => array_merge($etapa, [
                'grupo' => $grupo['titulo'],
            ])))
            ->where('concluida', false)
            ->values();

        return [
            'score' => $score,
            'nivel' => $this->nivel($score),
            'pronta' => $score >= 85 && $etapasPendentes->where('critica', true)->isEmpty(),
            'grupos' => $grupos,
            'metricas' => $metricas,
            'pendencias_criticas' => $etapasPendentes->where('critica', true)->count(),
            'proximas_acoes' => $etapasPendentes
                ->sortByDesc(fn (array $etapa) => $etapa['critica'] ? 1 : 0)
                ->take(4)
                ->values(),
        ];
    }

    private function grupoIdentidade(Conta $conta, array $metricas, array $capacidades): array
    {
        return [
            'codigo' => 'identidade',
            'titulo' => 'Identidade comercial',
            'descricao' => 'Base publica e cadastral para transmitir confianca antes do cliente decidir.',
            'icone' => 'building',
            'etapas' => [
                [
                    'titulo' => 'Dados comerciais completos',
                    'descricao' => 'Nome, documento, contato, segmento e localidade precisam estar prontos para suporte, cobranca e apresentacao.',
                    'concluida' => $metricas['configuracao_percentual'] >= 80,
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'gestao', 'admin.configuracoes.edit'),
                    'acao' => 'Completar dados',
                ],
                [
                    'titulo' => 'Descricao publica da operacao',
                    'descricao' => 'A conta precisa explicar o que vende, para quem vende e por que o consumidor pode confiar.',
                    'concluida' => filled($conta->descricao_publica),
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'gestao', 'admin.configuracoes.edit'),
                    'acao' => 'Ajustar apresentacao',
                ],
                [
                    'titulo' => 'Canal de contato visivel',
                    'descricao' => 'E-mail, telefone, site ou Instagram reduzem friccao para comprador e suporte.',
                    'concluida' => filled($conta->email) && (filled($conta->telefone) || filled($conta->site) || filled($conta->instagram)),
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'gestao', 'admin.configuracoes.edit'),
                    'acao' => 'Revisar canais',
                ],
            ],
        ];
    }

    private function grupoVitrine(array $metricas, array $capacidades): array
    {
        return [
            'codigo' => 'vitrine',
            'titulo' => 'Vitrine e precos',
            'descricao' => 'O que faz o produto ser percebido pelo comprador: lojas, catalogo, imagem e disputa de preco.',
            'icone' => 'tag',
            'etapas' => [
                [
                    'titulo' => 'Loja ativa cadastrada',
                    'descricao' => 'Sem loja ativa, nao existe ancora para catalogo, precos e descoberta publica.',
                    'concluida' => $metricas['lojas_ativas'] > 0,
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'lojas', 'admin.lojas.index'),
                    'acao' => 'Revisar lojas',
                ],
                [
                    'titulo' => 'Produtos publicados no comparador',
                    'descricao' => 'A conta precisa ter produtos com preco para aparecer nas jornadas publicas.',
                    'concluida' => $metricas['produtos_publicados'] > 0 && $metricas['precos_publicados'] > 0,
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'precos', 'admin.precos.index'),
                    'acao' => 'Publicar precos',
                ],
                [
                    'titulo' => 'Produtos com imagem',
                    'descricao' => 'Cards com imagem aumentam leitura, confianca e percepcao de produto acabado.',
                    'concluida' => $metricas['produtos_publicados'] > 0 && $metricas['produtos_com_imagem'] >= min(3, $metricas['produtos_publicados']),
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'catalogo', 'admin.produtos.index'),
                    'acao' => 'Melhorar catalogo',
                ],
                [
                    'titulo' => 'Prova social inicial',
                    'descricao' => 'Avaliacoes ajudam a loja a parecer viva e confiavel no primeiro contato.',
                    'concluida' => $metricas['avaliacoes'] > 0,
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'lojas', 'admin.lojas.index'),
                    'acao' => 'Ver lojas',
                ],
            ],
        ];
    }

    private function grupoOperacao(array $metricas, array $capacidades): array
    {
        return [
            'codigo' => 'operacao',
            'titulo' => 'Operacao financeira',
            'descricao' => 'A base minima para acompanhar caixa, compromissos e ritmo real da conta.',
            'icone' => 'wallet',
            'etapas' => [
                [
                    'titulo' => 'Categorias e contas financeiras',
                    'descricao' => 'A leitura financeira depende de categorias e contas bem definidas.',
                    'concluida' => $metricas['categorias_financeiras'] > 0 && $metricas['contas_financeiras'] > 0,
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.index'),
                    'acao' => 'Abrir financeiro',
                ],
                [
                    'titulo' => 'Movimentacoes reais registradas',
                    'descricao' => 'Entradas e saidas deixam os graficos, indicadores e sinais gerenciais vivos.',
                    'concluida' => $metricas['movimentacoes'] > 0,
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.lancamentos.index'),
                    'acao' => 'Ver lancamentos',
                ],
                [
                    'titulo' => 'Previsao com titulos',
                    'descricao' => 'Contas a pagar e receber deixam a operacao pronta para antecipar risco de caixa.',
                    'concluida' => $metricas['titulos'] > 0,
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'financeiro', 'admin.financeiro.index'),
                    'acao' => 'Revisar titulos',
                ],
            ],
        ];
    }

    private function grupoGovernanca(array $metricas, array $capacidades): array
    {
        return [
            'codigo' => 'governanca',
            'titulo' => 'Governanca e escala',
            'descricao' => 'Controles que reduzem risco quando a conta sai da demonstracao e vira operacao diaria.',
            'icone' => 'shield',
            'etapas' => [
                [
                    'titulo' => 'Assinatura em bom estado',
                    'descricao' => 'Conta ativa ou em trial evita interrupcao na operacao e na relacao comercial.',
                    'concluida' => in_array($metricas['assinatura_status'], ['ativa', 'trial'], true),
                    'critica' => true,
                    'rota' => $this->rotaPermitida($capacidades, 'gestao', 'admin.assinatura'),
                    'acao' => 'Ver assinatura',
                ],
                [
                    'titulo' => 'Mais de um usuario ativo',
                    'descricao' => 'Separar responsabilidades reduz dependencia de uma unica pessoa.',
                    'concluida' => $metricas['usuarios_ativos'] > 1,
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'equipe', 'admin.equipe.index'),
                    'acao' => 'Gerir equipe',
                ],
                [
                    'titulo' => 'Auditoria com historico',
                    'descricao' => 'Registros de acao ajudam suporte, seguranca e governanca em contas reais.',
                    'concluida' => $metricas['logs_auditoria'] > 0,
                    'critica' => false,
                    'rota' => $this->rotaPermitida($capacidades, 'equipe', 'admin.auditoria'),
                    'acao' => 'Abrir auditoria',
                ],
            ],
        ];
    }

    private function configuracaoPercentual(Conta $conta): int
    {
        $campos = [
            'nome_fantasia',
            'razao_social',
            'documento',
            'email',
            'telefone',
            'segmento',
            'porte',
            'cidade',
            'uf',
            'descricao_publica',
        ];

        $preenchidos = collect($campos)->filter(fn (string $campo) => filled($conta->{$campo}))->count();

        return (int) round(($preenchidos / count($campos)) * 100);
    }

    private function nivel(int $score): array
    {
        return match (true) {
            $score >= 90 => [
                'nome' => 'Pronta para lancar',
                'descricao' => 'A conta ja tem os elementos centrais para operar, vender e ser apresentada com confianca.',
            ],
            $score >= 75 => [
                'nome' => 'Quase pronta',
                'descricao' => 'A base esta forte. Poucas pendencias separam a conta de uma demonstracao excelente.',
            ],
            $score >= 50 => [
                'nome' => 'Em preparacao',
                'descricao' => 'A conta ja tem estrutura, mas ainda precisa completar pontos essenciais antes de ir para publico.',
            ],
            default => [
                'nome' => 'Fundacao inicial',
                'descricao' => 'Priorize os itens criticos antes de apresentar a conta como operacao pronta.',
            ],
        };
    }

    private function rotaPermitida(array $capacidades, string $capacidade, string $rota): ?string
    {
        if ($capacidades !== [] && ! in_array($capacidade, $capacidades, true)) {
            return null;
        }

        return route($rota);
    }
}
