@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Painel administrativo')
@section('subheading', 'Uma leitura mais gerencial da conta logada, conectando operacao, catalogo, precificacao e saude financeira em uma unica visao.')

@section('content')
    <section class="card visual-hero">
        <div class="card-body visual-hero-copy">
            <div class="section-header">
                <div>
                    <h2>Centro de comando</h2>
                    <p>O painel agora combina icones, sinais visuais e graficos leves para transformar a rotina da conta em decisoes mais rapidas.</p>
                </div>
            </div>

            <div class="visual-action-grid">
                <a class="button" href="{{ route('admin.lancamento') }}"><x-ui.icon name="spark" /> Ver prontidao</a>
                @if (in_array('financeiro', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.financeiro.index') }}"><x-ui.icon name="wallet" /> Abrir financeiro</a>
                @endif
                @if (in_array('lojas', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.lojas.index') }}"><x-ui.icon name="store" /> Operar lojas</a>
                @endif
                @if (in_array('catalogo', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.produtos.index') }}"><x-ui.icon name="package" /> Gerir catalogo</a>
                @endif
                @if (in_array('precos', $capacidadesConta, true))
                    <a class="button-secondary" href="{{ route('admin.precos.index') }}"><x-ui.icon name="tag" /> Revisar precos</a>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="score-ring" style="--score: {{ $saudeConta['score'] }};">
                <div class="score-ring-inner">
                    <x-ui.icon name="shield" size="30" />
                    <strong>{{ $saudeConta['score'] }}</strong>
                    <span>{{ $saudeConta['nivel']['nome'] }}</span>
                </div>
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

    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <span class="pill">Plano do dia</span>
                    <h2>O que mais aproxima a conta do lancamento</h2>
                    <p>Prioridades calculadas com base em prontidao, financeiro, vitrine e saude operacional da conta.</p>
                </div>
                <a class="button-secondary" href="{{ route('admin.lancamento') }}">Ver centro de lancamento</a>
            </div>

            <div class="highlight-grid">
                @foreach ($planoDoDia as $acao)
                    @php
                        $iconeAcao = [
                            'lancamento' => 'spark',
                            'caixa' => 'wallet',
                            'vitrine' => 'store',
                            'recebimento' => 'credit-card',
                            'saude' => 'shield',
                            'crescimento' => 'trend',
                        ][$acao['impacto']] ?? 'spark';
                    @endphp
                    <article class="highlight-card">
                        <div class="card-title-row">
                            <span class="visual-icon"><x-ui.icon :name="$iconeAcao" /></span>
                            <span>
                                <span class="pill">{{ $acao['impacto'] }}</span>
                                <strong>{{ $acao['titulo'] }}</strong>
                            </span>
                        </div>
                        <span>{{ $acao['descricao'] }}</span>
                        <a class="ghost-link" href="{{ $acao['rota'] }}">{{ $acao['acao'] }}</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <h2>Saude da conta</h2>
                    <p>Score executivo que combina assinatura, operacao, financeiro e governanca para indicar onde agir primeiro.</p>
                </div>
                <span class="pill">{{ $saudeConta['nivel']['nome'] }}</span>
            </div>

            <div class="highlight-grid">
                <article class="highlight-card">
                    <div class="card-title-row">
                        <span class="visual-icon is-teal"><x-ui.icon name="shield" /></span>
                        <strong>{{ $saudeConta['score'] }}/100</strong>
                    </div>
                    <span>{{ $saudeConta['nivel']['descricao'] }}</span>
                </article>

                <article class="highlight-card">
                    <div class="card-title-row">
                        <span class="visual-icon is-warning"><x-ui.icon name="alert" /></span>
                        <strong>{{ $saudeConta['proxima_acao']['titulo'] ?? 'Conta em ritmo bom' }}</strong>
                    </div>
                    <span>{{ $saudeConta['proxima_acao']['descricao'] ?? 'Continue acompanhando os sinais da operacao.' }}</span>
                </article>

                <article class="highlight-card">
                    <div class="card-title-row">
                        <span class="visual-icon"><x-ui.icon name="bell" /></span>
                        <strong>{{ number_format(count($saudeConta['sinais']), 0, ',', '.') }}</strong>
                    </div>
                    <span>sinais priorizados para orientar a proxima decisao</span>
                </article>
            </div>

            <div class="mini-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));">
                @foreach ($saudeConta['pilares'] as $pilar)
                    <article class="mini-card">
                        <strong>{{ $pilar['score'] }}/100</strong>
                        <span>{{ $pilar['nome'] }}</span>
                        <div class="progress-track" style="margin-top:12px;">
                            <span class="progress-fill {{ $pilar['score'] >= 70 ? 'is-teal' : '' }}" style="width: {{ $pilar['score'] }}%;"></span>
                        </div>
                        <small class="helper-text">{{ $pilar['descricao'] }}</small>
                    </article>
                @endforeach
            </div>

            <div class="signal-list">
                @foreach (array_slice($saudeConta['sinais'], 0, 3) as $sinal)
                    @php
                        $capacidadeSinal = [
                            'admin.assinatura' => 'gestao',
                            'admin.configuracoes.edit' => 'gestao',
                            'admin.financeiro.index' => 'financeiro',
                            'admin.precos.index' => 'precos',
                            'admin.equipe.index' => 'equipe',
                        ][$sinal['rota'] ?? ''] ?? null;
                        $podeAbrirSinal = ! $capacidadeSinal || in_array($capacidadeSinal, $capacidadesConta, true);
                    @endphp
                    <article class="signal-item">
                        <strong>{{ $sinal['titulo'] }}</strong>
                        <span>{{ $sinal['descricao'] }}</span>
                        @if (! empty($sinal['rota']) && $podeAbrirSinal)
                            <a class="ghost-link" href="{{ route($sinal['rota']) }}">{{ $sinal['acao'] }}</a>
                        @else
                            <small class="helper-text">{{ $sinal['acao'] }}</small>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <h2>Uso do plano</h2>
                    <p>Leitura de capacidade da conta para evitar crescimento desorganizado e antecipar upgrade quando a operacao estiver perto do limite.</p>
                </div>
                <span class="pill">{{ $usoPlano['plano']?->nome ?? 'Plano nao definido' }}</span>
            </div>

            <div class="mini-grid" style="grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));">
                @foreach ($usoPlano['metricas'] as $metrica)
                    <article class="mini-card">
                        <strong>{{ number_format($metrica['usado'], 0, ',', '.') }}{{ $metrica['ilimitado'] ? '' : ' / ' . number_format($metrica['limite'], 0, ',', '.') }}</strong>
                        <span>{{ ucfirst($metrica['rotulo']) }} em uso</span>
                        @if (! $metrica['ilimitado'])
                            <div class="progress-track" style="margin-top:12px;">
                                <span class="progress-fill {{ $metrica['excedido'] ? '' : 'is-teal' }}" style="width: {{ $metrica['percentual'] }}%;"></span>
                            </div>
                            <small class="helper-text">
                                {{ $metrica['disponivel'] }} disponiveis no plano atual
                            </small>
                        @else
                            <small class="helper-text">sem limite operacional configurado</small>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid-4">
        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Saldo projetado</span>
                <span class="metric-icon {{ $saldoProjetado < 0 ? 'is-danger' : 'is-teal' }}"><x-ui.icon name="wallet" /></span>
            </div>
            <strong class="metric-value">R$ {{ number_format($saldoProjetado, 2, ',', '.') }}</strong>
            <span class="metric-trend {{ $saldoProjetado < 0 ? 'is-danger' : '' }}">
                {{ $saldoProjetado < 0 ? 'pressao sobre o caixa' : 'resultado consolidado da operacao' }}
            </span>
        </article>

        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Margem operacional</span>
                <span class="metric-icon {{ $margemOperacional < 0 ? 'is-danger' : 'is-teal' }}"><x-ui.icon name="trend" /></span>
            </div>
            <strong class="metric-value">{{ number_format($margemOperacional, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $margemOperacional < 0 ? 'is-danger' : '' }}">
                relacao entre receitas e despesas realizadas
            </span>
        </article>

        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Cobertura de caixa</span>
                <span class="metric-icon {{ $coberturaCaixa < 100 ? 'is-warning' : 'is-teal' }}"><x-ui.icon name="shield" /></span>
            </div>
            <strong class="metric-value">{{ number_format($coberturaCaixa, 1, ',', '.') }}%</strong>
            <span class="metric-trend {{ $coberturaCaixa < 100 ? 'is-danger' : '' }}">
                saldo das contas financeiras versus despesas
            </span>
        </article>

        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Precos monitorados</span>
                <span class="metric-icon"><x-ui.icon name="tag" /></span>
            </div>
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
        <article class="card chart-card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Ritmo dos ultimos meses</h2>
                        <p>Receitas e despesas organizadas em uma linha do tempo simples para leitura executiva.</p>
                    </div>
                </div>

                <div class="chart-legend">
                    <span class="legend-dot">Receitas</span>
                    <span class="legend-dot is-danger">Despesas</span>
                    <span class="legend-dot is-warning">Saldo mensal</span>
                </div>

                @php
                    $cashChart = $serieMensal->values();
                    $chartTop = 34;
                    $chartBottom = 218;
                    $chartLeft = 46;
                    $chartRight = 676;
                    $chartWidth = $chartRight - $chartLeft;
                    $chartSteps = max(1, $cashChart->count() - 1);
                    $saldoValores = $cashChart->pluck('saldo')->push(0);
                    $saldoMinimo = min(-1, (float) $saldoValores->min());
                    $saldoMaximo = max(1, (float) $saldoValores->max());
                    $saldoIntervalo = max(1, $saldoMaximo - $saldoMinimo);
                    $maiorBarraChart = max(1, (float) $cashChart->max('receitas'), (float) $cashChart->max('despesas'));
                    $zeroY = $chartTop + (($saldoMaximo - 0) / $saldoIntervalo) * ($chartBottom - $chartTop);
                    $chartPoints = $cashChart->map(function ($mes, $index) use ($chartLeft, $chartWidth, $chartSteps, $chartTop, $chartBottom, $saldoMaximo, $saldoIntervalo) {
                        $x = $chartLeft + (($chartWidth / $chartSteps) * $index);
                        $y = $chartTop + (($saldoMaximo - $mes['saldo']) / $saldoIntervalo) * ($chartBottom - $chartTop);

                        return [
                            ...$mes,
                            'x' => round($x, 2),
                            'y' => round($y, 2),
                        ];
                    });
                    $linePath = $chartPoints->map(fn ($point, $index) => ($index === 0 ? 'M' : 'L') . $point['x'] . ' ' . $point['y'])->implode(' ');
                    $areaPath = $chartPoints->isNotEmpty()
                        ? $linePath . ' L ' . $chartPoints->last()['x'] . ' ' . $chartBottom . ' L ' . $chartPoints->first()['x'] . ' ' . $chartBottom . ' Z'
                        : '';
                    $ultimoMes = $cashChart->last();
                @endphp

                <div class="apex-chart-shell" aria-label="Grafico financeiro dos ultimos meses">
                    <svg class="apex-chart" viewBox="0 0 720 300" role="img" aria-labelledby="cash-chart-title cash-chart-desc">
                        <title id="cash-chart-title">Evolucao financeira mensal</title>
                        <desc id="cash-chart-desc">Linha de saldo mensal com barras de receitas e despesas nos ultimos seis meses.</desc>
                        <defs>
                            <linearGradient id="cashAreaGradient" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="#f45a24" stop-opacity="0.28" />
                                <stop offset="100%" stop-color="#f45a24" stop-opacity="0.02" />
                            </linearGradient>
                        </defs>

                        @foreach ([58, 98, 138, 178, 218] as $gridY)
                            <line class="apex-grid-line" x1="{{ $chartLeft }}" x2="{{ $chartRight }}" y1="{{ $gridY }}" y2="{{ $gridY }}" />
                        @endforeach
                        <line class="apex-zero-line" x1="{{ $chartLeft }}" x2="{{ $chartRight }}" y1="{{ $zeroY }}" y2="{{ $zeroY }}" />

                        @foreach ($chartPoints as $point)
                            @php
                                $receitaHeight = ($point['receitas'] / $maiorBarraChart) * 112;
                                $despesaHeight = ($point['despesas'] / $maiorBarraChart) * 112;
                            @endphp
                            <rect class="apex-bar-receita" x="{{ $point['x'] - 13 }}" y="{{ 230 - $receitaHeight }}" width="9" height="{{ max(2, $receitaHeight) }}" rx="5" />
                            <rect class="apex-bar-despesa" x="{{ $point['x'] + 4 }}" y="{{ 230 - $despesaHeight }}" width="9" height="{{ max(2, $despesaHeight) }}" rx="5" />
                        @endforeach

                        <path class="apex-area" d="{{ $areaPath }}" />
                        <path class="apex-line" d="{{ $linePath }}" />

                        @foreach ($chartPoints as $point)
                            <circle class="apex-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" />
                            <text class="apex-axis-label" x="{{ $point['x'] }}" y="268" text-anchor="middle">{{ strtoupper($point['label']) }}</text>
                        @endforeach
                    </svg>

                    <div class="apex-value-pill">
                        <span>Ultimo saldo: R$ {{ number_format((float) ($ultimoMes['saldo'] ?? 0), 2, ',', '.') }}</span>
                        <span>Receitas: R$ {{ number_format((float) ($ultimoMes['receitas'] ?? 0), 2, ',', '.') }}</span>
                        <span>Despesas: R$ {{ number_format((float) ($ultimoMes['despesas'] ?? 0), 2, ',', '.') }}</span>
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
