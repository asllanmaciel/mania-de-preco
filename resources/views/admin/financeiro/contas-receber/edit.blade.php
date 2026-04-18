@extends('layouts.admin')

@section('title', 'Editar conta a receber')
@section('heading', 'Editar conta a receber')
@section('subheading', 'Atualize valores, vencimento e status do titulo para manter a previsao financeira da conta confiavel.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $titulo->descricao }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Cliente atual: {{ $titulo->cliente_nome ?: 'Nao informado' }}</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.contas-receber.update', $titulo) }}">
                @csrf
                @method('PUT')

                @include('admin.financeiro.contas-receber._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
