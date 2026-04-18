@extends('layouts.admin')

@section('title', 'Nova loja')
@section('heading', 'Nova loja')
@section('subheading', 'Cadastre uma unidade comercial para estruturar a conta e preparar a publicacao de precos.')

@section('content')
    <section class="card">
        <div class="card-body stack">
            <div>
                <h2 style="margin: 0;">Dados da loja</h2>
                <p class="helper-text" style="margin: 8px 0 0;">Comece pelo basico e complemente depois. A conta ativa ja sera vinculada automaticamente.</p>
            </div>

            <form class="stack" method="POST" action="{{ route('admin.lojas.store') }}">
                @csrf

                @include('admin.lojas._form', ['submitLabel' => 'Cadastrar loja'])
            </form>
        </div>
    </section>
@endsection
