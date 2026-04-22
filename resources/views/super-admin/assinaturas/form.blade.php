@extends('layouts.backoffice')

@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
    <a class="chip" href="{{ route('super-admin.contas.show', $conta) }}">Conta</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>@yield('heading')</h1>
        <p>@yield('subheading')</p>
        <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
            <span class="chip">{{ $conta->nome_fantasia }}</span>
            <span class="chip">{{ $conta->status }}</span>
        </div>
    </section>

    @if ($errors->any())
        <section class="card">
            <div class="card-body">
                <div class="flash-box" style="background:rgba(185,28,28,.08); border-color:rgba(185,28,28,.16); color:#8f1d1d;">
                    {{ $errors->first() }}
                </div>
            </div>
        </section>
    @endif

    <section class="card">
        <div class="card-body">
            <form method="POST" action="@yield('form_action')" style="display:grid; gap:18px;">
                @csrf
                @yield('form_method')

                <div class="grid-2">
                    <label style="display:grid; gap:8px;">
                        <span>Plano</span>
                        <select name="plano_id" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                            @foreach ($planos as $planoItem)
                                <option value="{{ $planoItem->id }}" @selected(old('plano_id', $assinatura->plano_id) == $planoItem->id)>
                                    {{ $planoItem->nome }} | R$ {{ number_format((float) $planoItem->valor_mensal, 2, ',', '.') }}/mes
                                </option>
                            @endforeach
                        </select>
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Status</span>
                        <select name="status" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                            @foreach (['trial' => 'Trial', 'ativa' => 'Ativa', 'inadimplente' => 'Inadimplente', 'cancelada' => 'Cancelada', 'encerrada' => 'Encerrada'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('status', $assinatura->status) === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="grid-3">
                    <label style="display:grid; gap:8px;">
                        <span>Ciclo</span>
                        <select name="ciclo_cobranca" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                            @foreach (['mensal' => 'Mensal', 'anual' => 'Anual'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('ciclo_cobranca', $assinatura->ciclo_cobranca ?: 'mensal') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Valor</span>
                        <input type="number" name="valor" min="0" step="0.01" value="{{ old('valor', $assinatura->valor) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Provedor de cobranca</span>
                        <select name="billing_provider" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                            <option value="">Definir depois</option>
                            <option value="asaas" @selected(old('billing_provider', $assinatura->billing_provider ?: config('billing.default_provider')) === 'asaas')>
                                Asaas | integracao atual temporaria
                            </option>
                            <option value="mercado_pago" disabled>
                                Mercado Pago | decidido para o MVP, em implementacao
                            </option>
                        </select>
                        <small style="color:var(--muted); line-height:1.6;">Mercado Pago foi definido como provedor do lancamento, mas ainda precisa de gateway e webhook operacionais. Ate la, Asaas permanece como integracao tecnica temporaria.</small>
                    </label>
                </div>

                <div class="grid-3">
                    <label style="display:grid; gap:8px;">
                        <span>Inicio</span>
                        <input type="date" name="inicia_em" value="{{ old('inicia_em', optional($assinatura->inicia_em)->format('Y-m-d') ?: $assinatura->inicia_em) }}" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Expira em</span>
                        <input type="date" name="expira_em" value="{{ old('expira_em', optional($assinatura->expira_em)->format('Y-m-d') ?: $assinatura->expira_em) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Cancelada em</span>
                        <input type="date" name="cancelada_em" value="{{ old('cancelada_em', optional($assinatura->cancelada_em)->format('Y-m-d') ?: $assinatura->cancelada_em) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                </div>

                <label style="display:grid; gap:8px;">
                    <span>Observacoes</span>
                    <textarea name="observacoes" rows="5" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">{{ old('observacoes', $assinatura->observacoes) }}</textarea>
                </label>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="button" type="submit">Salvar assinatura</button>
                    <a class="button-secondary" href="{{ route('super-admin.contas.show', $conta) }}">Voltar para a conta</a>
                </div>
            </form>
        </div>
    </section>
@endsection
