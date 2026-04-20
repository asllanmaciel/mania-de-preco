@extends('layouts.backoffice')

@section('title', 'Roadmap de lançamento')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preço | Super Admin')

@section('content')
    @php
        $faseAtual = $roadmap['fase_atual'];
        $proximaAcao = $roadmap['proxima_acao'];
    @endphp

    <style>
        .launch-roadmap-hero {
            position:relative;
            overflow:hidden;
            padding:30px;
            background:
                radial-gradient(circle at 86% 18%, rgba(244,90,36,.18), transparent 24%),
                linear-gradient(135deg, rgba(255,255,255,.96), rgba(255,248,240,.92));
        }
        .launch-roadmap-hero::after {
            content:"";
            position:absolute;
            right:34px;
            bottom:-54px;
            width:230px;
            height:230px;
            border-radius:999px;
            border:1px solid rgba(244,90,36,.18);
            background:radial-gradient(circle, rgba(19,222,185,.18), transparent 64%);
            pointer-events:none;
        }
        .launch-hero-content {
            position:relative;
            z-index:1;
            display:grid;
            grid-template-columns:minmax(0, 1fr) 320px;
            gap:24px;
            align-items:end;
        }
        .launch-hero-content h1 {
            margin:14px 0 0;
            max-width:880px;
            font-size:clamp(2rem, 4vw, 3.5rem);
            line-height:.98;
            letter-spacing:var(--tracking-tight);
        }
        .launch-hero-content p {
            margin:14px 0 0;
            max-width:780px;
            color:var(--muted);
            line-height:1.75;
        }
        .launch-score-card {
            padding:22px;
            border-radius:24px;
            background:#21140f;
            color:#fff;
            box-shadow:0 24px 50px rgba(33,20,15,.16);
        }
        .launch-score-card strong {
            display:block;
            font-size:3rem;
            line-height:1;
            letter-spacing:-.08em;
        }
        .launch-score-card span {
            display:block;
            margin-top:8px;
            color:rgba(255,255,255,.72);
            line-height:1.55;
        }
        .launch-summary-grid {
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:14px;
        }
        .launch-summary-card {
            padding:18px;
            border-radius:20px;
            background:rgba(255,255,255,.72);
            border:1px solid var(--line);
            box-shadow:0 10px 26px rgba(31,42,68,.04);
        }
        .launch-summary-card strong {
            display:block;
            margin-top:8px;
            font-size:1.25rem;
            letter-spacing:-.04em;
        }
        .launch-summary-card span {
            display:block;
            margin-top:6px;
            color:var(--muted);
            font-size:.86rem;
            line-height:1.55;
        }
        .launch-roadmap-grid {
            display:grid;
            grid-template-columns:repeat(2,minmax(0,1fr));
            gap:18px;
        }
        .launch-phase {
            display:grid;
            gap:16px;
            padding:22px;
        }
        .launch-phase-head {
            display:flex;
            justify-content:space-between;
            gap:16px;
            align-items:flex-start;
        }
        .launch-phase-title {
            display:flex;
            gap:12px;
            align-items:flex-start;
        }
        .launch-phase-icon {
            display:grid;
            place-items:center;
            flex:0 0 auto;
            width:46px;
            height:46px;
            border-radius:16px;
            color:var(--primary);
            background:var(--primary-soft);
            border:1px solid var(--line);
        }
        .launch-phase h2 {
            margin:0;
            font-size:1.28rem;
            letter-spacing:-.04em;
        }
        .launch-phase p {
            margin:7px 0 0;
            color:var(--muted);
            line-height:1.65;
        }
        .launch-phase small {
            display:block;
            margin-top:8px;
            color:#98643e;
            font-weight:800;
        }
        .launch-checklist {
            display:grid;
            gap:10px;
        }
        .launch-check {
            display:grid;
            grid-template-columns:32px minmax(0,1fr) auto;
            gap:12px;
            align-items:center;
            padding:13px;
            border-radius:16px;
            background:var(--surface-soft);
            border:1px solid var(--line);
        }
        .launch-check-icon {
            display:grid;
            place-items:center;
            width:32px;
            height:32px;
            border-radius:12px;
            background:#fff;
            color:var(--muted);
            border:1px solid var(--line);
        }
        .launch-check.is-done .launch-check-icon {
            color:#0f8f78;
            background:var(--success-soft);
            border-color:#c8f7ed;
        }
        .launch-check strong {
            display:block;
            font-size:.92rem;
        }
        .launch-check span {
            display:block;
            margin-top:3px;
            color:var(--muted);
            font-size:.78rem;
            line-height:1.45;
        }
        .launch-action-list {
            display:grid;
            gap:12px;
        }
        .launch-action {
            display:grid;
            grid-template-columns:44px minmax(0,1fr) auto;
            gap:12px;
            align-items:center;
            padding:14px;
            border-radius:18px;
            border:1px solid var(--line);
            background:#fff;
        }
        .launch-action .launch-phase-icon {
            width:44px;
            height:44px;
            border-radius:15px;
        }
        @media (max-width: 1080px) {
            .launch-hero-content,
            .launch-roadmap-grid {
                grid-template-columns:1fr;
            }
            .launch-summary-grid {
                grid-template-columns:repeat(2,minmax(0,1fr));
            }
        }
        @media (max-width: 680px) {
            .launch-roadmap-hero {
                padding:22px;
            }
            .launch-summary-grid {
                grid-template-columns:1fr;
            }
            .launch-check,
            .launch-action {
                grid-template-columns:32px minmax(0,1fr);
            }
            .launch-check .badge,
            .launch-action .button,
            .launch-action .button-secondary {
                grid-column:1 / -1;
                justify-self:start;
            }
        }
    </style>

    <section class="card launch-roadmap-hero">
        <div class="launch-hero-content">
            <div>
                <span class="badge {{ $roadmap['status']['classe'] }}">{{ $roadmap['status']['label'] }}</span>
                <h1>Roadmap vivo até o lançamento</h1>
                <p>{{ $roadmap['status']['descricao'] }} Esta tela organiza o que já está pronto, o que ainda bloqueia o lançamento e quais frentes ficam para depois do MVP.</p>

                <div style="margin-top:20px; display:flex; gap:12px; flex-wrap:wrap;">
                    @if ($proximaAcao && ! empty($proximaAcao['rota']))
                        <a class="button" href="{{ $proximaAcao['rota'] }}">{{ $proximaAcao['acao'] }}</a>
                    @elseif ($proximaAcao)
                        <span class="button">{{ $proximaAcao['acao'] }}</span>
                    @endif
                    <a class="button-secondary" href="{{ route('super-admin.dashboard') }}">Ver prontidão global</a>
                    <a class="button-secondary" href="{{ route('super-admin.analytics') }}">Ver analytics</a>
                </div>
            </div>

            <aside class="launch-score-card">
                <span>progresso geral</span>
                <strong>{{ $roadmap['score'] }}%</strong>
                <span>fase atual: {{ $faseAtual['titulo'] }}</span>
                <div class="progress-track" style="margin-top:18px; background:rgba(255,255,255,.16);">
                    <span class="progress-fill is-teal" style="width: {{ $roadmap['score'] }}%;"></span>
                </div>
            </aside>
        </div>
    </section>

    <section class="launch-summary-grid">
        <article class="launch-summary-card">
            <span class="badge {{ $faseAtual['status']['classe'] }}">{{ $faseAtual['status']['label'] }}</span>
            <strong>{{ $faseAtual['titulo'] }}</strong>
            <span>fase mais próxima de virar o próximo marco real de lançamento.</span>
        </article>
        <article class="launch-summary-card">
            <span class="badge {{ $roadmap['pendencias_criticas'] > 0 ? 'is-danger' : '' }}">bloqueios</span>
            <strong>{{ $roadmap['pendencias_criticas'] }} críticos</strong>
            <span>itens que precisam estar resolvidos antes de abrir produção.</span>
        </article>
        <article class="launch-summary-card">
            <span class="badge is-muted">base demo</span>
            <strong>{{ number_format($roadmap['metricas']['produtos'], 0, ',', '.') }} produtos</strong>
            <span>{{ number_format($roadmap['metricas']['precos'], 0, ',', '.') }} preços e {{ number_format($roadmap['metricas']['lojas_ativas'], 0, ',', '.') }} lojas ativas na vitrine.</span>
        </article>
        <article class="launch-summary-card">
            <span class="badge is-muted">receita</span>
            <strong>{{ number_format($roadmap['metricas']['assinaturas_operacionais'], 0, ',', '.') }} assinaturas</strong>
            <span>{{ number_format($roadmap['metricas']['planos_ativos'], 0, ',', '.') }} planos ativos para validar venda e cobrança.</span>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <span class="badge {{ $preflight['status']['classe'] }}">pré-flight</span>
                    <h2 style="margin:10px 0 0;">Checklist operacional de produção</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">{{ $preflight['status']['descricao'] }} Você também pode rodar esta mesma análise no Docker com <strong>php artisan launch:check</strong>.</p>
                </div>
                <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <span class="badge {{ $preflight['bloqueios_criticos'] > 0 ? 'is-danger' : '' }}">{{ $preflight['bloqueios_criticos'] }} bloqueios críticos</span>
                    <span class="badge is-muted">{{ $preflight['score'] }}% pronto</span>
                </div>
            </div>

            <div class="grid-3" style="margin-top:18px;">
                @foreach ($preflight['grupos'] as $grupo)
                    <article class="mini-card">
                        <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                            <span class="launch-phase-icon" style="width:42px; height:42px; border-radius:15px;"><x-ui.icon :name="$grupo['icone']" /></span>
                            <span class="badge {{ $grupo['status']['classe'] }}">{{ $grupo['score'] }}%</span>
                        </div>
                        <strong style="font-size:1.08rem; margin-top:14px;">{{ $grupo['titulo'] }}</strong>
                        <span>{{ $grupo['descricao'] }}</span>
                        <div class="progress-track" style="margin-top:12px;">
                            <span class="progress-fill {{ $grupo['score'] >= 70 ? 'is-teal' : '' }}" style="width: {{ $grupo['score'] }}%;"></span>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="launch-action-list" style="margin-top:18px;">
                @forelse ($preflight['pendencias']->take(8) as $pendencia)
                    <article class="launch-action">
                        <span class="launch-phase-icon"><x-ui.icon :name="$pendencia['icone']" /></span>
                        <span>
                            <strong>{{ $pendencia['titulo'] }}</strong>
                            <span style="display:block; margin-top:4px; color:var(--muted); line-height:1.5;">
                                {{ $pendencia['grupo'] }} | {{ $pendencia['critica'] ? 'crítico' : 'recomendado' }}
                                @if (! empty($pendencia['env']))
                                    | {{ $pendencia['env'] }}: {{ $pendencia['valor'] }}
                                @endif
                            </span>
                        </span>
                        @if (! empty($pendencia['rota']))
                            <a class="{{ $pendencia['critica'] ? 'button' : 'button-secondary' }}" href="{{ $pendencia['rota'] }}">{{ $pendencia['acao'] }}</a>
                        @else
                            <span class="badge {{ $pendencia['critica'] ? 'is-danger' : 'is-warning' }}">{{ $pendencia['acao'] }}</span>
                        @endif
                    </article>
                @empty
                    <article class="launch-action">
                        <span class="launch-phase-icon"><x-ui.icon name="check" /></span>
                        <span>
                            <strong>Pré-flight sem bloqueios</strong>
                            <span style="display:block; margin-top:4px; color:var(--muted); line-height:1.5;">A configuração crítica está pronta para a rodada de QA final.</span>
                        </span>
                    </article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Próximas ações</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Ordem sugerida para reduzir risco de lançamento sem perder velocidade.</p>
                    </div>
                    <span class="badge {{ $roadmap['pendencias']->where('critica', true)->count() > 0 ? 'is-danger' : '' }}">{{ $roadmap['pendencias']->count() }} pendências</span>
                </div>

                <div class="launch-action-list" style="margin-top:16px;">
                    @forelse ($roadmap['pendencias']->take(6) as $pendencia)
                        <article class="launch-action">
                            <span class="launch-phase-icon"><x-ui.icon :name="$pendencia['icone']" /></span>
                            <span>
                                <strong>{{ $pendencia['titulo'] }}</strong>
                                <span style="display:block; margin-top:4px; color:var(--muted); line-height:1.5;">{{ $pendencia['fase'] }} | {{ $pendencia['critica'] ? 'crítico' : 'recomendado' }}</span>
                            </span>
                            @if (! empty($pendencia['rota']))
                                <a class="{{ $pendencia['critica'] ? 'button' : 'button-secondary' }}" href="{{ $pendencia['rota'] }}">{{ $pendencia['acao'] }}</a>
                            @else
                                <span class="badge {{ $pendencia['critica'] ? 'is-danger' : 'is-warning' }}">{{ $pendencia['acao'] }}</span>
                            @endif
                        </article>
                    @empty
                        <article class="launch-action">
                            <span class="launch-phase-icon"><x-ui.icon name="check" /></span>
                            <span>
                                <strong>Nenhuma pendência aberta</strong>
                                <span style="display:block; margin-top:4px; color:var(--muted); line-height:1.5;">O roteiro está pronto para uma rodada de validação final.</span>
                            </span>
                        </article>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Linha de lançamento</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Do MVP web à expansão pós-lançamento, com o que deve ou não bloquear a estreia.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:16px;">
                    @foreach ($roadmap['fases'] as $fase)
                        <div class="list-row">
                            <div>
                                <strong>{{ $fase['titulo'] }}</strong>
                                <small>{{ $fase['marco'] }} | {{ $fase['concluidos'] }}/{{ $fase['total'] }} itens prontos</small>
                                <div class="progress-track" style="margin-top:10px;">
                                    <span class="progress-fill {{ $fase['score'] >= 70 ? 'is-teal' : '' }}" style="width: {{ $fase['score'] }}%;"></span>
                                </div>
                            </div>
                            <span class="badge {{ $fase['status']['classe'] }}">{{ $fase['score'] }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="launch-roadmap-grid">
        @foreach ($roadmap['fases'] as $fase)
            <article class="card launch-phase" id="fase-{{ $fase['codigo'] }}">
                <div class="launch-phase-head">
                    <div class="launch-phase-title">
                        <span class="launch-phase-icon"><x-ui.icon :name="$fase['icone']" /></span>
                        <div>
                            <h2>{{ $fase['titulo'] }}</h2>
                            <p>{{ $fase['descricao'] }}</p>
                            <small>Marco: {{ $fase['marco'] }}</small>
                        </div>
                    </div>
                    <span class="badge {{ $fase['status']['classe'] }}">{{ $fase['status']['label'] }}</span>
                </div>

                <div>
                    <div class="progress-meta">
                        <span>{{ $fase['concluidos'] }} de {{ $fase['total'] }} prontos</span>
                        <span>{{ $fase['score'] }}%</span>
                    </div>
                    <div class="progress-track" style="margin-top:8px;">
                        <span class="progress-fill {{ $fase['score'] >= 70 ? 'is-teal' : '' }}" style="width: {{ $fase['score'] }}%;"></span>
                    </div>
                </div>

                <div class="launch-checklist">
                    @foreach ($fase['itens'] as $item)
                        <article class="launch-check {{ $item['concluida'] ? 'is-done' : '' }}">
                            <span class="launch-check-icon">
                                <x-ui.icon :name="$item['concluida'] ? 'check' : ($item['critica'] ? 'alert' : 'circle')" />
                            </span>
                            <span>
                                <strong>{{ $item['titulo'] }}</strong>
                                <span>{{ $item['descricao'] }}</span>
                            </span>
                            @if (! empty($item['rota']))
                                <a class="{{ $item['concluida'] ? 'button-secondary' : 'button' }}" href="{{ $item['rota'] }}">{{ $item['concluida'] ? 'Revisar' : $item['acao'] }}</a>
                            @else
                                <span class="badge {{ $item['concluida'] ? '' : ($item['critica'] ? 'is-danger' : 'is-warning') }}">{{ $item['concluida'] ? 'pronto' : ($item['critica'] ? 'crítico' : 'pendente') }}</span>
                            @endif
                        </article>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
@endsection
