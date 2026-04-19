@extends('layouts.auth')

@section('title', 'Entrar')
@section('eyebrow', 'acesso seguro')
@section('heading', 'Entre para comandar sua operacao.')
@section('description', 'Acesse o painel para acompanhar lojas, catalogo, precos, financeiro, assinatura e equipe em uma experiencia pensada para decisao rapida.')
@section('form_title', 'Login administrativo')
@section('form_description', 'Use o acesso vinculado a sua conta para entrar no painel.')

@section('features')
    <article class="feature-card">
        <strong>Painel protegido</strong>
        <span>Rotas internas ficam disponiveis apenas para usuarios autenticados e autorizados.</span>
    </article>
    <article class="feature-card">
        <strong>Conta e assinatura</strong>
        <span>O sistema reconhece a conta ativa, o plano contratado e os limites operacionais.</span>
    </article>
    <article class="feature-card">
        <strong>Operacao em um lugar</strong>
        <span>Financeiro, catalogo, lojas, equipe e precificacao seguem conectados no mesmo fluxo.</span>
    </article>
@endsection

@section('form')
    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <label for="email">
            <span>E-mail</span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
            <span class="field-help">Informe o e-mail vinculado ao usuario da conta.</span>
        </label>

        <label for="password">
            <span>Senha</span>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            <span class="field-help">Nunca compartilhe sua senha com suporte ou terceiros.</span>
        </label>

        <div class="remember-row">
            <label class="remember-toggle" for="remember">
                <input id="remember" type="checkbox" name="remember" value="1">
                <span>Manter sessao ativa</span>
            </label>

            <a href="{{ route('password.request') }}">Esqueci minha senha</a>
        </div>

        <button class="button" type="submit">Entrar no painel</button>
    </form>

    @if (app()->environment('local'))
        <div class="demo-box">
            <p>Credenciais da base demo local:</p>
            <code>test@example.com / password</code>
        </div>
    @endif

    <div class="demo-box">
        <p>Ainda nao tem conta de consumidor?</p>
        <a class="button-secondary" href="{{ route('register') }}">Criar conta gratuita</a>
    </div>
@endsection
