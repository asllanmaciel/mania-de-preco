@extends('layouts.admin')

@section('title', 'Lojas')
@section('heading', 'Lojas da conta')
@section('subheading', 'Cadastre os canais de venda que alimentam o comparador e estruturam a operacao comercial da conta.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">Operacao por loja</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Essas lojas ficam ligadas diretamente a conta ativa e servem de base para precos, financeiro e catalogo.</p>
                </div>

                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.lojas.create') }}">Nova loja</a>
                </div>
            </div>

            <div class="stats-grid">
                <article class="stat-card-soft">
                    <strong>
                        {{ number_format($usoPlano['metricas']['lojas']['usado'], 0, ',', '.') }}
                        @if (! $usoPlano['metricas']['lojas']['ilimitado'])
                            / {{ number_format($usoPlano['metricas']['lojas']['limite'], 0, ',', '.') }}
                        @endif
                    </strong>
                    <span>uso do limite de lojas do plano</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($lojas->getCollection()->where('status', 'ativo')->count(), 0, ',', '.') }}</strong>
                    <span>lojas ativas nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ $usoPlano['metricas']['lojas']['ilimitado'] ? 'sem limite' : number_format($usoPlano['metricas']['lojas']['disponivel'], 0, ',', '.') }}</strong>
                    <span>lojas ainda disponiveis no plano atual</span>
                </article>
            </div>

            <form class="filter-row" method="GET" action="{{ route('admin.lojas.index') }}">
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome, cidade, UF ou tipo">
                <button class="button-secondary" type="submit">Filtrar</button>
                @if ($busca !== '')
                    <a class="button-secondary" href="{{ route('admin.lojas.index') }}">Limpar</a>
                @endif
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <span class="pill">Prontidao comercial</span>
                    <h2>Lojas preparadas para aparecer melhor</h2>
                    <p>Antes de vender mais, cada loja precisa ter contato, preco e prova publica minimamente consistentes.</p>
                </div>
                <a class="button-secondary" href="{{ route('admin.precos.index') }}">Conectar precos</a>
            </div>

            <div class="highlight-grid">
                <article class="highlight-card">
                    <strong>{{ number_format($lojasResumo['sem_preco'], 0, ',', '.') }}</strong>
                    <span>lojas sem precos cadastrados. Elas existem, mas ainda nao alimentam o comparador.</span>
                </article>
                <article class="highlight-card">
                    <strong>{{ number_format($lojasResumo['sem_contato'], 0, ',', '.') }}</strong>
                    <span>lojas sem e-mail, telefone ou WhatsApp. Complete contatos para passar mais confianca.</span>
                </article>
                <article class="highlight-card">
                    <strong>{{ number_format($lojasResumo['com_avaliacoes'], 0, ',', '.') }}</strong>
                    <span>lojas com avaliacoes publicas, um sinal importante para conversao.</span>
                </article>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body stack">
            @if ($lojas->isEmpty())
                <div class="empty-state">
                    Nenhuma loja foi cadastrada ainda. Esse e o primeiro passo operacional do SaaS porque organiza a conta e abre caminho para tabela de precos, catalogo e comparacao publica.
                </div>
            @else
                <div class="table-head">
                    <span>Loja</span>
                    <span>Local / tipo</span>
                    <span>Status / volume</span>
                    <span>Acoes</span>
                </div>

                <div class="list-grid">
                    @foreach ($lojas as $loja)
                        <article class="list-row">
                            <div>
                                <strong>{{ $loja->nome }}</strong>
                                <small>{{ $loja->email ?: 'sem e-mail cadastrado' }}</small><br>
                                <code>{{ $loja->cnpj ?: 'sem CNPJ' }}</code>
                            </div>

                            <div>
                                <span class="pill">{{ $loja->cidade ?: 'Cidade nao informada' }}{{ $loja->uf ? ' / ' . $loja->uf : '' }}</span>
                                <small style="display: block; margin-top: 8px;">{{ ucfirst($loja->tipo_loja) }}</small>
                            </div>

                            <div>
                                <span class="badge {{ $loja->status === 'inativo' ? 'is-warning' : '' }}">{{ $loja->status }}</span>
                                <small style="display: block; margin-top: 8px;">{{ $loja->precos_count }} precos · {{ $loja->avaliacoes_count }} avaliacoes</small>
                            </div>

                            <div class="list-actions">
                                <a class="button-secondary" href="{{ route('admin.lojas.edit', $loja) }}">Editar</a>

                                <form class="inline-form" method="POST" action="{{ route('admin.lojas.destroy', $loja) }}" onsubmit="return confirm('Deseja remover esta loja do painel?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="button-danger" type="submit">Excluir</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pagination-wrap">
                    {{ $lojas->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
