@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Painel administrativo')
@section('subheading', 'Uma leitura mais gerencial da conta logada, conectando operacao, catalogo, precificacao e saude financeira em uma unica visao.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <h2>Centro de comando</h2>
                    <p>No celular, esta faixa vira o ponto mais rapido para entrar nas operacoes que mais importam ao longo do dia.</p>
                </div>
            </div>

            <div class="mini-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
                @if (in_array('financeiro', $capacidadesConta, true))
                    <a class="button" href="{{ route('admin.financeiro.index') }}">Abrir financeiro</a>
                @endif
                @if (in_array('lojas', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.lojas.index') }}">Operar lojas</a>
                @endif
                @if (in_array('catalogo', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.produtos.index') }}">Gerir catalogo</a>
                @endif
                @if (in_array('precos', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.precos.index') }}">Revisar precos</a>
                @endif
            </div>
        </div>
    </section>

    @if ($onboarding['percentual'] < 100)
        <section class="card">
            <div class="card-body">
                <div class="setup-banner">
                    <div>
                        <span class="pill">Onboarding da conta</span>
                        <h2>Seu setup esta em {{ $onboarding['percentual'] }}%</h2>
                        <p>
                            {{ $onboarding['proxima_etapa']['titulo'] ?? 'Base principal concluida.' }}
                            @if ($onboarding['proxima_etapa'])
                                {{ $onboarding['proxima_etapa']['descricao'] }}
                            @endif
                        </p>
                    </div>

                    <div class="setup-banner-actions">
                        <div class="progress-track">
                            <span class="progress-fill is-teal" style="width: {{ $onboarding['percentual'] }}%;"></span>
                        </div>
                        <div class="toolbar-actions">
                            <a class="button" href="{{ route('admin.onboarding') }}">Abrir onboarding</a>
                            @if ($onboarding['proxima_etapa'] && $onboarding['proxima_etapa']['rota'])
                                <a class="button-secondary" href="{{ $onboarding['proxima_etapa']['rota'] }}">Executar proxima etapa</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Saldo projetado</span>
            <strong class="metric-value">R$ {{ number_format($saldoProjetado, 2, ',', '.') }}</strong>
            <span class="metric-trend {{ $saldoProjetado < 0 ? 'is-danger' : '' }}">
                {{ $saldoProjetado < 0 ? 'pressao sobre o caixa' : 'resultado consolidado da operacao' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Margem operacional</span>
            <strong class="metric-value">{{ number_format($margemOperacional, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $margemOperacional < 0 ? 'is-danger' : '' }}">
                relacao entre receitas e despesas realizadas
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Cobertura de caixa</span>
            <strong class="metric-value">{{ number_format($coberturaCaixa, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $coberturaCaixa < 100 ? 'is-danger' : '' }}">
                saldo das contas financeiras versus despesas
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Precos monitorados</span>
            <strong class="metric-value">{{ number_format($totalPrecosMonitorados, 0, ',', '.') }}</strong>
            <span class="metric-trend">catalogo e comparador conectados</span>
        </article>
    </section>

    <section class="highlight-grid">
        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>{{ number_format($totalProdutosCatalogo, 0, ',', '.') }}</strong>
                    <span>produtos ativos no catalogo ligado as lojas da conta</span>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>{{ $titulosCriticos }}</strong>
                    <span>titulos com vencimento nos proximos sete dias</span>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>{{ $conta->lojas_count }}</strong>
                    <span>lojas operando na conta atual</span>
                </div>
            </div>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Ritmo dos ultimos meses</h2>
                        <p>Receitas e despesas organizadas em uma linha do tempo simples para leitura executiva.</p>
                    </div>
                </div>

                <div class="month-grid">
                    @foreach ($serieMensal as $mes)
                        <article class="month-card">
                            <strong>{{ strtoupper($mes['label']) }}</strong>

                            <div class="month-bars">
                                <div class="month-bar-group">
                                    <div class="month-bar is-receita">
                                        <span style="width: {{ min(100, ($mes['receitas'] / $maiorVolumeMensal) * 100) }}%;"></span>
                                    </div>
                                    <div class="month-bar is-despesa">
                                        <span style="width: {{ min(100, ($mes['despesas'] / $maiorVolumeMensal) * 100) }}%;"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="month-legend">
                                <span>Receita: R$ {{ number_format($mes['receitas'], 2, ',', '.') }}</span>
                                <span>Despesa: R$ {{ number_format($mes['despesas'], 2, ',', '.') }}</span>
                                <span>Saldo: R$ {{ number_format($mes['saldo'], 2, ',', '.') }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Sinais imediatos</h2>
                        <p>Os pontos que merecem mais atencao no curto prazo.</p>
                    </div>
                </div>

                <div class="signal-list">
                    <article class="signal-item">
                        <strong>{{ $contasPagarPendentes }} contas a pagar em aberto</strong>
                        <span>use o financeiro para negociar, programar ou baixar os compromissos pendentes.</span>
                    </article>

                    <article class="signal-item">
                        <strong>{{ $contasReceberPendentes }} contas a receber em aberto</strong>
                        <span>acompanhe previsao de entrada e baixa automatica nos titulos recebidos.</span>
                    </article>

                    <article class="signal-item">
                        <strong>{{ $conta->movimentacoes_financeiras_count }} lancamentos financeiros registrados</strong>
                        <span>o historico ja esta denso o bastante para gerar leitura gerencial no modulo financeiro.</span>
                    </article>
                </div>
            </div>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Radar de lojas</h2>
                        <p>Visao de quais canais estao mais conectados ao comparador e ao movimento financeiro.</p>
                    </div>
                </div>

                @if ($rankingLojas->isEmpty())
                    <div class="empty-state">
                        Nenhuma loja encontrada para montar o radar operacional da conta.
                    </div>
                @else
                    <div class="progress-stack">
                        @foreach ($rankingLojas as $loja)
                            <div class="progress-row">
                                <div class="progress-meta">
                                    <span>{{ $loja['nome'] }}</span>
                                    <span>{{ $loja['precos_count'] }} precos - {{ $loja['movimentacoes_count'] }} mov.</span>
                                </div>
                                <div class="progress-track">
                                    <span class="progress-fill is-teal" style="width: {{ min(100, ($loja['precos_count'] / max(1, $rankingLojas->max('precos_count'))) * 100) }}%;"></span>
                                </div>
                                <small class="helper-text">{{ $loja['local'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Composicao das categorias</h2>
                        <p>As frentes que mais puxam valor ou custo dentro da operacao.</p>
                    </div>
                </div>

                @if ($composicaoCategorias->isEmpty())
                    <div class="empty-state">
                        Ainda nao existem movimentacoes suficientes para montar a composicao por categoria.
                    </div>
                @else
                    <div class="progress-stack">
                        @foreach ($composicaoCategorias as $categoria)
                            <div class="progress-row">
                                <div class="progress-meta">
                                    <span>{{ $categoria['nome'] }}</span>
                                    <span>R$ {{ number_format($categoria['total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="progress-track">
                                    <span class="progress-fill {{ $categoria['tipo'] === 'receita' ? 'is-teal' : '' }}" style="width: {{ min(100, ($categoria['total'] / $maiorCategoria) * 100) }}%;"></span>
                                </div>
                                <small class="helper-text">{{ $categoria['tipo'] === 'receita' ? 'categoria geradora de entrada' : 'categoria puxando despesa' }}</small>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="grid-3">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Operacao da conta</h2>
                        <p>Estrutura ativa e capacidade de execucao da base atual.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $conta->categorias_financeiras_count }}</strong>
                        <span>categorias financeiras disponiveis</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->contas_pagar_count + $conta->contas_receber_count }}</strong>
                        <span>titulos totais registrados</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $totalProdutosCatalogo }}</strong>
                        <span>produtos com presenca nas lojas da conta</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Assinatura</h2>
                        <p>Contexto comercial do SaaS e horizonte de renovacao.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</strong>
                        <small>status atual do plano</small>
                    </div>
                    <div class="mini-card">
                        <strong>R$ {{ number_format((float) ($assinaturaAtual?->valor ?? 0), 2, ',', '.') }}</strong>
                        <small>valor da assinatura ativa</small>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $assinaturaAtual?->expira_em?->format('d/m/Y') ?? 'sem data' }}</strong>
                        <small>vigencia atual da conta</small>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Atalhos rapidos</h2>
                        <p>Entradas mais uteis para operacao e acompanhamento.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    @if (in_array('financeiro', $capacidadesConta, true))
                        <a class="button" href="{{ route('admin.financeiro.index') }}">Abrir centro financeiro</a>
                    @endif
                    @if (in_array('onboarding', $capacidadesConta, true))
                        <a class="button-secondary" href="{{ route('admin.onboarding') }}">Ver onboarding</a>
                    @endif
                    @if (in_array('catalogo', $capacidadesConta, true))
                        <a class="button-secondary" href="{{ route('admin.produtos.index') }}">Ver catalogo</a>
                    @endif
                    @if (in_array('precos', $capacidadesConta, true))
                        <a class="button-secondary" href="{{ route('admin.precos.index') }}">Abrir comparador interno</a>
                    @endif
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Ultimas movimentacoes</h3>
                        <p>Movimentos mais recentes puxados diretamente do financeiro da conta.</p>
                    </div>
                </div>

                @if ($ultimasMovimentacoes->isEmpty())
                    <div class="empty-state">
                        Ainda nao existem movimentacoes financeiras registradas para esta conta.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($ultimasMovimentacoes as $movimentacao)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $movimentacao->descricao }}</strong>
                                    <small>
                                        {{ $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria' }}
                                        @if ($movimentacao->loja)
                                            - {{ $movimentacao->loja->nome }}
                                        @endif
                                    </small>
                                    <br>
                                    <code>{{ $movimentacao->data_movimentacao?->format('d/m/Y H:i') ?? 'Sem data' }}</code>
                                </div>
                                <span class="badge {{ $movimentacao->tipo === 'despesa' ? 'is-warning' : '' }}">
                                    R$ {{ number_format((float) $movimentacao->valor, 2, ',', '.') }}
                                </span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Lojas ligadas a conta</h3>
                        <p>Canais ativos para catalogo, precificacao e operacao.</p>
                    </div>
                </div>

                @if ($lojas->isEmpty())
                    <div class="empty-state">
                        Nenhuma loja foi cadastrada ainda.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($lojas as $loja)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $loja->nome }}</strong>
                                    <small>{{ $loja->cidade ?: 'Cidade nao informada' }} @if ($loja->uf) / {{ $loja->uf }} @endif</small>
                                    <br>
                                    <code>{{ $loja->email ?: 'sem email cadastrado' }}</code>
                                </div>
                                <span class="badge is-muted">{{ $loja->status ?: 'sem status' }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>
@endsection
