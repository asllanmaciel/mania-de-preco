@extends('layouts.admin')

@section('title', 'Contas financeiras')
@section('heading', 'Contas financeiras')
@section('subheading', 'Organize caixa, bancos e carteiras da conta ativa para sustentar os lancamentos do financeiro.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Estrutura de contas</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Cada conta financeira pode representar caixa, banco, cartao ou carteira digital vinculada ao negocio.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.financeiro.contas.create') }}">Nova conta financeira</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>{{ number_format($contasFinanceiras->total(), 0, ',', '.') }}</strong>
                    <span>contas financeiras cadastradas</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($contasFinanceiras->getCollection()->where('ativa', true)->count(), 0, ',', '.') }}</strong>
                    <span>contas ativas nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>R$ {{ number_format($contasFinanceiras->getCollection()->sum('saldo_atual'), 2, ',', '.') }}</strong>
                    <span>soma do saldo atual exibido</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.financeiro.contas.index') }}">
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome, tipo ou instituicao">
                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($busca !== '')
                    <a class="button-secondary" href="{{ route('admin.financeiro.contas.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($contasFinanceiras->isEmpty())
                <div class="empty-state">
                    Nenhuma conta financeira cadastrada ainda. Esse e o primeiro passo operacional do modulo financeiro, porque os lancamentos dependem dessa base.
                </div>
            @else
                <div class="table-head">
                    <span>Conta</span>
                    <span>Tipo / loja</span>
                    <span>Saldo / status</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($contasFinanceiras as $item)
                        <article class="list-row">
                            <div>
                                <strong>{{ $item->nome }}</strong>
                                <small>{{ $item->instituicao ?: 'sem instituicao informada' }}</small><br>
                                <code>{{ $item->agencia ?: 'sem agencia' }} · {{ $item->numero ?: 'sem numero' }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ ucfirst(str_replace('_', ' ', $item->tipo)) }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $item->loja?->nome ?? 'Sem loja vinculada' }}</small>
                            </div>

                            <div>
                                <span class="badge {{ ! $item->ativa ? 'is-warning' : '' }}">R$ {{ number_format((float) $item->saldo_atual, 2, ',', '.') }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $item->ativa ? 'ativa' : 'inativa' }}</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.financeiro.contas.edit', $item) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.financeiro.contas.destroy', $item) }}" onsubmit="return confirm('Deseja remover esta conta financeira?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $contasFinanceiras->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
