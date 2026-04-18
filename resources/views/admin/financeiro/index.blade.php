@extends('layouts.admin')

@section('title', 'Financeiro')
@section('heading', 'Centro financeiro')
@section('subheading', 'Uma visao consolidada da saude financeira da conta, com tendencia mensal, composicao por categoria, radar de contas e proximos eventos.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Janela de analise</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">A leitura abaixo considera {{ $periodoLabel }} para receitas, despesas, composicao e historico recente.</p>
                </div>

                <form class="filter-row" method="GET" action="{{ route('admin.financeiro.index') }}">
                    <select name="periodo" onchange="this.form.submit()">
                        @foreach ($periodosDisponiveis as $slug => $periodo)
                            <option value="{{ $slug }}" @selected($periodoAtual === $slug)>{{ ucfirst($periodo['label']) }}</option>
                        @endforeach
                    </select>
                    <noscript>
                        <button class="button-secondary" type="submit">Aplicar</button>
                    </noscript>
                </form>
            </div>
        </div>
    </section>

    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Saldo projetado</span>
            <strong class="metric-value">R$ {{ number_format($saldoProjetado, 2, ',', '.') }}</strong>
            <span class="metric-trend {{ $saldoProjetado < 0 ? 'is-danger' : '' }}">
                {{ $saldoProjetado < 0 ? 'mais saidas do que entradas na janela' : 'resultado consolidado da janela selecionada' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Comprometimento</span>
            <strong class="metric-value">{{ number_format($comprometimento, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $comprometimento > 100 ? 'is-danger' : '' }}">
                despesas realizadas sobre receitas realizadas em {{ $periodoLabel }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Cobertura de titulos</span>
            <strong class="metric-value">{{ number_format($coberturaTitulos, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $coberturaTitulos < 100 ? 'is-danger' : '' }}">
                saldo das contas frente ao que falta pagar
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Baixas automaticas</span>
            <strong class="metric-value">{{ number_format($titulosComBaixa, 0, ',', '.') }}</strong>
            <span class="metric-trend">titulos sincronizados com lancamentos na base atual</span>
        </article>
    </section>

    <section class="highlight-grid">
        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>R$ {{ number_format($totalReceberAberto, 2, ',', '.') }}</strong>
                    <span>volume em contas a receber ainda abertas</span>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>R$ {{ number_format($totalPagarAberto, 2, ',', '.') }}</strong>
                    <span>volume em contas a pagar ainda abertas</span>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="highlight-card">
                    <strong>{{ number_format($relacaoEntradaSaida, 1, ',', '.') }}%</strong>
                    <span>forca relativa de entrada sobre saida realizada</span>
                </div>
            </div>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Ritmo financeiro mensal</h2>
                        <p>Receitas e despesas realizadas distribuidas em {{ $periodoLabel }}.</p>
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
                        <h2>Atalhos de operacao</h2>
                        <p>Entradas mais usadas para continuar alimentando o financeiro.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <a class="button" href="{{ route('admin.financeiro.lancamentos.create') }}">Novo lancamento</a>
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas.create') }}">Nova conta financeira</a>
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas-pagar.create') }}">Nova conta a pagar</a>
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas-receber.create') }}">Nova conta a receber</a>
                </div>
            </div>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Composicao de receitas</h2>
                        <p>Onde o caixa esta sendo puxado para cima em {{ $periodoLabel }}.</p>
                    </div>
                </div>

                @if ($composicaoReceitas->isEmpty())
                    <div class="empty-state">
                        Ainda nao existem receitas suficientes para montar a composicao do financeiro.
                    </div>
                @else
                    <div class="progress-stack">
                        @foreach ($composicaoReceitas as $categoria)
                            <div class="progress-row">
                                <div class="progress-meta">
                                    <span>{{ $categoria['nome'] }}</span>
                                    <span>R$ {{ number_format($categoria['total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="progress-track">
                                    <span class="progress-fill is-teal" style="width: {{ min(100, ($categoria['total'] / $maiorReceitaCategoria) * 100) }}%;"></span>
                                </div>
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
                        <h2>Composicao de despesas</h2>
                        <p>Onde a operacao esta consumindo mais recurso em {{ $periodoLabel }}.</p>
                    </div>
                </div>

                @if ($composicaoDespesas->isEmpty())
                    <div class="empty-state">
                        Ainda nao existem despesas suficientes para montar a composicao do financeiro.
                    </div>
                @else
                    <div class="progress-stack">
                        @foreach ($composicaoDespesas as $categoria)
                            <div class="progress-row">
                                <div class="progress-meta">
                                    <span>{{ $categoria['nome'] }}</span>
                                    <span>R$ {{ number_format($categoria['total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="progress-track">
                                    <span class="progress-fill" style="width: {{ min(100, ($categoria['total'] / $maiorDespesaCategoria) * 100) }}%;"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Radar das contas financeiras</h2>
                        <p>Onde o saldo esta mais concentrado dentro da estrutura financeira da conta.</p>
                    </div>
                </div>

                @if ($radarContas->isEmpty())
                    <div class="empty-state">
                        Nenhuma conta financeira cadastrada ainda.
                    </div>
                @else
                    <div class="progress-stack">
                        @foreach ($radarContas as $item)
                            <div class="progress-row">
                                <div class="progress-meta">
                                    <span>{{ $item['nome'] }}</span>
                                    <span>R$ {{ number_format($item['saldo_atual'], 2, ',', '.') }}</span>
                                </div>
                                <div class="progress-track">
                                    <span class="progress-fill is-teal" style="width: {{ min(100, ($item['saldo_atual'] / $maiorSaldoConta) * 100) }}%;"></span>
                                </div>
                                <small class="helper-text">{{ ucfirst(str_replace('_', ' ', $item['tipo'])) }} - {{ $item['contexto'] }}</small>
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
                        <h2>Proximos eventos</h2>
                        <p>Agenda curta do que vence primeiro no financeiro.</p>
                    </div>
                </div>

                @if ($proximosEventos->isEmpty())
                    <div class="empty-state">
                        Nenhum evento financeiro proximo para mostrar agora.
                    </div>
                @else
                    <div class="signal-list">
                        @foreach ($proximosEventos as $evento)
                            <article class="signal-item">
                                <strong>{{ $evento['descricao'] }}</strong>
                                <span>{{ $evento['tipo'] === 'pagar' ? 'Saida prevista' : 'Entrada prevista' }} - {{ $evento['parceiro'] }}</span>
                                <small>{{ $evento['vencimento']?->format('d/m/Y') ?? 'Sem vencimento' }} - R$ {{ number_format($evento['valor'], 2, ',', '.') }}</small>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Movimentacoes recentes</h3>
                        <p>Ultimos registros financeiros ligados a conta dentro de {{ $periodoLabel }}.</p>
                    </div>
                </div>

                @if ($movimentacoesRecentes->isEmpty())
                    <div class="empty-state">
                        Nenhuma movimentacao financeira encontrada em {{ $periodoLabel }}. Quando o fluxo operacional entrar nessa janela, essa area vai refletir entradas, saidas e origem dos lancamentos.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($movimentacoesRecentes as $movimentacao)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $movimentacao->descricao }}</strong>
                                    <small>
                                        {{ $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria' }}
                                        @if ($movimentacao->contaFinanceira)
                                            - {{ $movimentacao->contaFinanceira->nome }}
                                        @endif
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
                        <h3>Titulos pendentes</h3>
                        <p>Leitura rapida do que ainda esta em aberto na previsao de caixa.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>R$ {{ number_format($totalPagarAberto, 2, ',', '.') }}</strong>
                        <span>total em contas a pagar abertas</span>
                    </div>
                    <div class="mini-card">
                        <strong>R$ {{ number_format($totalReceberAberto, 2, ',', '.') }}</strong>
                        <span>total em contas a receber abertas</span>
                    </div>
                    <div class="mini-card">
                        <strong>R$ {{ number_format($somaContasFinanceiras, 2, ',', '.') }}</strong>
                        <span>soma do saldo atual nas contas financeiras</span>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
