@extends('layouts.admin')

@section('title', 'Editar preco')
@section('heading', 'Editar preco')
@section('subheading', 'Ajuste valor, loja ou tipo de pagamento para manter o comparador e a operacao alinhados.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $preco->produto->nome }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Loja atual: {{ $preco->loja->nome }}</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.precos.update', $preco) }}">
                @csrf
                @method('PUT')

                @include('admin.precos._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
