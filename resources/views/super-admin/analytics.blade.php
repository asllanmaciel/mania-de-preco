@extends('layouts.backoffice')

@section('title', 'Analytics')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('content')
    <style>
        .analytics-hero {
            display:grid;
            grid-template-columns:minmax(0, 1.12fr) minmax(280px, .88fr);
            gap:18px;
            align-items:stretch;
        }

        .analytics-hero-panel {
            position:relative;
            overflow:hidden;
            padding:30px;
            border-radius:var(--radius-xl);
            background:
                radial-gradient(circle at 88% 10%, rgba(19,222,185,.16), transparent 28%),
                linear-gradient(135deg, #19202e, #263657);
            color:#fff;
            box-shadow:var(--shadow);
        }

        .analytics-hero-panel::after {
            content:"";
            position:absolute;
            right:-70px;
            bottom:-90px;
            width:260px;
            height:260px;
            border-radius:50%;
            border:1px solid rgba(255,255,255,.14);
        }

        .analytics-hero-panel h1 {
            position:relative;
            margin:16px 0 0;
            max-width:760px;
            font-size:clamp(2rem, 4vw, 3.1rem);
            line-height:1;
            letter-spacing:var(--tracking-tight);
        }

        .analytics-hero-panel p {
            position:relative;
            margin:14px 0 0;
            max-width:760px;
            color:rgba(255,255,255,.72);
            line-height:1.7;
        }

        .period-tabs {
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }

        .period-tabs .chip {
            width:auto;
            min-height:38px;
            background:rgba(255,255,255,.08);
            color:#fff;
            border-color:rgba(255,255,255,.12);
        }

        .period-tabs .chip.is-active {
            color:#19202e;
            background:#fff;
        }

        .signal-card {
            display:grid;
            gap:14px;
            padding:22px;
            border-radius:var(--radius-xl);
            background:var(--surface);
            border:1px solid var(--line);
            box-shadow:var(--shadow);
        }

        .signal-card strong {
            font-size:2rem;
            letter-spacing:-.06em;
        }

        .bar-chart {
            display:flex;
            align-items:end;
            gap:8px;
            min-height:210px;
            padding:18px;
            border-radius:22px;
            background:linear-gradient(180deg, #fff, #fff8f0);
            border:1px solid var(--line);
        }

        .bar-column {
            display:grid;
            grid-template-rows:1fr auto;
            gap:8px;
            align-items:end;
            min-width:22px;
            flex:1;
            color:var(--muted);
            font-size:.7rem;
            text-align:center;
        }

        .bar-column span:first-child {
            display:block;
            min-height:8px;
            border-radius:999px 999px 6px 6px;
            background:linear-gradient(180deg, var(--primary), #ffb067);
            box-shadow:0 10px 22px rgba(244,90,36,.18);
        }

        .funnel-stack {
            display:grid;
            gap:12px;
        }

        .funnel-row {
            display:grid;
            grid-template-columns:220px minmax(0, 1fr) auto;
            gap:14px;
            align-items:center;
            padding:14px;
            border-radius:18px;
            background:var(--surface-soft);
            border:1px solid var(--line);
        }

        .funnel-row strong { display:block; margin-bottom:4px; }
        .funnel-row small { color:var(--muted); line-height:1.5; }

        .analytics-progress {
            height:12px;
            overflow:hidden;
            border-radius:999px;
            background:#e9eef7;
        }

        .analytics-progress span {
            display:block;
            height:100%;
            border-radius:inherit;
            background:linear-gradient(90deg, var(--primary), var(--success));
        }

        .event-row {
            display:grid;
            grid-template-columns:minmax(0, 1fr) minmax(120px, 180px) auto;
            gap:14px;
            align-items:center;
            padding:14px;
            border-radius:18px;
            border:1px solid var(--line);
            background:#fff;
        }

        .event-row code {
            color:#1f2a44;
            font:800 .82rem var(--font-mono);
        }

        @media (max-width:1100px) {
            .analytics-hero, .funnel-row, .event-row { grid-template-columns:1fr; }
            .bar-chart { overflow-x:auto; }
            .bar-column { min-width:34px; }
        }
    </style>

    <section class="analytics-hero">
        <article class="analytics-hero-panel">
            <span class="badge">inteligencia de produto</span>
            <h1>Analytics para saber onde o Mania de Preço está ganhando tração.</h1>
            <p>Leitura executiva de uso real: busca, intenção de compra, cadastro, alertas e canais. A tela foi pensada para acompanhar o lançamento sem depender de planilhas soltas.</p>

            <div class="period-tabs" style="margin-top:22px;">
                @foreach ([7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $dias => $label)
                    <a class="chip {{ $periodo === $dias ? 'is-active' : '' }}" href="{{ route('super-admin.analytics', ['periodo' => $dias]) }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </article>

        <aside class="signal-card">
            <span class="badge is-muted">janela analisada</span>
            <strong>{{ $periodo }} dias</strong>
            <p style="margin:0; color:var(--muted); line-height:1.7;">Use esta visão para decidir aquisição, UX, priorização do app e quais vitrines merecem destaque.</p>
            <a class="button-secondary" href="{{ route('super-admin.dashboard') }}">Voltar para visão geral</a>
        </aside>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($metricas['eventos'], 0, ',', '.') }}</strong><span>eventos no período</span></article>
        <article class="metric"><strong>{{ number_format($metricas['visitantes_estimados'], 0, ',', '.') }}</strong><span>visitantes estimados por IP</span></article>
        <article class="metric"><strong>{{ number_format($metricas['cadastros'], 0, ',', '.') }}</strong><span>cadastros de clientes</span></article>
        <article class="metric"><strong>{{ number_format($metricas['alertas_criados'], 0, ',', '.') }}</strong><span>alertas criados no app</span></article>
        <article class="metric"><strong>{{ number_format($metricas['eventos_mobile'], 0, ',', '.') }}</strong><span>sinais mobile</span></article>
        <article class="metric"><strong>{{ number_format($metricas['eventos_publicos'], 0, ',', '.') }}</strong><span>sinais públicos</span></article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Ritmo diário</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Volume de eventos por dia para enxergar picos após divulgação, testes e campanhas.</p>
                    </div>
                </div>

                <div class="bar-chart" style="margin-top:18px;">
                    @foreach ($serieDiaria as $dia)
                        <div class="bar-column" title="{{ $dia['total'] }} eventos em {{ $dia['label'] }}">
                            <span style="height:{{ $dia['altura'] }}%;"></span>
                            <small>{{ $dia['label'] }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Canais</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Distribuição entre vitrine pública, app mobile, autenticação e suporte.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @forelse ($eventosPorArea as $area)
                        <div class="list-row">
                            <div>
                                <strong>{{ ucfirst($area['area']) }}</strong>
                                <small>{{ $area['percentual'] }}% dos eventos do período</small>
                                <div class="analytics-progress" style="margin-top:8px;"><span style="width:{{ $area['percentual'] }}%;"></span></div>
                            </div>
                            <span class="badge">{{ number_format($area['total'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Nenhum canal com evento ainda</strong>
                            <small>Quando usuários navegarem, os canais aparecem aqui.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Funil de conversão</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Da descoberta de oferta até o alerta criado, que é um dos sinais mais fortes de recorrência.</p>
                </div>
            </div>

            <div class="funnel-stack" style="margin-top:18px;">
                @foreach ($funil as $etapa)
                    <article class="funnel-row">
                        <div>
                            <strong>{{ $etapa['titulo'] }}</strong>
                            <small>{{ $etapa['descricao'] }}</small>
                        </div>
                        <div class="analytics-progress"><span style="width:{{ $etapa['percentual'] }}%;"></span></div>
                        <span class="badge">{{ number_format($etapa['total'], 0, ',', '.') }}</span>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Eventos mais fortes</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Ajuda a entender quais ações estão aparecendo mais durante o lançamento.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @forelse ($eventosPorTipo as $evento)
                        <article class="event-row">
                            <code>{{ $evento['evento'] }}</code>
                            <div class="analytics-progress"><span style="width:{{ $evento['percentual'] }}%;"></span></div>
                            <span class="badge">{{ number_format($evento['total'], 0, ',', '.') }}</span>
                        </article>
                    @empty
                        <article class="list-row">
                            <strong>Nenhum evento capturado</strong>
                            <small>Abra a home pública, filtre ofertas ou use o app/API para popular esta visão.</small>
                        </article>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Linha do tempo</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Últimos sinais recebidos pela plataforma.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @forelse ($eventosRecentes as $evento)
                        <div class="list-row">
                            <div>
                                <strong>{{ $evento->evento }}</strong>
                                <small>
                                    {{ $evento->area }} |
                                    {{ $evento->usuario?->email ?? 'visitante' }} |
                                    {{ $evento->ocorreu_em?->format('d/m H:i') }}
                                </small>
                            </div>
                            <span class="badge is-muted">{{ $evento->conta?->nome_fantasia ?? 'público' }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Sem eventos recentes</strong>
                            <small>Os eventos aparecem conforme a vitrine, app e cadastros forem usados.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Produtos com mais interesse</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Produtos mais visualizados no período analisado.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @forelse ($produtosMaisVistos as $produto)
                        <div class="list-row">
                            <strong>{{ $produto['nome'] }}</strong>
                            <span class="badge">{{ number_format($produto['total'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Nenhum produto ranqueado</strong>
                            <small>Abra páginas de produto para gerar esse ranking.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Lojas com mais interesse</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Lojas mais abertas pelos consumidores no período.</p>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @forelse ($lojasMaisVistas as $loja)
                        <div class="list-row">
                            <strong>{{ $loja['nome'] }}</strong>
                            <span class="badge">{{ number_format($loja['total'], 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Nenhuma loja ranqueada</strong>
                            <small>Abra páginas de loja para gerar esse ranking.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>
@endsection
