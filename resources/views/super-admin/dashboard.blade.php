@extends('layouts.backoffice')

@section('title', 'Super admin')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.analytics') }}">Analytics</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.planos.index') }}">Planos</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
    @if (auth()->user()->possuiAcessoAdmin())
        <a class="chip" href="{{ route('admin.dashboard') }}">Painel lojista</a>
    @endif
    <a class="chip" href="{{ route('cliente.dashboard') }}">Area do cliente</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>Super admin da plataforma</h1>
        <p>Centro de governanca para operar contas, planos e receita recorrente com mais clareza comercial e menos dependencia de processos manuais.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <a class="button" href="{{ route('super-admin.contas.index') }}">Abrir gestao de contas</a>
            <a class="button-secondary" href="{{ route('super-admin.planos.index') }}">Abrir catalogo de planos</a>
            <a class="button-secondary" href="{{ route('super-admin.analytics') }}">Ver analytics</a>
            <a class="button-secondary" href="{{ route('super-admin.suporte.index') }}">Ver chamados</a>
            <a class="button-secondary" href="{{ route('cliente.dashboard') }}">Ver area do cliente</a>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Prontidao global de lancamento</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Checklist executivo para saber o que ainda bloqueia producao, cobranca, experiencia publica e operacao antes de abrir o produto para clientes reais.</p>
                </div>
                <span class="badge {{ $prontidaoLancamento['pendencias_criticas'] > 0 ? 'is-danger' : '' }}">
                    {{ $prontidaoLancamento['pendencias_criticas'] }} bloqueios criticos
                </span>
            </div>

            <div class="readiness-panel" style="margin-top:18px;">
                <aside class="readiness-score">
                    <div>
                        <span class="badge {{ $prontidaoLancamento['pronta'] ? '' : 'is-warning' }}">
                            {{ $prontidaoLancamento['nivel']['nome'] }}
                        </span>
                        <strong style="margin-top:18px;">{{ $prontidaoLancamento['score'] }}%</strong>
                        <p>{{ $prontidaoLancamento['nivel']['descricao'] }}</p>
                    </div>

                    <div class="readiness-actions">
                        @forelse ($prontidaoLancamento['proximas_acoes'] as $acao)
                            <article class="checklist-item" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.12);">
                                <div>
                                    <strong>{{ $acao['titulo'] }}</strong>
                                    <span style="color:rgba(255,255,255,.68);">{{ $acao['grupo'] }} | {{ $acao['critica'] ? 'critico' : 'recomendado' }}</span>
                                </div>
                                @if (! empty($acao['rota']))
                                    <a class="button-secondary" href="{{ $acao['rota'] }}">{{ $acao['acao'] }}</a>
                                @else
                                    <span class="badge is-muted">{{ $acao['acao'] }}</span>
                                @endif
                            </article>
                        @empty
                            <article class="checklist-item" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.12);">
                                <div>
                                    <strong>Nenhuma pendencia relevante</strong>
                                    <span style="color:rgba(255,255,255,.68);">A plataforma esta pronta para uma rodada controlada de lancamento.</span>
                                </div>
                            </article>
                        @endforelse
                    </div>
                </aside>

                <div class="readiness-groups">
                    @foreach ($prontidaoLancamento['grupos'] as $grupo)
                        <article class="readiness-group">
                            <div class="progress-meta">
                                <span><x-ui.icon :name="$grupo['icone']" /> {{ $grupo['titulo'] }}</span>
                                <span>{{ $grupo['score'] }}%</span>
                            </div>
                            <p class="helper-text">{{ $grupo['descricao'] }}</p>
                            <div class="progress-track" style="margin:10px 0 12px;">
                                <span class="progress-fill {{ $grupo['score'] >= 75 ? 'is-teal' : '' }}" style="width: {{ $grupo['score'] }}%;"></span>
                            </div>

                            <div class="checklist-stack">
                                @foreach ($grupo['etapas'] as $etapa)
                                    <article class="checklist-item">
                                        <div>
                                            <strong>{{ $etapa['titulo'] }}</strong>
                                            <span>{{ $etapa['descricao'] }}</span>
                                        </div>
                                        <div class="checklist-actions">
                                            <span class="badge {{ $etapa['concluida'] ? '' : ($etapa['critica'] ? 'is-danger' : 'is-warning') }}">
                                                {{ $etapa['concluida'] ? 'pronto' : ($etapa['critica'] ? 'critico' : 'pendente') }}
                                            </span>
                                            @if (! empty($etapa['rota']))
                                                <a class="{{ $etapa['concluida'] ? 'button-secondary' : 'button' }}" href="{{ $etapa['rota'] }}">
                                                    {{ $etapa['concluida'] ? 'Revisar' : $etapa['acao'] }}
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($metricas['contas'], 0, ',', '.') }}</strong><span>contas monitoradas</span></article>
        <article class="metric"><strong>{{ number_format($metricas['lojas'], 0, ',', '.') }}</strong><span>lojas publicadas</span></article>
        <article class="metric"><strong>{{ number_format($metricas['usuarios'], 0, ',', '.') }}</strong><span>usuarios na base</span></article>
        <article class="metric"><strong>{{ number_format($metricas['produtos'], 0, ',', '.') }}</strong><span>produtos catalogados</span></article>
        <article class="metric"><strong>{{ number_format($metricas['alertas'], 0, ',', '.') }}</strong><span>alertas monitorados</span></article>
        <article class="metric"><strong>{{ number_format($metricas['historicos_precos'], 0, ',', '.') }}</strong><span>eventos de preco</span></article>
        <article class="metric"><strong>{{ number_format($metricas['planos_ativos'], 0, ',', '.') }}</strong><span>planos ativos no portfolio</span></article>
        <article class="metric"><strong>{{ number_format($metricas['assinaturas_ativas'], 0, ',', '.') }}</strong><span>assinaturas em operacao</span></article>
        <article class="metric"><strong>{{ number_format($metricas['chamados_abertos'], 0, ',', '.') }}</strong><span>chamados aguardando suporte</span></article>
        <article class="metric"><strong>{{ number_format($metricas['eventos_24h'], 0, ',', '.') }}</strong><span>eventos de produto nas ultimas 24h</span></article>
        <article class="metric"><strong>{{ number_format($metricas['eventos_publicos_7d'], 0, ',', '.') }}</strong><span>sinais publicos nos ultimos 7 dias</span></article>
        <article class="metric"><strong>R$ {{ number_format($metricas['mrr'], 2, ',', '.') }}</strong><span>receita mensal recorrente estimada</span></article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Sinais de produto</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Eventos reais capturados para entender busca, interesse, cadastro e suporte antes de ampliar investimento em trafego.</p>
                    </div>
                </div>

                <div class="list">
                    @forelse ($eventosPorTipo as $evento)
                        <div class="list-row">
                            <div>
                                <strong>{{ $evento->evento }}</strong>
                                <small>volume nos ultimos 7 dias</small>
                            </div>
                            <span class="badge">{{ number_format($evento->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Nenhum evento capturado ainda</strong>
                            <small>Os sinais aparecem aqui conforme usuarios navegam pela vitrine publica e pelo cadastro.</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Linha do tempo recente</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Ultimas interacoes relevantes registradas pela plataforma.</p>
                    </div>
                </div>

                <div class="list">
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
                            <span class="badge is-muted">{{ $evento->conta?->nome_fantasia ?? 'publico' }}</span>
                        </div>
                    @empty
                        <div class="list-row">
                            <strong>Sem eventos recentes</strong>
                            <small>Quando a vitrine receber uso real, a linha do tempo passa a mostrar os sinais mais novos.</small>
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
                        <h2 style="margin:0;">Contas recentes</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Novas operacoes que chegaram na plataforma.</p>
                    </div>
                </div>
                <div class="list">
                    @foreach ($contasRecentes as $conta)
                        <div class="list-row">
                            <div>
                                <strong><a href="{{ route('super-admin.contas.show', $conta) }}">{{ $conta->nome_fantasia }}</a></strong>
                                <small>{{ $conta->email ?: 'Sem e-mail' }} | {{ $conta->status }}</small>
                            </div>
                            <a class="chip" href="{{ route('super-admin.contas.show', $conta) }}">abrir</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Usuarios recentes</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Base de acesso da plataforma com destaque para perfil estrutural.</p>
                    </div>
                </div>
                <div class="list">
                    @foreach ($usuariosRecentes as $usuario)
                        <div class="list-row">
                            <strong>{{ $usuario->name }}</strong>
                            <small>{{ $usuario->email }} | {{ $usuario->ehSuperAdmin() ? 'super admin' : $usuario->perfilPainel() }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Assinaturas recentes</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Leitura rapida da esteira comercial mais proxima da operacao financeira da plataforma.</p>
                </div>
            </div>
            <div class="list">
                @foreach ($assinaturasRecentes as $assinatura)
                    <div class="list-row">
                        <strong>{{ $assinatura->conta?->nome_fantasia ?? 'Conta indisponivel' }}</strong>
                        <small>
                            {{ $assinatura->plano?->nome ?? 'Sem plano' }} | {{ $assinatura->status }} |
                            {{ $assinatura->ciclo_cobranca }} | R$ {{ number_format((float) $assinatura->valor, 2, ',', '.') }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
