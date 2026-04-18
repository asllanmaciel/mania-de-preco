@extends('layouts.backoffice')

@section('title', 'Super admin')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.planos.index') }}">Planos</a>
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
            <a class="button-secondary" href="{{ route('cliente.dashboard') }}">Ver area do cliente</a>
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
        <article class="metric"><strong>R$ {{ number_format($metricas['mrr'], 2, ',', '.') }}</strong><span>receita mensal recorrente estimada</span></article>
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
