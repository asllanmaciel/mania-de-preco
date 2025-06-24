<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use Illuminate\Http\Request;

class LojaController extends Controller
{
    public function index()
    {
        return Loja::with(['precos', 'avaliacoes', 'plano'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|unique:lojas',
            'telefone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'site' => 'nullable|url',
            'instagram' => 'nullable|string',
            'facebook' => 'nullable|string',
            'endereco' => 'nullable|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'uf' => 'nullable|string|max:2',
            'cep' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tipo_loja' => 'in:fisica,online,mista',
            'status' => 'in:ativo,inativo',
            'logo' => 'nullable|string',
        ]);

        $loja = Loja::create($request->all());

        return response()->json($loja, 201);
    }

    public function show($id)
    {
        return Loja::with(['precos', 'avaliacoes', 'plano'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $loja = Loja::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|unique:lojas,cnpj,' . $id,
            'telefone' => 'nullable|string',
            'whatsapp' => 'nullable|string',
            'email' => 'nullable|email',
            'site' => 'nullable|url',
            'instagram' => 'nullable|string',
            'facebook' => 'nullable|string',
            'endereco' => 'nullable|string',
            'numero' => 'nullable|string',
            'bairro' => 'nullable|string',
            'cidade' => 'nullable|string',
            'uf' => 'nullable|string|max:2',
            'cep' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tipo_loja' => 'in:fisica,online,mista',
            'status' => 'in:ativo,inativo',
            'logo' => 'nullable|string',
        ]);

        $loja->update($request->all());

        return response()->json($loja, 200);
    }

    public function destroy($id)
    {
        $loja = Loja::findOrFail($id);
        $loja->delete();

        return response()->json(null, 204);
    }
}
