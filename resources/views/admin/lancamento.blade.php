@extends('layouts.admin')

@section('title', 'Lancamento')
@section('heading', 'Centro de lancamento')
@section('subheading', 'Uma leitura objetiva para saber se a conta esta pronta para ser apresentada, operada e usada por clientes reais.')

@section('content')
    @php
        $primeiraAcao = $prontidao['proximas_acoes']->first();
    @endphp

    <section class="card visual-hero">
        <div class="card-body visual-hero-copy">
            <div class="section-header">
                <div>
                    <span class="pill">Prontidao de lancamento</span>
                    <h2 style="margin:14px 0 0;">{{ $prontidao['nivel']['nome'] }}</h2>
                    <p>{{ $prontidao['nivel']['descricao'] }}</p>
                </div>
            </div>

            <div class="visual-action-grid">
                @if ($prontidao['pronta'])
                    <a class="button" href="{{ route('home') }}"><x-ui.icon name="spark" /> Ver vitrine publica</a>
                @elseif (! empty($primeiraAcao['rota']))
                    <a class="button" href="{{ $primeiraAcao['rota'] }}"><x-ui.icon name="check" /> Resolver proxima acao</a>
                @endif
                <a class="button-secondary" href="{{ route('admin.onboarding') }}"><x-ui.icon name="compass" /> Abrir onboarding</a>
                <a class="button-secondary" href="{{ route('admin.notificacoes') }}"><x-ui.icon name="bell" /> Ver acoes</a>
            </div>
        </div>

        <div class="card-body">
            <div class="score-ring" style="--score: {{ $prontidao['score'] }};">
                <div class="score-ring-inner">
                    <x-ui.icon name="spark" size="30" />
                    <strong>{{ $prontidao['score'] }}</strong>
                    <span>de 100</span>
                </div>
            </div>
        </div>
    </section>

    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Score de lancamento</span>
            <strong class="metric-value">{{ $prontidao['score'] }}%</strong>
            <span class="metric-trend {{ $prontidao['score'] < 75 ? 'is-danger' : '' }}">{{ $prontidao['nivel']['nome'] }}</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Pendencias criticas</span>
            <strong class="metric-value">{{ $prontidao['pendencias_criticas'] }}</strong>
            <span class="metric-trend {{ $prontidao['pendencias_criticas'] > 0 ? 'is-danger' : '' }}">
                {{ $prontidao['pendencias_criticas'] > 0 ? 'resolver antes de apresentar' : 'sem bloqueios criticos' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Produtos na vitrine</span>
            <strong class="metric-value">{{ $prontidao['metricas']['produtos_publicados'] }}</strong>
            <span class="metric-trend">com {{ $prontidao['metricas']['precos_publicados'] }} precos publicados</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Time ativo</span>
            <strong class="metric-value">{{ $prontidao['metricas']['usuarios_ativos'] }}</strong>
            <span class="metric-trend">usuarios prontos para operar</span>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Mapa de prontidao</h2>
                        <p>Cada bloco representa uma frente essencial para transformar a conta em operacao apresentavel e confiavel.</p>
                    </div>
                </div>

                <div class="onboarding-groups">
                    @foreach ($prontidao['grupos'] as $grupo)
                        <article class="onboarding-group">
                            <div class="progress-meta">
                                <span><x-ui.icon :name="$grupo['icone']" /> {{ $grupo['titulo'] }}</span>
                                <span>{{ $grupo['score'] }}%</span>
                            </div>
                            <p class="helper-text">{{ $grupo['descricao'] }}</p>
                            <div class="progress-track">
                                <span class="progress-fill {{ $grupo['score'] >= 75 ? 'is-teal' : '' }}" style="width: {{ $grupo['score'] }}%;"></span>
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
                                            <span class="badge {{ ! $etapa['concluida'] ? 'is-warning' : '' }}">
                                                {{ $etapa['concluida'] ? 'pronto' : ($etapa['critica'] ? 'critico' : 'melhoria') }}
                                            </span>
                                            @if ($etapa['rota'])
                                                <a class="{{ $etapa['concluida'] ? 'button-secondary' : 'button' }}" href="{{ $etapa['rota'] }}">
                                                    {{ $etapa['concluida'] ? 'Revisar' : $etapa['acao'] }}
                                                </a>
                                            @else
                                                <span class="badge is-muted">sem acesso</span>
                                            @endif
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
                        <h2>Proximas acoes</h2>
                        <p>O que mais aumenta a chance de uma boa demonstracao agora.</p>
                    </div>
                </div>

                @if ($prontidao['proximas_acoes']->isEmpty())
                    <div class="empty-state">
                        A conta nao possui pendencias relevantes para lancamento neste momento.
                    </div>
                @else
                    <div class="signal-list">
                        @foreach ($prontidao['proximas_acoes'] as $acao)
                            <article class="signal-item">
                                <strong>{{ $acao['titulo'] }}</strong>
                                <span>{{ $acao['descricao'] }}</span>
                                <small class="helper-text">{{ $acao['grupo'] }} | {{ $acao['critica'] ? 'prioridade critica' : 'melhoria recomendada' }}</small>
                                @if ($acao['rota'])
                                    <a class="ghost-link" href="{{ $acao['rota'] }}">{{ $acao['acao'] }}</a>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $prontidao['metricas']['lojas_ativas'] }}/{{ $prontidao['metricas']['total_lojas'] }}</strong>
                        <span>lojas ativas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $prontidao['metricas']['produtos_com_imagem'] }}</strong>
                        <span>produtos com imagem</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $prontidao['metricas']['avaliacoes'] }}</strong>
                        <span>avaliacoes publicas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $prontidao['metricas']['logs_auditoria'] }}</strong>
                        <span>registros de auditoria</span>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
