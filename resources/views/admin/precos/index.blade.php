@extends('layouts.admin')

@section('title', 'Precos')
@section('heading', 'Tabela de precos')
@section('subheading', 'Gerencie os valores praticados pelas lojas da conta e alimente o comparador publico do produto.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Comparador operado pelo admin</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Os precos aqui refletem a estrategia comercial das lojas da conta e conectam produto com canal de venda.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.precos.create') }}">Novo preco</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($precos->total(), 0, ',', '.') }}</strong>
                    <span>precos cadastrados nas lojas da conta</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($lojasDaConta->count(), 0, ',', '.') }}</strong>
                    <span>lojas disponiveis para precificacao</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($precos->getCollection()->sum('preco'), 2, ',', '.') }}</strong>
                    <span>soma dos valores exibidos nesta pagina</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.precos.index') }}">
                <select name="loja_id">
                    <option value="">Todas as lojas</option>
                    @foreach ($lojasDaConta as $loja)
                        <option value="{{ $loja->id }}" @selected((string) $lojaIdSelecionada === (string) $loja->id)>{{ $loja->nome }}</option>
                    @endforeach
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>

                @if ($lojaIdSelecionada)
                    <a class="button-secondary" href="{{ route('admin.precos.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($precos->isEmpty())
                <div class="empty-state">
                    Ainda nao existem precos cadastrados para as lojas da conta. Assim que voce relacionar um produto a uma loja, essa area passa a sustentar o comparador publico.
                </div>
            @else
                <div class="table-head">
                    <span>Produto</span>
                    <span>Loja</span>
                    <span>Valor / tipo</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($precos as $preco)
                        <article class="list-row">
                            <div>
                                <strong>{{ $preco->produto->nome }}</strong>
                                <small>{{ $preco->produto->categoria?->nome ?? 'Sem categoria' }}</small><br>
                                <code>{{ $preco->url_produto ?: 'sem URL cadastrada' }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ $preco->loja->nome }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $preco->loja->cidade ?: 'Cidade nao informada' }}</small>
                            </div>

                            <div>
                                <span class="badge">R$ {{ number_format((float) $preco->preco, 2, ',', '.') }}</span>
                                <small style="display: block; margin-top: 8px;">{{ ucfirst($preco->tipo_preco) }}</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.precos.edit', $preco) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.precos.destroy', $preco) }}" onsubmit="return confirm('Deseja remover este preco do comparador?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $precos->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
