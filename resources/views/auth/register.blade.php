@extends('layouts.auth')

@section('title', 'Criar conta')
@section('eyebrow', 'alertas inteligentes')
@section('heading', 'Crie sua conta para nunca perder uma boa oferta.')
@section('description', 'Salve produtos, defina o preco que faz sentido para voce e acompanhe quando as lojas ficam competitivas de verdade.')
@section('form_title', 'Conta gratuita de consumidor')
@section('form_description', 'Esse acesso abre sua area de cliente para alertas, acompanhamento de ofertas e futuras preferencias de compra.')

@section('features')
    <article class="feature-card">
        <strong>Alertas por produto</strong>
        <span>Escolha o preco alvo e acompanhe quando o mercado chegar perto do valor ideal.</span>
    </article>
    <article class="feature-card">
        <strong>Comparacao sem pressa</strong>
        <span>Volte ao painel quando quiser para revisar produtos monitorados, lojas e historico.</span>
    </article>
    <article class="feature-card">
        <strong>Separado do painel lojista</strong>
        <span>A experiencia do consumidor fica limpa e nao mistura operacao, financeiro ou equipe.</span>
    </article>
@endsection

@section('form')
    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <label for="name">
            <span>Nome</span>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
            <span class="field-help">Use o nome que vai aparecer na sua area de cliente.</span>
        </label>

        <label for="email">
            <span>E-mail</span>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            <span class="field-help">Vamos usar esse e-mail para seu acesso e comunicacoes importantes.</span>
        </label>

        <label for="password">
            <span>Senha</span>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            <span class="field-help">Use pelo menos 8 caracteres.</span>
        </label>

        <label for="password_confirmation">
            <span>Confirmar senha</span>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>

        <button class="button" type="submit">Criar minha conta</button>

        <div class="demo-box">
            <p>Ja tem acesso?</p>
            <a class="button-secondary" href="{{ route('login') }}">Entrar com minha conta</a>
        </div>
    </form>
@endsection
