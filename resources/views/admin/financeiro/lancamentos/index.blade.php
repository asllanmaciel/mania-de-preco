@extends('layouts.admin')

@section('title', 'Lancamentos')
@section('heading', 'Lancamentos financeiros')
@section('subheading', 'Cadastre receitas, despesas e ajustes diretamente no painel para construir o historico financeiro da conta.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Historico operacional</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Os lancamentos alimentam o saldo projetado e a leitura de caixa do modulo financeiro.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.financeiro.lancamentos.create') }}">Novo lancamento</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($movimentacoes->total(), 0, ',', '.') }}</strong>
                    <span>lancamentos financeiros cadastrados</span>
                </article>
                <article class="stat-card-soft">
                    <strong>R$ {{ number_format($movimentacoes->getCollection()->where('tipo', 'receita')->sum('valor'), 2, ',', '.') }}</strong>
                    <span>receitas exibidas nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>R$ {{ number_format($movimentacoes->getCollection()->where('tipo', 'despesa')->sum('valor'), 2, ',', '.') }}</strong>
                    <span>despesas exibidas nesta pagina</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.financeiro.lancamentos.index') }}">
                <select name="tipo">
                    <option value="">Todos os tipos</option>
                    <option value="receita" @selected($tipoSelecionado === 'receita')>Receita</option>
                    <option value="despesa" @selected($tipoSelecionado === 'despesa')>Despesa</option>
                    <option value="transferencia" @selected($tipoSelecionado === 'transferencia')>Transferencia</option>
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($tipoSelecionado !== '')
                    <a class="button-secondary" href="{{ route('admin.financeiro.lancamentos.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($movimentacoes->isEmpty())
                <div class="empty-state">
                    Nenhum lancamento registrado ainda. Assim que voce criar o primeiro, o centro financeiro passa a refletir esse historico automaticamente.
                </div>
            @else
                <div class="table-head">
                    <span>Lancamento</span>
                    <span>Conta / categoria</span>
                    <span>Valor / status</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($movimentacoes as $item)
                        <article class="list-row">
                            <div>
                                <strong>{{ $item->descricao }}</strong>
                                <small>{{ $item->loja?->nome ?? 'Sem loja vinculada' }}</small><br>
                                <code>{{ $item->data_movimentacao?->format('d/m/Y H:i') ?? 'Sem data' }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ $item->contaFinanceira?->nome ?? 'Sem conta' }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $item->categoriaFinanceira?->nome ?? 'Sem categoria' }}</small>
                            </div>

                            <div>
                                <span class="badge {{ $item->tipo === 'despesa' ? 'is-warning' : '' }}">R$ {{ number_format((float) $item->valor, 2, ',', '.') }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $item->tipo }} · {{ $item->status }}</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.financeiro.lancamentos.edit', $item) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.financeiro.lancamentos.destroy', $item) }}" onsubmit="return confirm('Deseja remover este lancamento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $movimentacoes->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
