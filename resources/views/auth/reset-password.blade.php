@extends('layouts.auth')

@section('title', 'Nova senha')
@section('eyebrow', 'senha nova')
@section('heading', 'Defina uma nova senha.')
@section('description', 'Escolha uma senha forte para recuperar seu acesso e manter a operacao da conta protegida.')
@section('form_title', 'Atualizar credencial')
@section('form_description', 'Confirme o e-mail, defina a nova senha e volte ao painel com seguranca.')

@section('features')
    <article class="feature-card">
        <strong>Conta protegida</strong>
        <span>Depois da troca, use apenas a nova senha para acessar o painel.</span>
    </article>
    <article class="feature-card">
        <strong>Senha mais forte</strong>
        <span>Prefira uma combinacao longa, unica e dificil de adivinhar.</span>
    </article>
    <article class="feature-card">
        <strong>Operacao sem pausa</strong>
        <span>Ao concluir, voce pode voltar ao fluxo normal de lojas, precos e financeiro.</span>
    </article>
@endsection

@section('form')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">
            <span>E-mail</span>
            <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
        </label>

        <label for="password">
            <span>Nova senha</span>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            <span class="field-help">Use pelo menos 8 caracteres, misturando letras e numeros.</span>
        </label>

        <label for="password_confirmation">
            <span>Confirmar nova senha</span>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>

        <div class="actions">
            <button class="button" type="submit">Redefinir senha</button>
            <a class="button-secondary" href="{{ route('login') }}">Voltar ao login</a>
        </div>
    </form>
@endsection
