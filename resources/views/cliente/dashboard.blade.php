@extends('layouts.backoffice')

@section('title', 'Area do cliente')
@section('brand_route', route('cliente.dashboard'))
@section('brand_label', 'Mania de Preco | Cliente')

@section('nav')
    <a class="chip" href="{{ route('cliente.dashboard') }}">Minha area</a>
    <a class="chip" href="{{ route('home') }}">Ver ofertas</a>
    @if (auth()->user()->possuiAcessoAdmin())
        <a class="chip" href="{{ route('admin.dashboard') }}">Painel lojista</a>
    @endif
    @if (auth()->user()->ehSuperAdmin())
        <a class="chip" href="{{ route('super-admin.dashboard') }}">Super admin</a>
    @endif
@endsection

@section('content')
    <section class="card hero">
        <h1>Seu radar pessoal de bons precos</h1>
        <p>Escolha produtos importantes para voce, defina o valor ideal e acompanhe quando as lojas entram em uma faixa que vale a compra.</p>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($totalAlertas, 0, ',', '.') }}</strong><span>alertas criados</span></article>
        <article class="metric"><strong>{{ number_format($alertasAtivos, 0, ',', '.') }}</strong><span>monitorando agora</span></article>
        <article class="metric"><strong>{{ number_format($alertasAtendidos, 0, ',', '.') }}</strong><span>precos que ja bateram a meta</span></article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Criar alerta</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Defina um produto e o preco alvo. O sistema avalia a melhor oferta atual automaticamente.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('cliente.alertas.store') }}">
                    @csrf

                    <label>
                        <span>Produto</span>
                        <select name="produto_id" required>
                            <option value="">Selecione um produto</option>
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}" @selected((int) old('produto_id') === $produto->id)>
                                    {{ $produto->nome }}
                                    @if ($produto->menor_preco)
                                        - a partir de R$ {{ number_format((float) $produto->menor_preco, 2, ',', '.') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span>Preco desejado</span>
                        <input type="number" name="preco_desejado" value="{{ old('preco_desejado') }}" min="0.01" step="0.01" placeholder="Ex: 18,50" required>
                    </label>

                    <button class="button" type="submit">Ativar alerta</button>
                </form>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Como usar bem</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Um alerta bom precisa ter meta realista e produto recorrente. Assim ele vira economia de verdade.</p>
                    </div>
                </div>

                <div class="list">
                    <div class="mini-card">
                        <strong>Use como lista inteligente</strong>
                        <span>Monitore itens de compra recorrente, como mercado, pet, higiene e limpeza.</span>
                    </div>
                    <div class="mini-card">
                        <strong>Compare antes de comprar</strong>
                        <span>Quando um alerta for atendido, confira a loja de referencia e o tipo de preco antes de decidir.</span>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Meus alertas</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Atualize a meta, pause o monitoramento ou remova alertas que nao fazem mais sentido.</p>
                </div>
            </div>

            <div class="list">
                @forelse ($alertas as $alerta)
                    <div class="list-row">
                        <div>
                            <strong>{{ $alerta->produto?->nome ?? 'Produto' }}</strong>
                            <small>
                                alvo R$ {{ number_format((float) $alerta->preco_desejado, 2, ',', '.') }}
                                | melhor atual R$ {{ number_format((float) ($alerta->ultimo_preco_menor ?? 0), 2, ',', '.') }}
                                | {{ $alerta->lojaReferencia?->nome ?? 'sem loja de referencia' }}
                                | {{ $alerta->status }}
                            </small>
                        </div>

                        <div style="display:grid; gap:10px; min-width:min(100%, 360px);">
                            <form method="POST" action="{{ route('cliente.alertas.update', $alerta) }}" style="display:grid; grid-template-columns:1fr 1fr auto; gap:8px; margin:0;">
                                @csrf
                                @method('PATCH')

                                <input type="number" name="preco_desejado" value="{{ number_format((float) $alerta->preco_desejado, 2, '.', '') }}" min="0.01" step="0.01" required aria-label="Preco desejado">
                                <select name="status" aria-label="Status do alerta">
                                    <option value="ativo" @selected($alerta->status !== 'inativo')>monitorar</option>
                                    <option value="inativo" @selected($alerta->status === 'inativo')>pausar</option>
                                </select>
                                <button class="button-secondary" type="submit">Salvar</button>
                            </form>

                            <form method="POST" action="{{ route('cliente.alertas.destroy', $alerta) }}" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button class="logout-button" type="submit" style="width:100%;">Remover alerta</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="mini-card">
                        <strong>Nenhum alerta ativo ainda</strong>
                        <span>Crie seu primeiro alerta ou visite uma pagina de produto para salvar uma meta de preco direto da comparacao.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="card">
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
                        <span>Depois, essa area pode evoluir para favoritos, avaliacoes e recomendacoes personalizadas.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
