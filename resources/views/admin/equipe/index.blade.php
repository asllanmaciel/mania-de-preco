@extends('layouts.admin')

@section('title', 'Equipe')
@section('heading', 'Gestao da equipe')
@section('subheading', 'Organize acessos por papel para que operacao, financeiro e lideranca trabalhem com clareza dentro da conta.')

@section('content')
    <section class="grid-3">
        <article class="metric-card card">
            <span class="metric-label">Membros ativos</span>
            <strong class="metric-value">{{ number_format($usuarios->where('pivot.ativo', true)->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">time operacional habilitado</span>
        </article>
        <article class="metric-card card">
            <span class="metric-label">Owners e gestores</span>
            <strong class="metric-value">{{ number_format(($contagemPorPapel['owner'] ?? 0) + ($contagemPorPapel['gestor'] ?? 0), 0, ',', '.') }}</strong>
            <span class="metric-trend">governanca da conta</span>
        </article>
        <article class="metric-card card">
            <span class="metric-label">Funcoes ativas</span>
            <strong class="metric-value">{{ number_format(collect($contagemPorPapel)->filter(fn ($total) => $total > 0)->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">papeis em uso</span>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="toolbar">
                <div>
                    <h2 style="margin:0;">Estrutura de acesso</h2>
                    <p class="helper-text" style="margin:8px 0 0;">Defina quem governa a conta e quem opera cada frente com seguranca.</p>
                </div>
                <div class="toolbar-actions">
                    <a class="button" href="{{ route('admin.equipe.create') }}">Adicionar membro</a>
                </div>
            </div>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <form class="filter-row" method="GET" action="{{ route('admin.equipe.index') }}">
                <input type="text" name="busca" value="{{ $busca }}" placeholder="Buscar por nome ou e-mail">
                <select name="papel">
                    <option value="">Todos os papeis</option>
                    @foreach ($papeisDisponiveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($papelSelecionado === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                <button class="button" type="submit">Filtrar</button>
                <a class="button-secondary" href="{{ route('admin.equipe.index') }}">Limpar</a>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="table-list">
                @forelse ($usuarios as $membro)
                    <div class="table-row">
                        <div>
                            <strong>{{ $membro->name }}</strong>
                            <small>{{ $membro->email }}</small>
                        </div>
                        <div>
                            <strong>{{ $papeisDisponiveis[$membro->pivot->papel] ?? $membro->pivot->papel }}</strong>
                            <small>papel atual na conta</small>
                        </div>
                        <div>
                            <strong>{{ $membro->pivot->ativo ? 'Ativo' : 'Inativo' }}</strong>
                            <small>ultimo acesso {{ $membro->pivot->ultimo_acesso_em ? \Illuminate\Support\Carbon::parse($membro->pivot->ultimo_acesso_em)->format('d/m/Y H:i') : 'ainda nao registrado' }}</small>
                        </div>
                        <div class="list-actions">
                            <a class="button-secondary" href="{{ route('admin.equipe.edit', $membro) }}">Editar</a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        Nenhum membro foi adicionado a equipe ainda. Crie os primeiros acessos para separar lideranca, operacao e financeiro sem depender de um unico login.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="pagination-wrap">
        {{ $usuarios->links() }}
    </section>
@endsection
