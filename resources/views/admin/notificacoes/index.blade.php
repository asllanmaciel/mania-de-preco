@extends('layouts.admin')

@section('title', 'Notificacoes')
@section('heading', 'Central de acoes')
@section('subheading', 'Alertas operacionais calculados a partir de dados reais da conta: assinatura, financeiro, onboarding, suporte e vitrine.')

@section('content')
    <section class="grid-3">
        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Total no radar</span>
                <span class="metric-icon"><x-ui.icon name="bell" /></span>
            </div>
            <strong class="metric-value">{{ number_format($notificacoes->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">acoes calculadas agora</span>
        </article>

        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Riscos</span>
                <span class="metric-icon is-danger"><x-ui.icon name="alert" /></span>
            </div>
            <strong class="metric-value">{{ number_format($notificacoes->where('tipo', 'risco')->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend {{ $notificacoes->where('tipo', 'risco')->count() > 0 ? 'is-danger' : '' }}">prioridade alta</span>
        </article>

        <article class="card metric-card">
            <div class="metric-head">
                <span class="metric-label">Proximas acoes</span>
                <span class="metric-icon is-teal"><x-ui.icon name="check" /></span>
            </div>
            <strong class="metric-value">{{ number_format($notificacoes->whereIn('tipo', ['alerta', 'info'])->count(), 0, ',', '.') }}</strong>
            <span class="metric-trend">oportunidades de melhoria</span>
        </article>
    </section>

    <section class="card">
        <div class="card-body stack">
            <div class="section-header">
                <div>
                    <h2>Fila inteligente</h2>
                    <p>Use esta lista como um checklist vivo. Quando os dados mudam, a central muda junto.</p>
                </div>
            </div>

            <div class="signal-list">
                @foreach ($notificacoes as $notificacao)
                    <article class="signal-item">
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <span class="metric-icon {{ $notificacao['tipo'] === 'risco' ? 'is-danger' : ($notificacao['tipo'] === 'alerta' ? 'is-warning' : ($notificacao['tipo'] === 'sucesso' ? 'is-teal' : '')) }}">
                                <x-ui.icon :name="$notificacao['icone']" />
                            </span>
                            <div>
                                <strong>{{ $notificacao['titulo'] }}</strong>
                                <span>{{ $notificacao['descricao'] }}</span>
                                <small class="helper-text">
                                    Area: {{ ucfirst($notificacao['area']) }}
                                    @if ($notificacao['lida'])
                                        | vista {{ $notificacao['lida_em']?->diffForHumans() }}
                                    @endif
                                    @if ($notificacao['dispensada'])
                                        | dispensada ate {{ $notificacao['dispensada_ate']?->format('d/m H:i') }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="toolbar-actions">
                            @if ($notificacao['rota'])
                                <a class="ghost-link" href="{{ $notificacao['rota'] }}">{{ $notificacao['acao'] }}</a>
                            @else
                                <small class="helper-text">Sem permissao para executar esta acao neste perfil.</small>
                            @endif

                            @if (! $notificacao['lida'])
                                <form class="inline-form" method="POST" action="{{ route('admin.notificacoes.interagir') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="chave" value="{{ $notificacao['chave'] }}">
                                    <input type="hidden" name="acao" value="ler">
                                    <button class="ghost-link" type="submit">Marcar vista</button>
                                </form>
                            @endif

                            @if (! $notificacao['dispensada'] && $notificacao['tipo'] !== 'sucesso')
                                <form class="inline-form" method="POST" action="{{ route('admin.notificacoes.interagir') }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="chave" value="{{ $notificacao['chave'] }}">
                                    <input type="hidden" name="acao" value="dispensar">
                                    <button class="button-secondary" type="submit">Dispensar 24h</button>
                                </form>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
