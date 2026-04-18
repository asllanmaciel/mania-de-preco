@extends('layouts.admin')

@section('title', 'Nova categoria financeira')
@section('heading', 'Nova categoria financeira')
@section('subheading', 'Estruture um plano de categorias mais maduro para escalar o financeiro com padrao de mercado.')

@section('content')
    @include('admin.financeiro._nav')

    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Cadastro de categoria</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Use nomes objetivos e descricoes claras para orientar o time e melhorar a leitura dos indicadores.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.financeiro.categorias.store') }}">
                @csrf

                @include('admin.financeiro.categorias._form', ['submitLabel' => 'Cadastrar categoria'])
            </form>
        </div>
    </section>
@endsection
