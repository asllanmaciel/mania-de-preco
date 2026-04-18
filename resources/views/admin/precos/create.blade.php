@extends('layouts.admin')

@section('title', 'Novo preco')
@section('heading', 'Novo preco')
@section('subheading', 'Relacione um produto a uma loja e publique o valor praticado pela conta.')

@section('content')
    @if ($lojasDaConta->isEmpty())
        <section class="card">
            <div class="card-body">
                <div class="empty-state">
                    Antes de cadastrar um preco, voce precisa ter ao menos uma loja na conta.
                    <br><br>
                    <a class="button" href="{{ route('admin.lojas.create') }}">Cadastrar loja</a>
                </div>
            </div>
        </section>
    @elseif ($produtos->isEmpty())
        <section class="card">
            <div class="card-body">
                <div class="empty-state">
                    Antes de cadastrar um preco, voce precisa criar ao menos um produto no catalogo.
                    <br><br>
                    <a class="button" href="{{ route('admin.produtos.create') }}">Cadastrar produto</a>
                </div>
            </div>
        </section>
    @else
        <section class="card">
            <div class="card-body stack">
                <div>
                    <h2 style="margin: 0;">Cadastro de preco</h2>
                    <p class="helper-text" style="margin: 8px 0 0;">Esse registro ja prepara a exibicao do item no comparador publico do Mania de Preco.</p>
                </div>

                <form class="stack" method="POST" action="{{ route('admin.precos.store') }}">
                    @csrf

                    @include('admin.precos._form', ['submitLabel' => 'Cadastrar preco'])
                </form>
            </div>
        </section>
    @endif
@endsection
