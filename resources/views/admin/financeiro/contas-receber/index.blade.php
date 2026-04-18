@extends('layouts.admin')

@section('title', 'Contas a receber')
@section('heading', 'Contas a receber')
@section('subheading', 'Gerencie entradas previstas da conta com cliente, vencimento e controle de recebimento.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Entradas programadas</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Use essa area para acompanhar vendas a prazo, cobrancas e outras entradas previstas para a conta.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.financeiro.contas-receber.create') }}">Nova conta a receber</a>
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
                    <strong>R$ {{ number_format($titulos->getCollection()->sum('valor_recebido'), 2, ',', '.') }}</strong>
                    <span>valor recebido exibido</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.financeiro.contas-receber.index') }}">
                <select name="status">
                    <option value="">Todos os status</option>
                    @foreach (['aberta' => 'Aberta', 'parcial' => 'Parcial', 'recebida' => 'Recebida', 'vencida' => 'Vencida', 'cancelada' => 'Cancelada'] as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($statusSelecionado === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>

                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($statusSelecionado !== '')
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas-receber.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($titulos->isEmpty())
                <div class="empty-state">
                    Nenhuma conta a receber cadastrada ainda. Quando voce registrar os primeiros titulos, essa area passa a refletir a previsao de entradas da conta.
                </div>
            @else
                <div class="table-head">
                    <span>Titulo</span>
                    <span>Cliente / categoria</span>
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
                                <span class="pill">{{ $item->cliente_nome ?: 'Cliente nao informado' }}</span>
                                <small style="display: block; margin-top: 8px;">
                                    {{ $item->categoriaFinanceira?->nome ?? 'Sem categoria' }}
                                    @if ($item->contaFinanceira)
                                        · {{ $item->contaFinanceira->nome }}
                                    @endif
                                </small>
                            </div>

                            <div>
                                <span class="badge">R$ {{ number_format((float) $item->valor_total, 2, ',', '.') }}</span>
                                <small style="display: block; margin-top: 8px;">
                                    {{ $item->status }} · recebido R$ {{ number_format((float) $item->valor_recebido, 2, ',', '.') }}
                                    @if ($item->movimentacao_financeira_id)
                                        · baixa automatica ativa
                                    @endif
                                </small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.financeiro.contas-receber.edit', $item) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.financeiro.contas-receber.destroy', $item) }}" onsubmit="return confirm('Deseja remover esta conta a receber?');">
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
