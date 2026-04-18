@extends('layouts.admin')

@section('title', 'Nova conta financeira')
@section('heading', 'Nova conta financeira')
@section('subheading', 'Cadastre uma estrutura de caixa, banco ou carteira para começar a operar lancamentos no painel.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Cadastro de conta financeira</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Essa conta podera receber lancamentos de receita, despesa ou ajuste no fluxo financeiro.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas.store') }}">
                @csrf

                @include('admin.financeiro.contas._form', ['submitLabel' => 'Cadastrar conta'])
            </form>
        </div>
    </section>
@endsection
