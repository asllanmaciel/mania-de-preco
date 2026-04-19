@extends('layouts.admin')

@section('title', 'Meu perfil')
@section('heading', 'Meu perfil')
@section('subheading', 'Gerencie seus dados de acesso e mantenha sua senha protegida para operar a conta com seguranca.')

@section('content')
    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="grid-3">
        <article class="metric-card card">
            <span class="metric-label">Usuario</span>
            <strong class="metric-value">{{ $user->name }}</strong>
            <span class="metric-trend">perfil autenticado</span>
        </article>

        <article class="metric-card card">
            <span class="metric-label">E-mail</span>
            <strong class="metric-value" style="font-size:1.4rem;">{{ $user->email }}</strong>
            <span class="metric-trend {{ $user->email_verified_at ? '' : 'is-danger' }}">
                {{ $user->email_verified_at ? 'verificado' : 'verificacao pendente' }}
            </span>
        </article>

        <article class="metric-card card">
            <span class="metric-label">Papel na conta</span>
            <strong class="metric-value">{{ $papelAtualConta ?: 'sem papel' }}</strong>
            <span class="metric-trend">permissoes aplicadas no painel</span>
        </article>
    </section>

    <section class="grid-2">
        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Dados pessoais</h2>
                        <p>Essas informacoes identificam voce nas acoes do painel, auditoria e operacao da conta.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.perfil.update') }}" class="stack" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mini-card" style="display:grid; grid-template-columns:auto minmax(0, 1fr); gap:16px; align-items:center;">
                        <span class="avatar" style="width:72px; height:72px; font-size:1.1rem;">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Foto de {{ $user->name }}">
                            @else
                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </span>
                        <div>
                            <strong>Sua foto no painel</strong>
                            <span>Ela aparece na topbar, no menu de perfil e ajuda a humanizar a operacao.</span>
                        </div>
                    </div>

                    <div class="form-grid">
                        <div class="field-group">
                            <label for="name">Nome</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="field-group">
                            <label for="email">E-mail</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                            <small>Se o e-mail for alterado, a verificacao futura precisara ser refeita.</small>
                        </div>

                        <div class="field-group-full">
                            <label for="avatar">Foto do perfil</label>
                            <input id="avatar" type="file" name="avatar" accept="image/png,image/jpeg,image/webp">
                            <small>Formatos aceitos: JPG, PNG ou WebP ate 2 MB.</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <span class="helper-text">Mudancas de perfil ficam registradas na auditoria.</span>
                        <button class="button" type="submit">Salvar perfil</button>
                    </div>
                </form>
            </div>
        </article>

        <article class="card">
            <div class="card-body stack">
                <div class="section-header">
                    <div>
                        <h2>Trocar senha</h2>
                        <p>Use uma senha forte e exclusiva para proteger o acesso ao painel.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.perfil.password') }}" class="stack">
                    @csrf
                    @method('PUT')

                    <div class="field-group">
                        <label for="current_password">Senha atual</label>
                        <input id="current_password" type="password" name="current_password" autocomplete="current-password" required>
                    </div>

                    <div class="field-group">
                        <label for="password">Nova senha</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" required>
                        <small>Use pelo menos 8 caracteres, com letras e numeros.</small>
                    </div>

                    <div class="field-group">
                        <label for="password_confirmation">Confirmar nova senha</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
                    </div>

                    <div class="form-actions">
                        <span class="helper-text">A troca tambem fica registrada na auditoria.</span>
                        <button class="button" type="submit">Atualizar senha</button>
                    </div>
                </form>
            </div>
        </article>
    </section>
@endsection
