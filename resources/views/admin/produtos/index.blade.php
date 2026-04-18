@extends('layouts.admin')

@section('title', 'Produtos')
@section('heading', 'Catalogo de produtos')
@section('subheading', 'Organize a base de produtos que sera usada nas lojas e no comparador publico de precos.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Base do catalogo</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Produtos sao compartilhados pela plataforma e conectam categorias, marcas e precos das lojas.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.produtos.create') }}">Novo produto</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($produtos->total(), 0, ',', '.') }}</strong>
                    <span>produtos no catalogo</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($produtos->getCollection()->where('status', 'ativo')->count(), 0, ',', '.') }}</strong>
                    <span>produtos ativos nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($produtos->getCollection()->sum('precos_count'), 0, ',', '.') }}</strong>
                    <span>precos vinculados aos produtos listados</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.produtos.index') }}">
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome, slug, categoria ou marca">

                <select name="status">
                    <option value="">Todos os status</option>
                    <option value="ativo" @selected($statusSelecionado === 'ativo')>Ativo</option>
                    <option value="inativo" @selected($statusSelecionado === 'inativo')>Inativo</option>
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>

                @if ($busca !== '' || $statusSelecionado !== '')
                    <a class="button-secondary" href="{{ route('admin.produtos.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($produtos->isEmpty())
                <div class="empty-state">
                    Ainda nao existem produtos no catalogo. Cadastre o primeiro item para começar a ligar lojas e precos dentro do SaaS.
                </div>
            @else
                <div class="table-head">
                    <span>Produto</span>
                    <span>Categoria / marca</span>
                    <span>Status / precos</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($produtos as $produto)
                        <article class="list-row">
                            <div>
                                <strong>{{ $produto->nome }}</strong>
                                <small>{{ $produto->descricao ? \Illuminate\Support\Str::limit($produto->descricao, 90) : 'sem descricao' }}</small><br>
                                <code>{{ $produto->slug }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ $produto->categoria?->nome ?? 'Sem categoria' }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $produto->marca?->nome ?? 'Sem marca' }}</small>
                            </div>

                            <div>
                                <span class="badge {{ $produto->status === 'inativo' ? 'is-warning' : '' }}">{{ $produto->status }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $produto->precos_count }} precos vinculados</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.produtos.edit', $produto) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.produtos.destroy', $produto) }}" onsubmit="return confirm('Deseja remover este produto do catalogo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $produtos->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
