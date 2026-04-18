@extends('layouts.admin')

@section('title', 'Editar loja')
@section('heading', 'Editar loja')
@section('subheading', 'Atualize os dados comerciais da loja para manter a conta e o comparador coerentes.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $loja->nome }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Use essa tela para revisar contato, localizacao e status operacional da loja.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.lojas.update', $loja) }}">
                @csrf
                @method('PUT')

                @include('admin.lojas._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
