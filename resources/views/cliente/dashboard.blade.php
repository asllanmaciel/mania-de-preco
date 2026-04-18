@extends('layouts.backoffice')

@section('title', 'Area do cliente')
@section('brand_route', route('cliente.dashboard'))
@section('brand_label', 'Mania de Preco | Cliente')

@section('nav')
    <a class="chip" href="{{ route('cliente.dashboard') }}">Minha area</a>
    @if (auth()->user()->possuiAcessoAdmin())
        <a class="chip" href="{{ route('admin.dashboard') }}">Painel lojista</a>
    @endif
    @if (auth()->user()->ehSuperAdmin())
        <a class="chip" href="{{ route('super-admin.dashboard') }}">Super admin</a>
    @endif
@endsection

@section('content')
    <section class="card hero">
        <h1>Area do cliente</h1>
        <p>Base inicial da jornada do consumidor logado para acompanhar alertas, preferencias e relacao com as lojas sem misturar essa experiencia com a operacao do lojista.</p>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($alertas->count(), 0, ',', '.') }}</strong><span>alertas recentes</span></article>
        <article class="metric"><strong>{{ number_format($alertas->where('status', 'atendido')->count(), 0, ',', '.') }}</strong><span>alertas atendidos</span></article>
        <article class="metric"><strong>{{ number_format($avaliacoes->count(), 0, ',', '.') }}</strong><span>avaliacoes registradas</span></article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Meus alertas</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Monitoramento pessoal para acompanhar quando o mercado bate o preco desejado.</p>
                    </div>
                </div>

                <div class="list">
                    @forelse ($alertas as $alerta)
                        <div class="list-row">
                            <strong>{{ $alerta->produto?->nome ?? 'Produto' }}</strong>
                            <small>
                                alvo R$ {{ number_format((float) $alerta->preco_desejado, 2, ',', '.') }}
                                | atual R$ {{ number_format((float) ($alerta->ultimo_preco_menor ?? 0), 2, ',', '.') }}
                                | {{ $alerta->status }}
                            </small>
                        </div>
                    @empty
                        <div class="mini-card">
                            <strong>Sem alertas por enquanto</strong>
                            <span>Essa area ja esta pronta para receber a camada completa de acompanhamento do cliente.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Minhas avaliacoes</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Historico inicial de interacao do cliente com as lojas da plataforma.</p>
                    </div>
                </div>

                <div class="list">
                    @forelse ($avaliacoes as $avaliacao)
                        <div class="list-row">
                            <strong>{{ $avaliacao->loja?->nome ?? 'Loja' }}</strong>
                            <small>nota {{ number_format((float) $avaliacao->nota, 1, ',', '.') }} | {{ $avaliacao->comentario ?: 'Sem comentario adicional.' }}</small>
                        </div>
                    @empty
                        <div class="mini-card">
                            <strong>Nenhuma avaliacao registrada</strong>
                            <span>Quando a jornada do cliente ganhar mais profundidade, esta area passa a refletir favoritos, avaliacoes e recomendacoes.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>
    </section>
@endsection
