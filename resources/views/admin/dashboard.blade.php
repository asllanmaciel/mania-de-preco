@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Painel administrativo')
@section('subheading', 'Uma primeira area de operacao para a conta logada, com leitura de assinatura, lojas, movimentos e sinais do financeiro.')

@section('content')
    <section class="grid-4">
        <article class="card metric-card">
            <span class="metric-label">Saldo projetado</span>
            <strong class="metric-value">R$ {{ number_format($saldoProjetado, 2, ',', '.') }}</strong>
            <span class="metric-trend {{ $saldoProjetado < 0 ? 'is-danger' : '' }}">
                {{ $saldoProjetado < 0 ? 'acima do limite ideal' : 'resumo consolidado da conta' }}
            </span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Receitas registradas</span>
            <strong class="metric-value">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</strong>
            <span class="metric-trend">base puxada de movimentacoes</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Despesas registradas</span>
            <strong class="metric-value">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</strong>
            <span class="metric-trend is-danger">monitoramento de saidas</span>
        </article>

        <article class="card metric-card">
            <span class="metric-label">Precos monitorados</span>
            <strong class="metric-value">{{ number_format($totalPrecosMonitorados, 0, ',', '.') }}</strong>
            <span class="metric-trend">vitrine publica conectada</span>
        </article>
    </section>

    <section class="grid-3">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Operacao da conta</h2>
                        <p>Leitura rapida da estrutura ja criada para a conta ativa.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $conta->lojas_count }}</strong>
                        <span>lojas conectadas a esta conta</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->movimentacoes_financeiras_count }}</strong>
                        <span>movimentacoes financeiras ja registradas</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->categorias_financeiras_count }}</strong>
                        <span>categorias financeiras disponiveis</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Contas pendentes</h2>
                        <p>Visao do que ainda precisa entrar ou sair do caixa.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $contasPagarPendentes }}</strong>
                        <span>contas a pagar em aberto</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $contasReceberPendentes }}</strong>
                        <span>contas a receber em aberto</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $conta->contas_pagar_count + $conta->contas_receber_count }}</strong>
                        <span>titulos totais vinculados a conta</span>
                    </div>
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h2>Assinatura</h2>
                        <p>Estado comercial da conta e proximo passo do SaaS.</p>
                    </div>
                </div>

                <div class="mini-grid">
                    <div class="mini-card">
                        <strong>{{ $assinaturaAtual?->status ?? 'sem assinatura' }}</strong>
                        <small>status atual do plano</small>
                    </div>
                    <div class="mini-card">
                        <strong>R$ {{ number_format((float) ($assinaturaAtual?->valor ?? 0), 2, ',', '.') }}</strong>
                        <small>valor da assinatura ativa</small>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $assinaturaAtual?->expira_em?->format('d/m/Y') ?? 'sem data' }}</strong>
                        <small>vigencia atual da conta</small>
                    </div>
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-header">
                    <div>
                        <h3>Ultimas movimentacoes</h3>
                        <p>Essa area ja esta pronta para refletir o financeiro real da conta quando os lancamentos comecarem a entrar.</p>
                    </div>
                </div>

                @if ($ultimasMovimentacoes->isEmpty())
                    <div class="empty-state">
                        Ainda nao existem movimentacoes financeiras registradas para esta conta.
                        O proximo passo natural e criar o fluxo de lancamentos pelo painel.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($ultimasMovimentacoes as $movimentacao)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $movimentacao->descricao }}</strong>
                                    <small>
                                        {{ $movimentacao->categoriaFinanceira?->nome ?? 'Sem categoria' }}
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
                        <h3>Lojas ligadas a conta</h3>
                        <p>Base inicial para catalogo, precificacao e comparador publico.</p>
                    </div>
                </div>

                @if ($lojas->isEmpty())
                    <div class="empty-state">
                        Nenhuma loja foi cadastrada ainda. Quando a primeira loja entrar, esse card passa a mostrar os canais conectados a conta.
                    </div>
                @else
                    <div class="table-list">
                        @foreach ($lojas as $loja)
                            <article class="table-row">
                                <div>
                                    <strong>{{ $loja->nome }}</strong>
                                    <small>{{ $loja->cidade ?: 'Cidade nao informada' }} @if ($loja->uf) / {{ $loja->uf }} @endif</small>
                                    <br>
                                    <code>{{ $loja->email ?: 'sem email cadastrado' }}</code>
                                </div>
                                <span class="badge is-muted">{{ $loja->status ?: 'sem status' }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </article>
    </section>
@endsection
