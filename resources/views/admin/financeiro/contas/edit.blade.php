@extends('layouts.admin')

@section('title', 'Editar conta financeira')
@section('heading', 'Editar conta financeira')
@section('subheading', 'Ajuste saldo, vinculacao e status da conta para manter o financeiro coerente com a operacao.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $contaFinanceira->nome }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Tipo atual: {{ ucfirst(str_replace('_', ' ', $contaFinanceira->tipo)) }}</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas.update', $contaFinanceira) }}">
                @csrf
                @method('PUT')

                @include('admin.financeiro.contas._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
