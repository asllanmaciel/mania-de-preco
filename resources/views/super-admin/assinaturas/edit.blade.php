@extends('super-admin.assinaturas.form')

@section('title', 'Editar assinatura')
@section('heading', 'Editar assinatura')
@section('subheading', 'Ajuste status, ciclo, vigencia e provedor da assinatura sem perder o contexto da conta.')
@section('form_action', route('super-admin.contas.assinaturas.update', [$conta, $assinatura]))
@section('form_method')
    @method('PUT')
@endsection
