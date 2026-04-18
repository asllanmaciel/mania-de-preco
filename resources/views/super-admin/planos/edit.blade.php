@extends('super-admin.planos.form')

@section('title', 'Editar plano')
@section('heading', 'Editar plano')
@section('subheading', 'Ajuste precificacao, limites e proposta de valor sem perder a coerencia comercial da plataforma.')
@section('form_action', route('super-admin.planos.update', $plano))
@section('form_method')
    @method('PUT')
@endsection
