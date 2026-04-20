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
    <style>
        .support-board {
            display:grid;
            grid-template-columns:repeat(3, minmax(0, 1fr));
            gap:16px;
        }

        .support-card {
            position:relative;
            display:grid;
            gap:18px;
            min-height:100%;
            padding:22px;
            border-radius:24px;
            background:var(--surface);
            border:1px solid var(--line);
            box-shadow:var(--shadow);
            transition:.18s ease;
        }

        .support-card:hover {
            transform:translateY(-3px);
            border-color:rgba(244,90,36,.28);
            box-shadow:0 20px 42px rgba(31,42,68,.10);
        }

        .support-card::before {
            content:"";
            position:absolute;
            inset:0 auto 0 0;
            width:5px;
            border-radius:24px 0 0 24px;
            background:var(--success);
        }

        .support-card.is-critica::before { background:var(--danger); }
        .support-card.is-alta::before { background:var(--warning); }
        .support-card.is-resolvido::before { background:#9aa6b8; }

        .support-card-head {
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:14px;
        }

        .support-card h2 {
            margin:8px 0 0;
            font-size:1.15rem;
            line-height:1.25;
            letter-spacing:-.035em;
        }

        .support-card p {
            margin:0;
            color:var(--muted);
            line-height:1.65;
        }

        .support-card-meta {
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .support-card-footer {
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:12px;
            color:var(--muted);
            font-size:.85rem;
        }

        .support-arrow {
            display:inline-grid;
            place-items:center;
            width:38px;
            height:38px;
            border-radius:999px;
            color:var(--primary);
            background:var(--primary-soft);
            border:1px solid var(--line);
            font-weight:900;
        }

        @media (max-width:1180px) {
            .support-board { grid-template-columns:repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width:720px) {
            .support-board { grid-template-columns:1fr; }
            .support-card-footer { align-items:flex-start; flex-direction:column; }
        }
    </style>

    <section class="card hero">
        <h1>Central de suporte</h1>
        <p>Fila operacional em cards para priorizar incidentes, dúvidas de cobrança e sinais de atrito antes que eles virem churn.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <a class="button" href="{{ route('suporte') }}">Ver página pública</a>
            <a class="button-secondary" href="{{ route('super-admin.suporte.index') }}">Atualizar fila</a>
        </div>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($metricas['abertos'], 0, ',', '.') }}</strong><span>chamados em aberto</span></article>
        <article class="metric"><strong>{{ number_format($metricas['criticos'], 0, ',', '.') }}</strong><span>prioridade crítica</span></article>
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

    <section class="support-board">
        @forelse ($chamados as $chamado)
            @php
                $cardClass = match ($chamado->prioridade) {
                    'critica' => 'is-critica',
                    'alta' => 'is-alta',
                    default => '',
                };

                if (in_array($chamado->status, ['resolvido', 'fechado'], true)) {
                    $cardClass = 'is-resolvido';
                }
            @endphp

            <a class="support-card {{ $cardClass }}" href="{{ route('super-admin.suporte.show', $chamado) }}" aria-label="Abrir chamado {{ $chamado->protocolo }}">
                <div class="support-card-head">
                    <div>
                        <span class="badge is-muted">{{ $chamado->protocolo }}</span>
                        <h2>{{ $chamado->assunto }}</h2>
                    </div>
                    <span class="support-arrow">›</span>
                </div>

                <p>{{ str($chamado->mensagem)->limit(150) }}</p>

                <div class="support-card-meta">
                    <span class="badge {{ $chamado->status === 'novo' ? 'is-warning' : (in_array($chamado->status, ['resolvido', 'fechado'], true) ? 'is-muted' : '') }}">
                        {{ $chamado->statusLabel() }}
                    </span>
                    <span class="badge {{ $chamado->prioridade === 'critica' ? 'is-danger' : ($chamado->prioridade === 'alta' ? 'is-warning' : 'is-muted') }}">
                        {{ $chamado->prioridadeLabel() }}
                    </span>
                    <span class="badge is-muted">{{ $chamado->categoriaLabel() }}</span>
                </div>

                <div class="support-card-footer">
                    <span>{{ $chamado->nome }} | {{ $chamado->empresa ?: 'sem empresa' }}</span>
                    <span>{{ $chamado->created_at->format('d/m H:i') }}</span>
                </div>
            </a>
        @empty
            <article class="card" style="grid-column:1 / -1;">
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
