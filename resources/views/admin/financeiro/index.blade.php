@extends('layouts.admin')

@section('title', 'Financeiro')
@section('heading', 'Centro financeiro')
@section('subheading', 'Uma visao consolidada da saude financeira da conta, com saldos, categorias, movimentacoes e titulos em aberto.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Saldo projetado</span>
            <strong class="metric-value">R$ {{ number_format($saldoProjetado, 2, ',', '.') }}</strong>
            <span class="metric-trend {{ $saldoProjetado < 0 ? 'is-danger' : '' }}">
                {{ $saldoProjetado < 0 ? 'mais saidas do que entradas' : 'resultado consolidado da conta' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Receitas</span>
            <strong class="metric-value">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</strong>
            <span class="metric-trend">movimentacoes do tipo receita</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Despesas</span>
            <strong class="metric-value">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</strong>
            <span class="metric-trend is-danger">movimentacoes do tipo despesa</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Saldo em contas</span>
            <strong class="metric-value">R$ {{ number_format($somaContasFinanceiras, 2, ',', '.') }}</strong>
            <span class="metric-trend">soma do saldo atual das contas financeiras</span>
        </article>
    </section>

    <section class="grid-3">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Titulos pendentes</h2>
                        <p>Resumo do que ainda precisa sair ou entrar no caixa.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>R$ {{ number_format($totalPagarAberto, 2, ',', '.') }}</strong>
                        <span>total em contas a pagar abertas</span>
                    </div>
                    <div class="mini-card">
                        <strong>R$ {{ number_format($totalReceberAberto, 2, ',', '.') }}</strong>
                        <span>total em contas a receber abertas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->categoriasFinanceiras()->where('ativa', true)->count() }}</strong>
                        <span>categorias financeiras ativas</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Estrutura</h2>
                        <p>Base ja preparada para a proxima evolucao operacional.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $conta->contasFinanceiras()->count() }}</strong>
                        <span>contas financeiras cadastradas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->movimentacoesFinanceiras()->count() }}</strong>
                        <span>movimentacoes registradas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->contasPagar()->count() + $conta->contasReceber()->count() }}</strong>
                        <span>titulos totais ligados a conta</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Proxima camada</h2>
                        <p>Essa pagina corrige a navegacao e prepara o modulo financeiro para CRUDs completos.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <a class="button" href="{{ route('admin.financeiro.contas.create') }}">Nova conta financeira</a>
                    <a class="button-secondary" href="{{ route('admin.financeiro.lancamentos.create') }}">Novo lancamento</a>
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Movimentacoes recentes</h3>
                        <p>Ultimos registros financeiros ligados a conta.</p>
                    </div>
                </div>

                @if ($movimentacoesRecentes->isEmpty())
                    <div class="empty-state">
                        Nenhuma movimentacao financeira registrada ainda. Quando o fluxo operacional entrar, essa area vai refletir entradas, saidas e origem dos lancamentos.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($movimentacoesRecentes as $movimentacao)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $movimentacao->descricao }}</strong>
                                    <small>
                                        {{ $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria' }}
                                        @if ($movimentacao->contaFinanceira)
                                            · {{ $movimentacao->contaFinanceira->nome }}
                                        @endif
                                        @if ($movimentacao->loja)
                                            · {{ $movimentacao->loja->nome }}
                                        @endif
                                    </small>
                                    <br>
                                    <code>{{ $movimentacao->data_movimentacao?->format('d/m/Y H:i') ?? 'Sem data' }}</code>
                                </div>
                                <span class="badge {{ $movimentacao->tipo === 'despesa' ? 'is-warning' : '' }}">
                                    R$ {{ number_format((float) $movimentacao->valor, 2, ',', '.') }}
                                </span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Categorias e contas</h3>
                        <p>Componentes base para organizar o financeiro da conta.</p>
                    </div>
                </div>

                <div class="stack">
                    @if ($categorias->isEmpty())
                        <div class="empty-state">
                            Ainda nao existem categorias financeiras configuradas para esta conta.
                        </div>
                    @else
                        <div class="table-list">
                            @foreach ($categorias as $categoria)
                                <article class="table-row">
                                    <div>
                                        <strong>{{ $categoria->nome }}</strong>
                                        <small>{{ $categoria->descricao ?: 'Categoria pronta para organizar lancamentos e titulos.' }}</small>
                                    </div>
                                    <span class="badge {{ $categoria->tipo === 'despesa' ? 'is-warning' : '' }}">
                                        {{ $categoria->tipo }}
                                    </span>
                                </article>
                            @endforeach
                        </div>
                    @endif

                    @if ($contasFinanceiras->isEmpty())
                        <div class="empty-state">
                            Nenhuma conta financeira cadastrada ainda. Esse sera um bom proximo passo para consolidar caixa, banco e outros saldos.
                        </div>
                    @else
                        <div class="table-list">
                            @foreach ($contasFinanceiras as $contaFinanceira)
                                <article class="table-row">
                                    <div>
                                        <strong>{{ $contaFinanceira->nome }}</strong>
                                        <small>
                                            {{ $contaFinanceira->tipo }}
                                            @if ($contaFinanceira->instituicao)
                                                · {{ $contaFinanceira->instituicao }}
                                            @endif
                                            @if ($contaFinanceira->loja)
                                                · {{ $contaFinanceira->loja->nome }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge">R$ {{ number_format((float) $contaFinanceira->saldo_atual, 2, ',', '.') }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Contas a pagar</h3>
                        <p>Titulos com saida prevista ou pendente.</p>
                    </div>
                </div>

                @if ($contasPagarPendentes->isEmpty())
                    <div class="empty-state">
                        Nenhuma conta a pagar pendente no momento.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($contasPagarPendentes as $titulo)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $titulo->descricao }}</strong>
                                    <small>
                                        {{ $titulo->fornecedor_nome ?: 'Fornecedor nao informado' }}
                                        @if ($titulo->categoriaFinanceira)
                                            · {{ $titulo->categoriaFinanceira->nome }}
                                        @endif
                                        @if ($titulo->loja)
                                            · {{ $titulo->loja->nome }}
                                        @endif
                                    </small>
                                    <br>
                                    <code>Vence em {{ $titulo->vencimento?->format('d/m/Y') ?? 'Sem data' }}</code>
                                </div>
                                <span class="badge is-warning">R$ {{ number_format((float) $titulo->valor_total, 2, ',', '.') }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Contas a receber</h3>
                        <p>Titulos com entrada prevista ou pendente.</p>
                    </div>
                </div>

                @if ($contasReceberPendentes->isEmpty())
                    <div class="empty-state">
                        Nenhuma conta a receber pendente no momento.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($contasReceberPendentes as $titulo)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $titulo->descricao }}</strong>
                                    <small>
                                        {{ $titulo->cliente_nome ?: 'Cliente nao informado' }}
                                        @if ($titulo->categoriaFinanceira)
                                            · {{ $titulo->categoriaFinanceira->nome }}
                                        @endif
                                        @if ($titulo->loja)
                                            · {{ $titulo->loja->nome }}
                                        @endif
                                    </small>
                                    <br>
                                    <code>Vence em {{ $titulo->vencimento?->format('d/m/Y') ?? 'Sem data' }}</code>
                                </div>
                                <span class="badge">R$ {{ number_format((float) $titulo->valor_total, 2, ',', '.') }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>
@endsection
