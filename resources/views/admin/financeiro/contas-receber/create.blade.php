@extends('layouts.admin')

@section('title', 'Nova conta a receber')
@section('heading', 'Nova conta a receber')
@section('subheading', 'Cadastre uma entrada prevista da conta com vencimento, cliente e situacao de recebimento.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Cadastro de titulo a receber</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Essa estrutura ajuda a organizar previsao de caixa, cobrancas e controle de recebimentos da operacao.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas-receber.store') }}">
                @csrf

                @include('admin.financeiro.contas-receber._form', ['submitLabel' => 'Cadastrar conta a receber'])
            </form>
        </div>
    </section>
@endsection
