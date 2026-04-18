@extends('layouts.admin')

@section('title', 'Categorias financeiras')
@section('heading', 'Categorias financeiras')
@section('subheading', 'Padronize entradas e saidas para ganhar leitura executiva, consistencia operacional e analise mais confiavel.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Governanca de classificacao</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Categorias bem definidas deixam o dashboard mais inteligente e aceleram o lancamento de receitas, despesas e titulos.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.financeiro.categorias.create') }}">Nova categoria</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($categorias->total(), 0, ',', '.') }}</strong>
                    <span>categorias financeiras cadastradas</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($categorias->getCollection()->where('ativa', true)->count(), 0, ',', '.') }}</strong>
                    <span>categorias ativas nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($categorias->getCollection()->sum(fn ($item) => $item->movimentacoes_count + $item->contas_pagar_count + $item->contas_receber_count), 0, ',', '.') }}</strong>
                    <span>vinculos operacionais exibidos</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.financeiro.categorias.index') }}">
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome, slug ou descricao">

                <select name="tipo">
                    <option value="">Todos os tipos</option>
                    <option value="receita" @selected($tipoSelecionado === 'receita')>Receita</option>
                    <option value="despesa" @selected($tipoSelecionado === 'despesa')>Despesa</option>
                    <option value="ambos" @selected($tipoSelecionado === 'ambos')>Ambos</option>
                </select>

                <select name="status">
                    <option value="">Todos os status</option>
                    <option value="ativas" @selected($statusSelecionado === 'ativas')>Ativas</option>
                    <option value="inativas" @selected($statusSelecionado === 'inativas')>Inativas</option>
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($busca !== '' || $tipoSelecionado !== '' || $statusSelecionado !== '')
                    <a class="button-secondary" href="{{ route('admin.financeiro.categorias.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($categorias->isEmpty())
                <div class="empty-state">
                    Nenhuma categoria financeira cadastrada ainda. Esse modulo ajuda a elevar o nivel analitico do financeiro e reduzir lancamentos despadronizados.
                </div>
            @else
                <div class="table-head">
                    <span>Categoria</span>
                    <span>Tipo / contexto</span>
                    <span>Status / uso</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($categorias as $item)
                        @php
                            $vinculos = $item->movimentacoes_count + $item->contas_pagar_count + $item->contas_receber_count;
                            $corCategoria = $item->cor ?: '#0f766e';
                        @endphp

                        <article class="list-row">
                            <div>
                                <strong>{{ $item->nome }}</strong>
                                <small>{{ $item->descricao ?: 'Sem descricao operacional cadastrada' }}</small><br>
                                <code>{{ $item->slug }}</code>
                            </div>

                            <div>
                                <span class="pill" style="background: {{ $corCategoria }}1a; color: {{ $corCategoria }}; border-color: {{ $corCategoria }}33;">
                                    {{ ucfirst($item->tipo) }}
                                </span>
                                <small style="display: block; margin-top: 8px;">{{ $item->icone ?: 'sem icone' }}</small>
                            </div>

                            <div>
                                <span class="badge {{ ! $item->ativa ? 'is-warning' : '' }}">{{ $item->ativa ? 'ativa' : 'inativa' }}</span>
                                <small style="display: block; margin-top: 8px;">{{ number_format($vinculos, 0, ',', '.') }} vinculos operacionais</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.financeiro.categorias.edit', $item) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.financeiro.categorias.destroy', $item) }}" onsubmit="return confirm('Deseja remover esta categoria financeira?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit" @disabled($vinculos > 0)>Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $categorias->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
