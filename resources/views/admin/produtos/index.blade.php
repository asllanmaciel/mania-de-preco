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
            <div class="section-header">
                <div>
                    <span class="pill">Prontidao da vitrine</span>
                    <h2>O que falta para o catalogo vender melhor</h2>
                    <p>Corrija lacunas que reduzem confianca na busca publica e deixam o comparador menos convincente.</p>
                </div>
                <a class="button-secondary" href="{{ route('admin.precos.index') }}">Revisar precos</a>
            </div>

            <div class="highlight-grid">
                <article class="highlight-card">
                    <strong>{{ number_format($produtosAtivosTotal, 0, ',', '.') }}</strong>
                    <span>produtos ativos prontos para aparecer nas vitrines e comparativos.</span>
                </article>
                <article class="highlight-card">
                    <strong>{{ number_format($produtosSemPreco, 0, ',', '.') }}</strong>
                    <span>produtos ativos sem preco. Eles existem no catalogo, mas ainda nao disputam no comparador.</span>
                </article>
                <article class="highlight-card">
                    <strong>{{ number_format($produtosSemImagem, 0, ',', '.') }}</strong>
                    <span>produtos sem imagem principal. Priorize fotos para aumentar leitura e confianca.</span>
                </article>
            </div>
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
                            <div style="display:flex; gap:14px; align-items:center;">
                                <img
                                    src="{{ $produto->imagem_url }}"
                                    alt="{{ $produto->nome }}"
                                    style="width:68px; height:68px; object-fit:cover; border-radius:18px; border:1px solid rgba(15, 23, 42, 0.08); background:#fff; flex-shrink:0;"
                                >
                                <div>
                                    <strong>{{ $produto->nome }}</strong>
                                    <small>{{ $produto->descricao ? \Illuminate\Support\Str::limit($produto->descricao, 90) : 'sem descricao' }}</small><br>
                                    <code>{{ $produto->slug }}</code>
                                </div>
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
