@extends('layouts.backoffice')

@section('title', 'Planos')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.planos.index') }}">Planos</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>Catalogo de planos</h1>
        <p>Organize a arquitetura comercial do SaaS, ajuste limites, precificacao e estrutura de entrega sem depender de edicoes espalhadas pela base.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <a class="button" href="{{ route('super-admin.planos.create') }}">Criar novo plano</a>
        </div>
    </section>

    <section class="grid-2">
        <article class="metric"><strong>{{ number_format($metricas['ativos'], 0, ',', '.') }}</strong><span>planos ativos</span></article>
        <article class="metric"><strong>{{ number_format($metricas['assinaturas'], 0, ',', '.') }}</strong><span>assinaturas vinculadas aos planos</span></article>
    </section>

    <section class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.planos.index') }}" class="toolbar">
                <div style="display:grid; gap:12px; flex:1;">
                    <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome ou slug" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                </div>
                <div style="display:grid; gap:12px; min-width:220px;">
                    <select name="status" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                        <option value="">Todos os status</option>
                        @foreach (['ativo' => 'Ativo', 'inativo' => 'Inativo'] as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected($statusSelecionado === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="button" type="submit">Filtrar</button>
                    <a class="button-secondary" href="{{ route('super-admin.planos.index') }}">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="grid-2">
        @foreach ($planos as $plano)
            <article class="card">
                <div class="card-body">
                    <div class="section-head">
                        <div>
                            <h2 style="margin:0;">{{ $plano->nome }}</h2>
                            <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">{{ $plano->descricao ?: 'Plano sem descricao publicada.' }}</p>
                        </div>
                        <span class="chip">{{ $plano->status }}</span>
                    </div>

                    <div class="grid-3" style="margin-top:18px;">
                        <div class="mini-card">
                            <strong>R$ {{ number_format((float) $plano->valor_mensal, 2, ',', '.') }}</strong>
                            <span>mensal</span>
                        </div>
                        <div class="mini-card">
                            <strong>R$ {{ number_format((float) $plano->valor_anual, 2, ',', '.') }}</strong>
                            <span>anual</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ number_format($plano->assinaturas_count, 0, ',', '.') }}</strong>
                            <span>assinaturas</span>
                        </div>
                    </div>

                    <div class="grid-3" style="margin-top:18px;">
                        <div class="mini-card">
                            <strong>{{ $plano->limite_usuarios ?: 'livre' }}</strong>
                            <span>usuarios</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ $plano->limite_lojas ?: 'livre' }}</strong>
                            <span>lojas</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ $plano->limite_produtos ?: 'livre' }}</strong>
                            <span>produtos</span>
                        </div>
                    </div>

                    <div class="list" style="margin-top:18px;">
                        @forelse ($plano->recursos ?? [] as $recurso)
                            <div class="list-row">
                                <strong>{{ $recurso }}</strong>
                            </div>
                        @empty
                            <div class="mini-card">
                                <strong>Sem recursos listados</strong>
                                <span>Este plano ainda nao recebeu uma proposta clara de valor.</span>
                            </div>
                        @endforelse
                    </div>

                    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:18px;">
                        <a class="button-secondary" href="{{ route('super-admin.planos.edit', $plano) }}">Editar plano</a>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section>
        {{ $planos->links() }}
    </section>
@endsection
