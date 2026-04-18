@extends('layouts.admin')

@section('title', 'Editar lancamento')
@section('heading', 'Editar lancamento financeiro')
@section('subheading', 'Revise valor, conta, categoria e status para manter o historico financeiro da conta consistente.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $movimentacao->descricao }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Conta atual: {{ $movimentacao->contaFinanceira?->nome ?? 'Sem conta' }}</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.lancamentos.update', $movimentacao) }}">
                @csrf
                @method('PUT')

                @include('admin.financeiro.lancamentos._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
