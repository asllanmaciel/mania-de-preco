<?php

namespace App\Http\Controllers;

use App\Models\Preco;
use Illuminate\Http\Request;

class PrecoController extends Controller
{
    public function index()
    {
        return Preco::with(['produto', 'loja'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'loja_id' => 'required|exists:lojas,id',
            'preco' => 'required|numeric',
            'tipo_preco' => 'in:dinheiro,pix,boleto,cartao,parcelado',
            'url_produto' => 'nullable|url',
        ]);

        $preco = Preco::create($request->all());

        return response()->json($preco, 201);
    }

    public function show($id)
    {
        return Preco::with(['produto', 'loja'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $preco = Preco::findOrFail($id);

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'loja_id' => 'required|exists:lojas,id',
            'preco' => 'required|numeric',
            'tipo_preco' => 'in:dinheiro,pix,boleto,cartao,parcelado',
            'url_produto' => 'nullable|url',
        ]);

        $preco->update($request->all());

        return response()->json($preco, 200);
    }

    public function destroy($id)
    {
        $preco = Preco::findOrFail($id);
        $preco->delete();

        return response()->json(null, 204);
    }
}
