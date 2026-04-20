@extends('layouts.backoffice')

@section('title', 'Chamado ' . $chamado->protocolo)
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
    <a class="chip" href="{{ route('super-admin.suporte.show', $chamado) }}">Detalhe</a>
@endsection

@section('content')
    <style>
        .ticket-shell {
            display:grid;
            grid-template-columns:minmax(0, 1.15fr) minmax(320px, .85fr);
            gap:18px;
            align-items:start;
        }

        .ticket-message {
            padding:22px;
            border-radius:22px;
            background:linear-gradient(180deg, #fff, #fff8f0);
            border:1px solid var(--line);
            color:#293244;
            line-height:1.8;
            white-space:pre-wrap;
        }

        .ticket-timeline {
            display:grid;
            gap:12px;
        }

        .ticket-step {
            display:grid;
            grid-template-columns:38px minmax(0, 1fr);
            gap:12px;
            align-items:start;
        }

        .ticket-dot {
            display:grid;
            place-items:center;
            width:38px;
            height:38px;
            border-radius:999px;
            color:var(--primary);
            background:var(--primary-soft);
            border:1px solid var(--line);
        }

        .ticket-step strong { display:block; margin-bottom:4px; }
        .ticket-step span { color:var(--muted); line-height:1.55; font-size:.9rem; }

        .ticket-form {
            display:grid;
            gap:14px;
        }

        .ticket-form label {
            display:grid;
            gap:8px;
            font-weight:800;
        }

        .ticket-form select,
        .ticket-form textarea {
            width:100%;
            padding:14px 16px;
            border-radius:14px;
            border:1px solid var(--line);
            background:rgba(255,255,255,.88);
            color:var(--text);
            font:inherit;
        }

        @media (max-width:1100px) {
            .ticket-shell { grid-template-columns:1fr; }
        }
    </style>

    <section class="card hero">
        <span class="badge is-muted">{{ $chamado->protocolo }}</span>
        <h1 style="margin-top:14px;">{{ $chamado->assunto }}</h1>
        <p>{{ $chamado->nome }} abriu este chamado em {{ $chamado->created_at->format('d/m/Y H:i') }}. Use esta tela para entender contexto, registrar decisão e avançar a fila.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <a class="button-secondary" href="{{ route('super-admin.suporte.index') }}">Voltar para cards</a>
            @if ($proximoChamado)
                <a class="button" href="{{ route('super-admin.suporte.show', $proximoChamado) }}">Próximo aberto</a>
            @endif
            @if ($chamado->conta)
                <a class="button-secondary" href="{{ route('super-admin.contas.show', $chamado->conta) }}">Ver conta vinculada</a>
            @endif
        </div>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ $chamado->statusLabel() }}</strong><span>status atual</span></article>
        <article class="metric"><strong>{{ $chamado->prioridadeLabel() }}</strong><span>prioridade</span></article>
        <article class="metric"><strong>{{ $chamado->categoriaLabel() }}</strong><span>categoria</span></article>
    </section>

    <section class="ticket-shell">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Mensagem do cliente</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Leia o contexto completo antes de ajustar status ou prioridade.</p>
                    </div>
                </div>

                <div class="ticket-message" style="margin-top:18px;">{{ $chamado->mensagem }}</div>

                <div class="grid-2" style="margin-top:18px;">
                    <div class="mini-card">
                        <strong>{{ $chamado->nome }}</strong>
                        <span>{{ $chamado->email }}</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $chamado->empresa ?: 'Empresa não informada' }}</strong>
                        <span>{{ $chamado->telefone ?: 'Telefone não informado' }}</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $chamado->conta?->nome_fantasia ?? 'Sem vínculo' }}</strong>
                        <span>conta relacionada</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $chamado->origem_url ?: 'Origem não informada' }}</strong>
                        <span>origem do chamado</span>
                    </div>
                </div>
            </div>
        </article>

        <aside class="card">
            <div class="card-body">
                <h2 style="margin:0;">Atualizar chamado</h2>
                <p style="margin:8px 0 18px; color:var(--muted); line-height:1.7;">Registre a leitura interna para manter histórico operacional claro.</p>

                <form class="ticket-form" method="POST" action="{{ route('super-admin.suporte.update', $chamado) }}">
                    @csrf
                    @method('PATCH')

                    <label>
                        <span>Status</span>
                        <select name="status">
                            @foreach ($statusDisponiveis as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('status', $chamado->status) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Prioridade</span>
                        <select name="prioridade">
                            @foreach ($prioridadesDisponiveis as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('prioridade', $chamado->prioridade) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Observação interna</span>
                        <textarea name="observacao_interna" rows="7">{{ old('observacao_interna', $chamado->observacao_interna) }}</textarea>
                    </label>

                    <button class="button" type="submit">Salvar atendimento</button>
                </form>
            </div>
        </aside>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Linha do chamado</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Resumo rápido do ciclo de atendimento.</p>
                </div>
            </div>

            <div class="ticket-timeline" style="margin-top:18px;">
                <div class="ticket-step">
                    <span class="ticket-dot"><x-ui.icon name="bell" /></span>
                    <div>
                        <strong>Chamado aberto</strong>
                        <span>{{ $chamado->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="ticket-step">
                    <span class="ticket-dot"><x-ui.icon name="user" /></span>
                    <div>
                        <strong>Solicitante</strong>
                        <span>{{ $chamado->nome }} | {{ $chamado->email }}</span>
                    </div>
                </div>

                <div class="ticket-step">
                    <span class="ticket-dot"><x-ui.icon name="check" /></span>
                    <div>
                        <strong>Respondido</strong>
                        <span>{{ $chamado->respondido_em?->format('d/m/Y H:i') ?? 'Ainda sem resposta registrada' }}</span>
                    </div>
                </div>

                <div class="ticket-step">
                    <span class="ticket-dot"><x-ui.icon name="shield" /></span>
                    <div>
                        <strong>Resolvido</strong>
                        <span>{{ $chamado->resolvido_em?->format('d/m/Y H:i') ?? 'Ainda em andamento' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
