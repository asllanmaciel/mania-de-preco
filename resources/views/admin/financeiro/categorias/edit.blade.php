@extends('layouts.admin')

@section('title', 'Editar categoria financeira')
@section('heading', 'Editar categoria financeira')
@section('subheading', 'Refine a classificacao financeira para manter o painel consistente conforme a operacao amadurece.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Ajuste de categoria</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Mudancas aqui afetam o entendimento de receitas, despesas, contas a pagar e contas a receber em toda a conta.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.categorias.update', $categoria) }}">
                @csrf
                @method('PUT')

                @include('admin.financeiro.categorias._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
