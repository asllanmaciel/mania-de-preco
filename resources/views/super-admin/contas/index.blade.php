@extends('layouts.backoffice')

@section('title', 'Contas da plataforma')
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('cliente.dashboard') }}">Area do cliente</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>Gestao de contas</h1>
        <p>Leitura centralizada das operacoes ativas da plataforma, com foco em assinatura, usuarios vinculados, lojas, intensidade de uso e visibilidade geral do ecossistema.</p>
    </section>

    <section class="grid-4">
        <article class="metric"><strong>{{ number_format((int) ($statusResumo['ativo'] ?? 0), 0, ',', '.') }}</strong><span>contas ativas</span></article>
        <article class="metric"><strong>{{ number_format((int) ($statusResumo['trial'] ?? 0), 0, ',', '.') }}</strong><span>contas em trial</span></article>
        <article class="metric"><strong>{{ number_format((int) ($statusResumo['inadimplente'] ?? 0), 0, ',', '.') }}</strong><span>contas inadimplentes</span></article>
        <article class="metric"><strong>{{ number_format($contas->total(), 0, ',', '.') }}</strong><span>contas no recorte atual</span></article>
    </section>

    <section class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.contas.index') }}" class="toolbar">
                <div class="nav" style="flex:1 1 320px;">
                    <input
                        type="text"
                        name="busca"
                        value="{{ $busca }}"
                        placeholder="Buscar por conta, slug ou e-mail"
                        style="width:100%; padding:14px 16px; border-radius:14px; border:1px solid rgba(76,42,22,.12); background:rgba(255,255,255,.92);"
                    >
                </div>
                <div class="nav">
                    <select name="status" style="padding:14px 16px; border-radius:14px; border:1px solid rgba(76,42,22,.12); background:rgba(255,255,255,.92);">
                        <option value="">Todos os status</option>
                        @foreach (['trial' => 'trial', 'ativo' => 'ativo', 'inadimplente' => 'inadimplente', 'cancelado' => 'cancelado'] as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected($statusSelecionado === $valor)>{{ ucfirst($rotulo) }}</option>
                        @endforeach
                    </select>
                    <button class="button" type="submit">Aplicar</button>
                    @if ($busca !== '' || $statusSelecionado !== '')
                        <a class="button-secondary" href="{{ route('super-admin.contas.index') }}">Limpar</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="grid-2">
        @foreach ($contas as $conta)
            @php $assinatura = $conta->assinaturas->first(); @endphp
            <article class="card">
                <div class="card-body" style="display:grid; gap:16px;">
                    <div class="section-head">
                        <div>
                            <h2 style="margin:0;">{{ $conta->nome_fantasia }}</h2>
                            <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">{{ $conta->email ?: 'Sem e-mail' }} | slug {{ $conta->slug }}</p>
                        </div>
                        <span class="chip">{{ $conta->status }}</span>
                    </div>

                    <div class="grid-3">
                        <div class="mini-card">
                            <strong>{{ number_format($conta->usuarios_count, 0, ',', '.') }}</strong>
                            <span>usuarios vinculados</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ number_format($conta->lojas_count, 0, ',', '.') }}</strong>
                            <span>lojas operando</span>
                        </div>
                        <div class="mini-card">
                            <strong>{{ number_format($conta->movimentacoes_financeiras_count, 0, ',', '.') }}</strong>
                            <span>movimentacoes registradas</span>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="mini-card">
                            <strong>Assinatura</strong>
                            <span>{{ $assinatura?->plano?->nome ?? 'Sem plano' }} | {{ $assinatura?->status ?? 'sem assinatura' }}</span>
                        </div>
                        <div class="mini-card">
                            <strong>Trial</strong>
                            <span>{{ $conta->trial_ends_at?->format('d/m/Y H:i') ?? 'Nao aplicavel' }}</span>
                        </div>
                    </div>

                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <a class="button-secondary" href="{{ route('super-admin.contas.show', $conta) }}">Abrir detalhe da conta</a>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <div class="card">
        <div class="card-body">
            {{ $contas->links() }}
        </div>
    </div>
@endsection
