@extends('layouts.admin')

@section('title', 'Editar conta a pagar')
@section('heading', 'Editar conta a pagar')
@section('subheading', 'Atualize valores, vencimento e status do titulo para manter o fluxo financeiro confiavel.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $titulo->descricao }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Fornecedor atual: {{ $titulo->fornecedor_nome ?: 'Nao informado' }}</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas-pagar.update', $titulo) }}">
                @csrf
                @method('PUT')

                @include('admin.financeiro.contas-pagar._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
