@extends('layouts.backoffice')

@section('title', 'Suporte')
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
        <h1>Central de suporte</h1>
        <p>Fila operacional para acompanhar pedidos de clientes, incidentes, duvidas de cobranca e sinais de atrito antes que eles virem churn.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <a class="button" href="{{ route('suporte') }}">Ver pagina publica</a>
            <a class="button-secondary" href="{{ route('super-admin.suporte.index') }}">Atualizar fila</a>
        </div>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($metricas['abertos'], 0, ',', '.') }}</strong><span>chamados em aberto</span></article>
        <article class="metric"><strong>{{ number_format($metricas['criticos'], 0, ',', '.') }}</strong><span>prioridade critica</span></article>
        <article class="metric"><strong>{{ number_format($metricas['resolvidos'], 0, ',', '.') }}</strong><span>resolvidos ou fechados</span></article>
    </section>

    <section class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.suporte.index') }}" class="toolbar">
                <div style="display:grid; gap:12px; flex:1;">
                    <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por protocolo, nome, e-mail, empresa ou assunto" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                </div>
                <div style="display:grid; gap:12px; min-width:220px;">
                    <select name="status" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                        <option value="">Todos os status</option>
                        @foreach ($statusDisponiveis as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected($statusSelecionado === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:grid; gap:12px; min-width:220px;">
                    <select name="categoria" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                        <option value="">Todas as categorias</option>
                        @foreach ($categoriasDisponiveis as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected($categoriaSelecionada === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="button" type="submit">Filtrar</button>
                    <a class="button-secondary" href="{{ route('super-admin.suporte.index') }}">Limpar</a>
                </div>
            </form>
        </div>
    </section>

    <section class="grid-2">
        @forelse ($chamados as $chamado)
            <article class="card">
                <div class="card-body">
                    <div class="section-head">
                        <div>
                            <h2 style="margin:0;">{{ $chamado->assunto }}</h2>
                            <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">
                                {{ $chamado->protocolo }} | {{ $chamado->nome }} | {{ $chamado->email }}
                            </p>
                        </div>
                        <span class="chip">{{ $chamado->statusLabel() }}</span>
                    </div>

                    <div class="grid-3" style="margin-top:18px;">
                        <div class="mini-card">
                            <strong>{{ $chamado->categoriaLabel() }}</strong>
                            <span>categoria</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ $chamado->prioridadeLabel() }}</strong>
                            <span>prioridade</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ $chamado->created_at->format('d/m H:i') }}</strong>
                            <span>entrada</span>
                        </div>
                    </div>

                    <div class="list" style="margin-top:18px;">
                        <div class="list-row">
                            <strong>Mensagem</strong>
                            <small>{{ $chamado->mensagem }}</small>
                        </div>
                        <div class="list-row">
                            <strong>Contexto</strong>
                            <small>
                                Empresa: {{ $chamado->empresa ?: 'nao informada' }} |
                                Conta vinculada: {{ $chamado->conta?->nome_fantasia ?? 'sem vinculo' }} |
                                Origem: {{ $chamado->origem_url ?: 'nao informada' }}
                            </small>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('super-admin.suporte.update', $chamado) }}" style="display:grid; gap:12px; margin-top:18px;">
                        @csrf
                        @method('PATCH')
                        <div class="grid-2">
                            <label style="display:grid; gap:8px;">
                                <span>Status</span>
                                <select name="status" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                                    @foreach ($statusDisponiveis as $valor => $rotulo)
                                        <option value="{{ $valor }}" @selected(old('status', $chamado->status) === $valor)>{{ $rotulo }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label style="display:grid; gap:8px;">
                                <span>Prioridade</span>
                                <select name="prioridade" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                                    @foreach ($prioridadesDisponiveis as $valor => $rotulo)
                                        <option value="{{ $valor }}" @selected(old('prioridade', $chamado->prioridade) === $valor)>{{ $rotulo }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <label style="display:grid; gap:8px;">
                            <span>Observacao interna</span>
                            <textarea name="observacao_interna" rows="4" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">{{ old('observacao_interna', $chamado->observacao_interna) }}</textarea>
                        </label>

                        <div style="display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end;">
                            <button class="button" type="submit">Atualizar chamado</button>
                        </div>
                    </form>
                </div>
            </article>
        @empty
            <article class="card">
                <div class="card-body">
                    <h2 style="margin:0;">Fila limpa</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Nenhum chamado encontrado para os filtros atuais. Bom sinal, mas continue monitorando os pontos de atrito da jornada.</p>
                </div>
            </article>
        @endforelse
    </section>

    <section>
        {{ $chamados->links() }}
    </section>
@endsection
