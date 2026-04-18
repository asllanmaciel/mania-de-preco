@extends('layouts.admin')

@section('title', 'Contas a pagar')
@section('heading', 'Contas a pagar')
@section('subheading', 'Gerencie os compromissos financeiros da conta e acompanhe vencimentos, pagamentos e titulos em aberto.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Saidas programadas</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Use essa area para organizar compromissos com fornecedores e outros custos da operacao.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.financeiro.contas-pagar.create') }}">Nova conta a pagar</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($titulos->total(), 0, ',', '.') }}</strong>
                    <span>titulos cadastrados</span>
                </article>
                <article class="stat-card-soft">
                    <strong>R$ {{ number_format($titulos->getCollection()->sum('valor_total'), 2, ',', '.') }}</strong>
                    <span>valor total exibido</span>
                </article>
                <article class="stat-card-soft">
                    <strong>R$ {{ number_format($titulos->getCollection()->sum('valor_pago'), 2, ',', '.') }}</strong>
                    <span>valor pago exibido</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.financeiro.contas-pagar.index') }}">
                <select name="status">
                    <option value="">Todos os status</option>
                    @foreach (['aberta' => 'Aberta', 'parcial' => 'Parcial', 'paga' => 'Paga', 'vencida' => 'Vencida', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($statusSelecionado === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($statusSelecionado !== '')
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas-pagar.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($titulos->isEmpty())
                <div class="empty-state">
                    Nenhuma conta a pagar cadastrada ainda. Quando voce registrar os primeiros compromissos, essa area passa a refletir o cronograma financeiro da conta.
                </div>
            @else
                <div class="table-head">
                    <span>Titulo</span>
                    <span>Fornecedor / categoria</span>
                    <span>Valor / status</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($titulos as $item)
                        <article class="list-row">
                            <div>
                                <strong>{{ $item->descricao }}</strong>
                                <small>{{ $item->loja?->nome ?? 'Sem loja vinculada' }}</small><br>
                                <code>Vence em {{ $item->vencimento?->format('d/m/Y') ?? 'Sem data' }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ $item->fornecedor_nome ?: 'Fornecedor nao informado' }}</span>
                                <small style="display: block; margin-top: 8px;">
                                    {{ $item->categoriaFinanceira?->nome ?? 'Sem categoria' }}
                                    @if ($item->contaFinanceira)
                                        · {{ $item->contaFinanceira->nome }}
                                    @endif
                                </small>
                            </div>

                            <div>
                                <span class="badge is-warning">R$ {{ number_format((float) $item->valor_total, 2, ',', '.') }}</span>
                                <small style="display: block; margin-top: 8px;">
                                    {{ $item->status }} · pago R$ {{ number_format((float) $item->valor_pago, 2, ',', '.') }}
                                    @if ($item->movimentacao_financeira_id)
                                        · baixa automatica ativa
                                    @endif
                                </small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.financeiro.contas-pagar.edit', $item) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.financeiro.contas-pagar.destroy', $item) }}" onsubmit="return confirm('Deseja remover esta conta a pagar?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $titulos->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
