@extends('layouts.backoffice')

@section('title', $conta->nome_fantasia)
@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.planos.index') }}">Planos</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
    <a class="chip" href="{{ route('super-admin.contas.show', $conta) }}">Detalhe</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>{{ $conta->nome_fantasia }}</h1>
        <p>Visao estrutural da conta para acompanhamento de assinatura, usuarios, lojas, base financeira e intensidade operacional sem precisar entrar no painel do lojista.</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <span class="chip">{{ $conta->status }}</span>
            <span class="chip">{{ $assinaturaAtual?->plano?->nome ?? 'Sem plano' }}</span>
            @if ($conta->email)
                <span class="chip">{{ $conta->email }}</span>
            @endif
            <span class="chip">{{ $conta->billing_provider ?: 'billing pendente' }}</span>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Saude da conta</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Leitura executiva para sucesso do cliente, retencao, upgrade e priorizacao de suporte.</p>
                </div>
                <span class="chip">{{ $saudeConta['nivel']['nome'] }} | {{ $saudeConta['score'] }}/100</span>
            </div>

            <div class="grid-2" style="margin-top:18px;">
                <article class="mini-card">
                    <strong>{{ $saudeConta['proxima_acao']['titulo'] ?? 'Conta estavel' }}</strong>
                    <span>{{ $saudeConta['proxima_acao']['descricao'] ?? 'Nenhuma acao critica identificada neste momento.' }}</span>
                </article>
                <article class="mini-card">
                    <strong>{{ number_format(count($saudeConta['sinais']), 0, ',', '.') }} sinais</strong>
                    <span>{{ $saudeConta['nivel']['descricao'] }}</span>
                </article>
            </div>

            <div class="grid-4" style="margin-top:18px;">
                @foreach ($saudeConta['pilares'] as $pilar)
                    <article class="mini-card">
                        <strong>{{ $pilar['score'] }}/100</strong>
                        <span>{{ $pilar['nome'] }}</span>
                        <small>{{ $pilar['descricao'] }}</small>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid-3">
        <article class="metric"><strong>{{ number_format($metricas['usuarios'], 0, ',', '.') }}</strong><span>usuarios vinculados</span></article>
        <article class="metric"><strong>{{ number_format($metricas['lojas'], 0, ',', '.') }}</strong><span>lojas operando</span></article>
        <article class="metric"><strong>{{ number_format($metricas['contas_financeiras'], 0, ',', '.') }}</strong><span>contas financeiras</span></article>
        <article class="metric"><strong>{{ number_format($metricas['movimentacoes'], 0, ',', '.') }}</strong><span>movimentacoes recentes no recorte</span></article>
        <article class="metric"><strong>{{ number_format($metricas['pagar_aberto'], 0, ',', '.') }}</strong><span>titulos a pagar abertos</span></article>
        <article class="metric"><strong>{{ number_format($metricas['receber_aberto'], 0, ',', '.') }}</strong><span>titulos a receber abertos</span></article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="section-head">
                <div>
                    <h2 style="margin:0;">Uso comercial do plano</h2>
                    <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Consumo real da conta contra os limites contratados, pronto para orientar upgrade, suporte e risco operacional.</p>
                </div>
                <span class="chip">{{ $usoPlano['plano']?->nome ?? 'Sem plano' }}</span>
            </div>

            <div class="grid-3" style="margin-top:18px;">
                @foreach ($usoPlano['metricas'] as $metrica)
                    <article class="mini-card">
                        <strong>
                            {{ number_format($metrica['usado'], 0, ',', '.') }}
                            @if (! $metrica['ilimitado'])
                                / {{ number_format($metrica['limite'], 0, ',', '.') }}
                            @endif
                        </strong>
                        <span>{{ ucfirst($metrica['rotulo']) }} consumidos</span>
                        <small>{{ $metrica['ilimitado'] ? 'sem limite configurado' : $metrica['disponivel'] . ' disponiveis' }}</small>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Assinaturas</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Historico contratual da conta com leitura pronta para sincronizacao externa.</p>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <a class="button-secondary" href="{{ route('super-admin.contas.assinaturas.create', $conta) }}">Nova assinatura</a>
                        @if ($assinaturaAtual)
                            <a class="button-secondary" href="{{ route('super-admin.contas.assinaturas.edit', [$conta, $assinaturaAtual]) }}">Editar atual</a>
                            <form method="POST" action="{{ route('super-admin.assinaturas.billing.sync', [$conta, $assinaturaAtual]) }}">
                                @csrf
                                <button class="button" type="submit">Sincronizar cobranca</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid-2" style="margin-bottom:18px;">
                    <div class="mini-card">
                        <strong>{{ $conta->billing_customer_id ?: 'nao sincronizado' }}</strong>
                        <span>cliente externo no provedor de cobranca</span>
                    </div>
                    <div class="mini-card">
                        <strong>{{ $assinaturaAtual?->billing_subscription_id ?: 'nao sincronizada' }}</strong>
                        <span>assinatura externa vinculada a esta conta</span>
                    </div>
                </div>

                <div class="list">
                    @forelse ($conta->assinaturas as $assinatura)
                        <div class="list-row">
                            <strong>{{ $assinatura->plano?->nome ?? 'Plano nao informado' }}</strong>
                            <small>
                                {{ $assinatura->status }} | {{ $assinatura->ciclo_cobranca }}
                                | R$ {{ number_format((float) $assinatura->valor, 2, ',', '.') }}
                                | expira em {{ $assinatura->expira_em?->format('d/m/Y') ?? 'sem data' }}
                                | billing {{ $assinatura->billing_status ?? 'pendente' }}
                            </small>
                            <small style="display:block; margin-top:6px;">
                                <a href="{{ route('super-admin.contas.assinaturas.edit', [$conta, $assinatura]) }}">Abrir configuracao desta assinatura</a>
                            </small>
                            @if ($assinatura->billing_checkout_url)
                                <small style="display:block; margin-top:6px;">
                                    <a href="{{ $assinatura->billing_checkout_url }}" target="_blank" rel="noreferrer">Abrir cobranca inicial</a>
                                </small>
                            @endif
                        </div>
                    @empty
                        <div class="mini-card">
                            <strong>Sem assinatura registrada</strong>
                            <span>A conta ainda nao possui historico contratual disponivel.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Usuarios da conta</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Quem esta operando a conta e com qual papel.</p>
                    </div>
                </div>

                <div class="list">
                    @foreach ($conta->usuarios as $usuario)
                        <div class="list-row">
                            <strong>{{ $usuario->name }}</strong>
                            <small>{{ $usuario->email }} | {{ $usuario->pivot->papel }} | {{ $usuario->pivot->ativo ? 'ativo' : 'inativo' }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Lojas da conta</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Presenca operacional e intensidade de catalogo por unidade.</p>
                    </div>
                </div>

                <div class="list">
                    @foreach ($conta->lojas as $loja)
                        <div class="list-row">
                            <strong>{{ $loja->nome }}</strong>
                            <small>{{ $loja->cidade ?: 'Sem cidade' }} | {{ $loja->status }} | {{ number_format($loja->precos_count, 0, ',', '.') }} precos</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Contas financeiras</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Mapa resumido do caixa e dos saldos operacionais.</p>
                    </div>
                </div>

                <div class="list">
                    @foreach ($conta->contasFinanceiras as $financeira)
                        <div class="list-row">
                            <strong>{{ $financeira->nome }}</strong>
                            <small>{{ $financeira->tipo }} | saldo atual R$ {{ number_format((float) $financeira->saldo_atual, 2, ',', '.') }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Movimentacoes recentes</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Ultimos eventos financeiros da conta.</p>
                    </div>
                </div>

                <div class="list">
                    @forelse ($conta->movimentacoesFinanceiras as $movimentacao)
                        <div class="list-row">
                            <strong>{{ $movimentacao->descricao }}</strong>
                            <small>{{ $movimentacao->tipo }} | R$ {{ number_format((float) $movimentacao->valor, 2, ',', '.') }} | {{ $movimentacao->status }}</small>
                        </div>
                    @empty
                        <div class="mini-card">
                            <strong>Sem movimentacoes recentes</strong>
                            <span>A conta ainda nao gerou eventos financeiros no recorte carregado.</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </article>

        <article class="card">
            <div class="card-body">
                <div class="section-head">
                    <div>
                        <h2 style="margin:0;">Pressao de titulos</h2>
                        <p style="margin:8px 0 0; color:var(--muted); line-height:1.7;">Visao direta de contas a pagar e a receber abertas.</p>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="mini-card">
                        <strong>A pagar</strong>
                        <span>{{ number_format($metricas['pagar_aberto'], 0, ',', '.') }} em aberto ou parcial</span>
                    </div>
                    <div class="mini-card">
                        <strong>A receber</strong>
                        <span>{{ number_format($metricas['receber_aberto'], 0, ',', '.') }} em aberto ou parcial</span>
                    </div>
                </div>

                <div class="list" style="margin-top:18px;">
                    @foreach ($conta->contasPagar as $titulo)
                        <div class="list-row">
                            <strong>{{ $titulo->descricao }}</strong>
                            <small>a pagar | R$ {{ number_format((float) $titulo->valor_total, 2, ',', '.') }} | {{ $titulo->status }}</small>
                        </div>
                    @endforeach
                    @foreach ($conta->contasReceber as $titulo)
                        <div class="list-row">
                            <strong>{{ $titulo->descricao }}</strong>
                            <small>a receber | R$ {{ number_format((float) $titulo->valor_total, 2, ',', '.') }} | {{ $titulo->status }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>
@endsection
