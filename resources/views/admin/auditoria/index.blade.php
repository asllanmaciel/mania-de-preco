@extends('layouts.admin')

@section('title', 'Auditoria')
@section('heading', 'Auditoria da conta')
@section('subheading', 'Acompanhe eventos relevantes da operacao para entender quem fez o que, quando e em qual area do sistema.')

@section('content')
    <section class="grid-3">
        <article class="metric-card card">
            <span class="metric-label">Eventos auditados</span>
            <strong class="metric-value">{{ number_format($logs->total(), 0, ',', '.') }}</strong>
            <span class="metric-trend">historico filtrado atual</span>
        </article>
        <article class="metric-card card">
            <span class="metric-label">Areas com eventos</span>
            <strong class="metric-value">{{ number_format($areas->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">cobertura operacional</span>
        </article>
        <article class="metric-card card">
            <span class="metric-label">Acoes registradas</span>
            <strong class="metric-value">{{ number_format($acoes->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">tipos de mudanca monitorados</span>
        </article>
    </section>

    <section class="card">
        <div class="card-body">
            <form class="filter-row" method="GET" action="{{ route('admin.auditoria') }}">
                <select name="area">
                    <option value="">Todas as areas</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area }}" @selected($areaSelecionada === $area)>{{ $area }}</option>
                    @endforeach
                </select>
                <select name="acao">
                    <option value="">Todas as acoes</option>
                    @foreach ($acoes as $acao)
                        <option value="{{ $acao }}" @selected($acaoSelecionada === $acao)>{{ $acao }}</option>
                    @endforeach
                </select>
                <select name="user_id">
                    <option value="">Todos os usuarios</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" @selected($usuarioSelecionado === $usuario->id)>{{ $usuario->name }}</option>
                    @endforeach
                </select>
                <button class="button" type="submit">Filtrar</button>
                <a class="button-secondary" href="{{ route('admin.auditoria') }}">Limpar</a>
            </form>
        </div>
    </section>

    <section class="card">
        <div class="card-body">
            <div class="table-list">
                @forelse ($logs as $log)
                    <article class="table-row">
                        <div>
                            <strong>{{ $log->descricao }}</strong>
                            <small>{{ $log->created_at->format('d/m/Y H:i') }} | {{ $log->usuario?->name ?? 'Sistema' }} | {{ $log->ip ?: 'IP nao registrado' }}</small>
                        </div>
                        <div>
                            <strong>{{ $log->area }}</strong>
                            <small>{{ $log->acao }}</small>
                        </div>
                        <div>
                            <strong>{{ class_basename((string) $log->entidade_tipo) ?: 'Evento' }}</strong>
                            <small>{{ $log->entidade_id ? '#' . $log->entidade_id : 'sem entidade' }}</small>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        Ainda nao existem eventos de auditoria para os filtros selecionados. Novas acoes relevantes da equipe passarao a aparecer aqui automaticamente.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="pagination-wrap">
        {{ $logs->links() }}
    </section>
@endsection
