<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use App\Models\Loja;
use Illuminate\Http\Request;

class LojaController extends Controller
{
    use InterageComConta;

    public function index()
    {
        return Loja::with(['precos', 'avaliacoes', 'plano', 'conta'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'conta_id' => 'required|exists:contas,id',
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

        $conta = Conta::findOrFail($request->conta_id);
        $this->garantirAcessoConta($request, $conta);

        $loja = Loja::create($request->all());

        return response()->json($loja, 201);
    }

    public function show($id)
    {
        return Loja::with(['precos', 'avaliacoes', 'plano', 'conta'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $loja = Loja::findOrFail($id);
        $this->garantirAcessoConta($request, $loja->conta);

        $request->validate([
            'conta_id' => 'sometimes|exists:contas,id',
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

        if ($request->filled('conta_id') && (int) $request->conta_id !== (int) $loja->conta_id) {
            $contaDestino = Conta::findOrFail($request->conta_id);
            $this->garantirAcessoConta($request, $contaDestino);
        }

        $loja->update($request->all());

        return response()->json($loja, 200);
    }

    public function destroy(Request $request, $id)
    {
        $loja = Loja::findOrFail($id);
        $this->garantirAcessoConta($request, $loja->conta);
        $loja->delete();

        return response()->json(null, 204);
    }
}
