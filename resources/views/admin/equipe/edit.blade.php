@extends('admin.equipe.form')

@section('title', 'Editar membro')
@section('heading', 'Editar membro da equipe')
@section('subheading', 'Ajuste permissoes, status e dados do acesso sem perder o historico operacional da conta.')
@section('form_action', route('admin.equipe.update', $membro))
@section('form_method')
    @method('PUT')
@endsection
