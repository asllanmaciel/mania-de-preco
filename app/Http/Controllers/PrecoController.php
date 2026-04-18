<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Loja;
use App\Models\Preco;
use Illuminate\Http\Request;

class PrecoController extends Controller
{
    use InterageComConta;

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

        $loja = Loja::findOrFail($request->loja_id);
        $this->garantirAcessoConta($request, $loja->conta);

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
        $this->garantirAcessoConta($request, $preco->loja->conta);

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'loja_id' => 'required|exists:lojas,id',
            'preco' => 'required|numeric',
            'tipo_preco' => 'in:dinheiro,pix,boleto,cartao,parcelado',
            'url_produto' => 'nullable|url',
        ]);

        if ((int) $request->loja_id !== (int) $preco->loja_id) {
            $lojaDestino = Loja::findOrFail($request->loja_id);
            $this->garantirAcessoConta($request, $lojaDestino->conta);
        }

        $preco->update($request->all());

        return response()->json($preco, 200);
    }

    public function destroy(Request $request, $id)
    {
        $preco = Preco::findOrFail($id);
        $this->garantirAcessoConta($request, $preco->loja->conta);
        $preco->delete();

        return response()->json(null, 204);
    }
}
