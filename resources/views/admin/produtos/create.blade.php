@extends('layouts.admin')

@section('title', 'Novo produto')
@section('heading', 'Novo produto')
@section('subheading', 'Cadastre um item no catalogo e deixe a base pronta para receber precos nas lojas.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Cadastro de produto</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Voce pode conectar o produto a categorias e marcas existentes ou criar novas durante o cadastro.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.produtos.store') }}">
                @csrf

                @include('admin.produtos._form', ['submitLabel' => 'Cadastrar produto'])
            </form>
        </div>
    </section>
@endsection
