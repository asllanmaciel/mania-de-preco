@extends('layouts.admin')

@section('title', 'Assinatura')
@section('heading', 'Assinatura e plano')
@section('subheading', 'Acompanhe o plano contratado, status comercial, cobranca, limites e capacidade de crescimento da conta.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <h2>{{ $mensagemStatus['titulo'] }}</h2>
                    <p>{{ $mensagemStatus['descricao'] }}</p>
                </div>
                <span class="pill">{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</span>
            </div>

            @if ($assinaturaAtual?->billing_checkout_url)
                <div class="toolbar">
                    <p class="helper-text" style="margin:0;">Existe uma cobranca vinculada a esta assinatura.</p>
                    <a class="button" href="{{ $assinaturaAtual->billing_checkout_url }}" target="_blank" rel="noreferrer">
                        Abrir cobranca
                    </a>
                </div>
            @endif
        </div>
    </section>

    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Plano atual</span>
            <strong class="metric-value">{{ $usoPlano['plano']?->nome ?? 'Sem plano' }}</strong>
            <span class="metric-trend">{{ $assinaturaAtual?->ciclo_cobranca ?? 'ciclo pendente' }}</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Valor contratado</span>
            <strong class="metric-value">R$ {{ number_format((float) ($assinaturaAtual?->valor ?? 0), 2, ',', '.') }}</strong>
            <span class="metric-trend">valor atual da assinatura</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Vigencia</span>
            <strong class="metric-value">{{ $assinaturaAtual?->expira_em?->format('d/m/Y') ?? 'Sem data' }}</strong>
            <span class="metric-trend {{ $assinaturaAtual?->expira_em && $assinaturaAtual->expira_em->isBefore(now()->addDays(7)) ? 'is-danger' : '' }}">
                data final do ciclo atual
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Billing externo</span>
            <strong class="metric-value">{{ $assinaturaAtual?->billing_provider ?: 'Pendente' }}</strong>
            <span class="metric-trend">{{ $assinaturaAtual?->billing_status ?? 'sem sincronizacao' }}</span>
        </article>
    </section>

    <section class="panel-grid">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Consumo do plano</h2>
                        <p>Uso real da conta contra os limites comerciais contratados.</p>
                    </div>
                </div>

                <div class="progress-stack">
                    @foreach ($usoPlano['metricas'] as $metrica)
                        <div class="progress-row">
                            <div class="progress-meta">
                                <span>{{ ucfirst($metrica['rotulo']) }}</span>
                                <span>
                                    {{ number_format($metrica['usado'], 0, ',', '.') }}
                                    @if (! $metrica['ilimitado'])
                                        / {{ number_format($metrica['limite'], 0, ',', '.') }}
                                    @else
                                        / sem limite
                                    @endif
                                </span>
                            </div>
                            <div class="progress-track">
                                <span class="progress-fill {{ $metrica['excedido'] ? '' : 'is-teal' }}" style="width: {{ $metrica['ilimitado'] ? 100 : $metrica['percentual'] }}%;"></span>
                            </div>
                            <small class="helper-text">
                                @if ($metrica['ilimitado'])
                                    Este recurso nao possui limite operacional configurado.
                                @elseif ($metrica['excedido'])
                                    Limite consumido. Avalie upgrade antes de expandir.
                                @elseif ($metrica['em_alerta'])
                                    Uso acima de 80%. Esse e um bom momento para avaliar crescimento.
                                @else
                                    {{ number_format($metrica['disponivel'], 0, ',', '.') }} disponiveis no plano atual.
                                @endif
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Recursos contratados</h2>
                        <p>O que esta incluido na proposta comercial do plano atual.</p>
                    </div>
                </div>

                @if (($usoPlano['plano']?->recursos ?? []) === [])
                    <div class="empty-state">
                        Nenhum recurso foi detalhado para este plano ainda. O super admin pode organizar essa proposta no catalogo de planos.
                    </div>
                @else
                    <div class="signal-list">
                        @foreach ($usoPlano['plano']->recursos as $recurso)
                            <article class="signal-item">
                                <strong>{{ str_replace('_', ' ', ucfirst($recurso)) }}</strong>
                                <span>Recurso incluido no plano {{ $usoPlano['plano']->nome }}.</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Historico comercial</h2>
                        <p>Ultimas assinaturas vinculadas a esta conta.</p>
                    </div>
                </div>

                @if ($historicoAssinaturas->isEmpty())
                    <div class="empty-state">
                        Ainda nao existe historico de assinatura para esta conta.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($historicoAssinaturas as $assinatura)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $assinatura->plano?->nome ?? 'Plano nao informado' }}</strong>
                                    <small>
                                        {{ $assinatura->status }} | {{ $assinatura->ciclo_cobranca }}
                                        | R$ {{ number_format((float) $assinatura->valor, 2, ',', '.') }}
                                    </small>
                                    <br>
                                    <code>
                                        inicio {{ $assinatura->inicia_em?->format('d/m/Y') ?? 'sem data' }}
                                        | expira {{ $assinatura->expira_em?->format('d/m/Y') ?? 'sem data' }}
                                    </code>
                                </div>
                                <span class="badge {{ in_array($assinatura->status, ['inadimplente', 'cancelada', 'encerrada'], true) ? 'is-warning' : '' }}">
                                    {{ $assinatura->billing_status ?? $assinatura->status }}
                                </span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Planos disponiveis</h2>
                        <p>Referencia comercial para entender proximos niveis de crescimento.</p>
                    </div>
                </div>

                <div class="signal-list">
                    @forelse ($planosDisponiveis as $plano)
                        <article class="signal-item">
                            <strong>{{ $plano->nome }}</strong>
                            <span>
                                R$ {{ number_format((float) $plano->valor_mensal, 2, ',', '.') }}/mes
                                | {{ $plano->limite_usuarios ?: 'sem limite' }} usuarios
                                | {{ $plano->limite_lojas ?: 'sem limite' }} lojas
                            </span>
                        </article>
                    @empty
                        <div class="empty-state">
                            Nenhum plano ativo foi cadastrado ainda.
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>
@endsection
