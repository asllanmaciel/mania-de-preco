@extends('layouts.backoffice')

@section('brand_route', route('super-admin.dashboard'))
@section('brand_label', 'Mania de Preco | Super Admin')

@section('nav')
    <a class="chip" href="{{ route('super-admin.dashboard') }}">Visao geral</a>
    <a class="chip" href="{{ route('super-admin.contas.index') }}">Contas</a>
    <a class="chip" href="{{ route('super-admin.planos.index') }}">Planos</a>
    <a class="chip" href="{{ route('super-admin.suporte.index') }}">Suporte</a>
@endsection

@section('content')
    <section class="card hero">
        <h1>@yield('heading')</h1>
        <p>@yield('subheading')</p>
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
                        <span>Nome</span>
                        <input type="text" name="nome" value="{{ old('nome', $plano->nome) }}" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Slug</span>
                        <input type="text" name="slug" value="{{ old('slug', $plano->slug) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                </div>

                <label style="display:grid; gap:8px;">
                    <span>Descricao</span>
                    <textarea name="descricao" rows="4" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">{{ old('descricao', $plano->descricao) }}</textarea>
                </label>

                <div class="grid-2">
                    <label style="display:grid; gap:8px;">
                        <span>Valor mensal</span>
                        <input type="number" name="valor_mensal" min="0" step="0.01" value="{{ old('valor_mensal', $plano->valor_mensal) }}" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Valor anual</span>
                        <input type="number" name="valor_anual" min="0" step="0.01" value="{{ old('valor_anual', $plano->valor_anual) }}" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                </div>

                <div class="grid-3">
                    <label style="display:grid; gap:8px;">
                        <span>Limite de usuarios</span>
                        <input type="number" name="limite_usuarios" min="1" value="{{ old('limite_usuarios', $plano->limite_usuarios) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Limite de lojas</span>
                        <input type="number" name="limite_lojas" min="1" value="{{ old('limite_lojas', $plano->limite_lojas) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                    <label style="display:grid; gap:8px;">
                        <span>Limite de produtos</span>
                        <input type="number" name="limite_produtos" min="1" value="{{ old('limite_produtos', $plano->limite_produtos) }}" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                    </label>
                </div>

                <div class="grid-2">
                    <label style="display:grid; gap:8px;">
                        <span>Status</span>
                        <select name="status" required style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">
                            @foreach (['ativo' => 'Ativo', 'inativo' => 'Inativo'] as $valor => $rotulo)
                                <option value="{{ $valor }}" @selected(old('status', $plano->status ?: 'ativo') === $valor)>{{ $rotulo }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <label style="display:grid; gap:8px;">
                    <span>Recursos do plano</span>
                    <textarea name="recursos_texto" rows="6" style="padding:14px 16px; border-radius:14px; border:1px solid var(--line); background:rgba(255,255,255,.8); font:inherit;">{{ old('recursos_texto', implode(PHP_EOL, $plano->recursos ?? [])) }}</textarea>
                    <small style="color:var(--muted);">Informe um recurso por linha para manter a proposta de valor organizada.</small>
                </label>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="button" type="submit">Salvar plano</button>
                    <a class="button-secondary" href="{{ route('super-admin.planos.index') }}">Voltar</a>
                </div>
            </form>
        </div>
    </section>
@endsection
