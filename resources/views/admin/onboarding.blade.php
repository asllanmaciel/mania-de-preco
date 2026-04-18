@extends('layouts.admin')

@section('title', 'Onboarding')
@section('heading', 'Onboarding da conta')
@section('subheading', 'Um guia operacional para transformar uma conta nova em um SaaS realmente configurado, utilizavel e pronto para gerar valor.')

@section('content')
    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Progresso do setup</span>
            <strong class="metric-value">{{ $onboarding['percentual'] }}%</strong>
            <span class="metric-trend {{ $onboarding['percentual'] < 50 ? 'is-danger' : '' }}">
                {{ $onboarding['concluidas'] }} de {{ $onboarding['total'] }} marcos concluidos
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Financeiro operacional</span>
            <strong class="metric-value">{{ $onboarding['pronta_para_operar'] ? 'Sim' : 'Nao' }}</strong>
            <span class="metric-trend {{ ! $onboarding['pronta_para_operar'] ? 'is-danger' : '' }}">
                {{ $onboarding['pronta_para_operar'] ? 'base suficiente para operar caixa' : 'faltam bases para operar com seguranca' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Vitrine ativa</span>
            <strong class="metric-value">{{ $onboarding['pronta_para_vitrine'] ? 'Sim' : 'Nao' }}</strong>
            <span class="metric-trend {{ ! $onboarding['pronta_para_vitrine'] ? 'is-danger' : '' }}">
                {{ $onboarding['pronta_para_vitrine'] ? 'a conta ja publica precos' : 'publique precos para aparecer no comparador' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Proxima prioridade</span>
            <strong class="metric-value" style="font-size: 1.4rem;">{{ $onboarding['proxima_etapa']['titulo'] ?? 'Conta evoluida' }}</strong>
            <span class="metric-trend">
                {{ $onboarding['proxima_etapa']['descricao'] ?? 'A base principal ja esta toda concluida.' }}
            </span>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Roadmap da implantacao</h2>
                        <p>Esse roteiro foi desenhado para reduzir atrito na entrada e acelerar o primeiro valor percebido.</p>
                    </div>
                </div>

                <div class="onboarding-groups">
                    @foreach ($onboarding['grupos'] as $grupo)
                        <article class="onboarding-group">
                            <div class="progress-meta">
                                <span>{{ $grupo['titulo'] }}</span>
                                <span>{{ $grupo['concluidas'] }}/{{ $grupo['total'] }} concluidas</span>
                            </div>
                            <div class="progress-track">
                                <span class="progress-fill is-teal" style="width: {{ $grupo['percentual'] }}%;"></span>
                            </div>

                            <div class="checklist-stack">
                                @foreach ($grupo['etapas'] as $etapa)
                                    <article class="checklist-item {{ $etapa['concluida'] ? 'is-done' : '' }}">
                                        <div>
                                            <strong>{{ $etapa['titulo'] }}</strong>
                                            <span>{{ $etapa['descricao'] }}</span>
                                        </div>

                                        <div class="checklist-actions">
                                            <span class="status-dot {{ $etapa['concluida'] ? 'is-done' : 'is-pending' }}"></span>
                                            <span class="badge {{ ! $etapa['concluida'] ? 'is-warning' : '' }}">{{ $etapa['concluida'] ? 'concluida' : 'pendente' }}</span>
                                            <a class="{{ $etapa['concluida'] ? 'button-secondary' : 'button' }}" href="{{ $etapa['rota'] }}">
                                                {{ $etapa['concluida'] ? 'Revisar' : $etapa['cta'] }}
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
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
                        <h2>Mapa da conta</h2>
                        <p>Leitura resumida da estrutura ja criada para sair do zero com mais confianca.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['lojas'] }}</strong>
                        <span>lojas cadastradas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['categorias'] }}</strong>
                        <span>categorias financeiras</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['contas_financeiras'] }}</strong>
                        <span>contas financeiras</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['movimentacoes'] }}</strong>
                        <span>movimentacoes financeiras</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['titulos'] }}</strong>
                        <span>titulos a pagar e receber</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $onboarding['totais']['precos'] }}</strong>
                        <span>precos publicados</span>
                    </div>
                </div>

                <div class="signal-list">
                    <article class="signal-item">
                        <strong>{{ $onboarding['pronta_para_operar'] ? 'Conta pronta para operacao financeira' : 'Conta ainda em configuracao operacional' }}</strong>
                        <span>{{ $onboarding['pronta_para_operar'] ? 'Voce ja tem a base minima para operar caixa, lancamentos e previsao.' : 'Conclua lojas, contas financeiras e lancamentos para destravar o uso diario do produto.' }}</span>
                    </article>

                    <article class="signal-item">
                        <strong>{{ $onboarding['pronta_para_vitrine'] ? 'Comparador ja alimentado' : 'Comparador ainda nao alimentado' }}</strong>
                        <span>{{ $onboarding['pronta_para_vitrine'] ? 'A conta ja possui presenca publica via precos cadastrados.' : 'Publique o primeiro preco para conectar a operacao interna com a descoberta publica.' }}</span>
                    </article>
                </div>

                @if ($onboarding['proxima_etapa'])
                    <a class="button" href="{{ $onboarding['proxima_etapa']['rota'] }}">
                        Avancar em {{ strtolower($onboarding['proxima_etapa']['titulo']) }}
                    </a>
                @endif
            </div>
        </article>
    </section>
@endsection
