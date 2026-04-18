@extends('super-admin.assinaturas.form')

@section('title', 'Nova assinatura')
@section('heading', 'Nova assinatura da conta')
@section('subheading', 'Defina o plano atual da conta e organize a esteira comercial com historico limpo.')
@section('form_action', route('super-admin.contas.assinaturas.store', $conta))
@section('form_method')
@endsection
