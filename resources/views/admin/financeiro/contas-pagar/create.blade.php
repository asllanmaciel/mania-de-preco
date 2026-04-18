@extends('layouts.admin')

@section('title', 'Nova conta a pagar')
@section('heading', 'Nova conta a pagar')
@section('subheading', 'Cadastre um compromisso financeiro da conta com vencimento, fornecedor e situacao de pagamento.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Cadastro de titulo a pagar</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Essa estrutura ajuda a organizar saidas previstas, vencimentos e controle de pagamentos da operacao.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas-pagar.store') }}">
                @csrf

                @include('admin.financeiro.contas-pagar._form', ['submitLabel' => 'Cadastrar conta a pagar'])
            </form>
        </div>
    </section>
@endsection
