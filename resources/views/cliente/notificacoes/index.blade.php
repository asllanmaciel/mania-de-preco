@extends('layouts.backoffice')

@section('title', 'Notificacoes')
@section('brand_route', route('cliente.dashboard'))
@section('brand_label', 'Mania de Preco | Cliente')

@section('nav')
    <a class="chip" href="{{ route('cliente.dashboard') }}">Minha area</a>
    <a class="chip" href="{{ route('cliente.notificacoes') }}">Notificacoes</a>
    <a class="chip" href="{{ route('home') }}">Ver ofertas</a>
    @if (auth()->user()->possuiAcessoAdmin())
        <a class="chip" href="{{ route('admin.dashboard') }}">Painel lojista</a>
    @endif
@endsection

@section('content')
    <section class="card hero">
        <h1>Alertas que merecem sua atencao</h1>
        <p>A central acompanha seus alertas de preco e transforma oportunidades em acoes simples para voce decidir mais rapido.</p>
    </section>

    <section class="grid-3">
        <article class="metric">
            <div class="metric-head">
                <span>Total</span>
                <span class="metric-icon"><x-ui.icon name="bell" /></span>
            </div>
            <strong>{{ number_format($notificacoes->count(), 0, ',', '.') }}</strong>
            <span>notificacoes ativas</span>
        </article>
        <article class="metric">
            <div class="metric-head">
                <span>Precos na meta</span>
                <span class="metric-icon is-teal"><x-ui.icon name="check" /></span>
            </div>
            <strong>{{ number_format($notificacoes->where('tipo', 'sucesso')->count(), 0, ',', '.') }}</strong>
            <span>oportunidades prontas para conferir</span>
        </article>
        <article class="metric">
            <div class="metric-head">
                <span>Monitoramento</span>
                <span class="metric-icon is-warning"><x-ui.icon name="search" /></span>
            </div>
            <strong>{{ number_format($notificacoes->whereIn('tipo', ['alerta', 'info'])->count(), 0, ',', '.') }}</strong>
            <span>itens acompanhando o mercado</span>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Minha fila</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Esses sinais sao atualizados a partir dos seus alertas e produtos monitorados.</p>
                </div>
            </div>

            <div class="list">
                @foreach ($notificacoes as $notificacao)
                    <div class="list-row">
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <span class="metric-icon {{ $notificacao['tipo'] === 'sucesso' ? 'is-teal' : ($notificacao['tipo'] === 'alerta' ? 'is-warning' : '') }}">
                                <x-ui.icon :name="$notificacao['icone']" />
                            </span>
                            <div>
                                <strong>{{ $notificacao['titulo'] }}</strong>
                                <small>
                                    {{ $notificacao['descricao'] }}
                                    @if ($notificacao['lida'])
                                        | vista {{ $notificacao['lida_em']?->diffForHumans() }}
                                    @endif
                                    @if ($notificacao['dispensada'])
                                        | dispensada ate {{ $notificacao['dispensada_ate']?->format('d/m H:i') }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                            @if ($notificacao['rota'])
                                <a class="button-secondary" href="{{ $notificacao['rota'] }}">{{ $notificacao['acao'] }}</a>
                            @endif

                            @if (! $notificacao['lida'])
                                <form method="POST" action="{{ route('cliente.notificacoes.interagir') }}" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="chave" value="{{ $notificacao['chave'] }}">
                                    <input type="hidden" name="acao" value="ler">
                                    <button class="button-secondary" type="submit">Marcar vista</button>
                                </form>
                            @endif

                            @if (! $notificacao['dispensada'] && $notificacao['tipo'] !== 'sucesso')
                                <form method="POST" action="{{ route('cliente.notificacoes.interagir') }}" style="margin:0;">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="chave" value="{{ $notificacao['chave'] }}">
                                    <input type="hidden" name="acao" value="dispensar">
                                    <button class="button-secondary" type="submit">Dispensar 24h</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
