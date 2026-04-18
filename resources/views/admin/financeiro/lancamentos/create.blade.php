@extends('layouts.admin')

@section('title', 'Novo lancamento')
@section('heading', 'Novo lancamento financeiro')
@section('subheading', 'Registre uma receita, despesa ou transferencia para alimentar o financeiro da conta.')

@section('content')
    @include('admin.financeiro._nav')

    @if ($contasFinanceiras->isEmpty())
        <section class="card">
            <div class="card-body">
                <div class="empty-state">
                    Antes de cadastrar um lancamento, voce precisa criar ao menos uma conta financeira.
                    <br><br>
                    <a class="button" href="{{ route('admin.financeiro.contas.create') }}">Cadastrar conta financeira</a>
                </div>
            </div>
        </section>
    @else
        <section class="card">
            <div class="card-body stack">
                <div>
                    <h2 style="margin: 0;">Cadastro de lancamento</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">O sistema usa esse registro para compor saldo, historico e leitura financeira do painel.</p>
                </div>

                <form class="stack" method="POST" action="{{ route('admin.financeiro.lancamentos.store') }}">
                    @csrf

                    @include('admin.financeiro.lancamentos._form', ['submitLabel' => 'Cadastrar lancamento'])
                </form>
            </div>
        </section>
    @endif
@endsection
