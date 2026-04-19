@extends('layouts.auth')

@section('title', 'Recuperar senha')
@section('eyebrow', 'recuperacao de acesso')
@section('heading', 'Recupere seu acesso com seguranca.')
@section('description', 'Informe o e-mail usado na conta. Se ele existir na base, enviaremos um link para redefinir a senha e voltar ao painel.')
@section('form_title', 'Esqueci minha senha')
@section('form_description', 'O link de recuperacao sera enviado para o e-mail cadastrado, quando houver uma conta correspondente.')

@section('features')
    <article class="feature-card">
        <strong>Fluxo seguro</strong>
        <span>O link de redefinicao usa token temporario e nao expõe a senha atual.</span>
    </article>
    <article class="feature-card">
        <strong>Sem intervencao manual</strong>
        <span>Usuarios conseguem recuperar acesso sem depender do time operacional.</span>
    </article>
    <article class="feature-card">
        <strong>Suporte com contexto</strong>
        <span>Se o e-mail nao chegar, abra um chamado informando e-mail e horario da tentativa.</span>
    </article>
@endsection

@section('form')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">
            <span>E-mail</span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            <span class="field-help">Use o e-mail vinculado ao seu usuario no painel.</span>
        </label>

        <div class="actions">
            <button class="button" type="submit">Enviar link de recuperacao</button>
            <a class="button-secondary" href="{{ route('login') }}">Voltar ao login</a>
        </div>
    </form>
@endsection
