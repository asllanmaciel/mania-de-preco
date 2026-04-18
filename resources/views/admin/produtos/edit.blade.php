@extends('layouts.admin')

@section('title', 'Editar produto')
@section('heading', 'Editar produto')
@section('subheading', 'Ajuste a base do catalogo para manter a descricao comercial e a comparacao de precos alinhadas.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">{{ $produto->nome }}</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Slug atual: <code>{{ $produto->slug }}</code></p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.produtos.update', $produto) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.produtos._form', ['submitLabel' => 'Salvar alteracoes'])
            </form>
        </div>
    </section>
@endsection
