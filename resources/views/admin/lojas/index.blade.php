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
                    <strong>{{ number_format($lojas->total(), 0, ',', '.') }}</strong>
                    <span>lojas cadastradas na conta</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($lojas->getCollection()->where('status', 'ativo')->count(), 0, ',', '.') }}</strong>
                    <span>lojas ativas nesta pagina</span>
                </article>
                <article class="stat-card-soft">
                    <strong>{{ number_format($lojas->getCollection()->sum('precos_count'), 0, ',', '.') }}</strong>
                    <span>precos vinculados nas lojas listadas</span>
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
