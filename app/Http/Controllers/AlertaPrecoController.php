<?php

namespace App\Http\Controllers;

use App\Models\AlertaPreco;
use Illuminate\Http\Request;

class AlertaPrecoController extends Controller
{
    public function index()
    {
        return AlertaPreco::with(['produto', 'user'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'produto_id' => 'required|exists:produtos,id',
            'preco_desejado' => 'required|numeric',
            'status' => 'in:ativo,atendido,inativo',
        ]);

        $alerta = AlertaPreco::create($request->all());

        return response()->json($alerta, 201);
    }

    public function show($id)
    {
        return AlertaPreco::with(['produto', 'user'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $alerta = AlertaPreco::findOrFail($id);

        $request->validate([
            'preco_desejado' => 'required|numeric',
            'status' => 'in:ativo,atendido,inativo',
        ]);

        $alerta->update($request->all());

        return response()->json($alerta, 200);
    }

    public function destroy($id)
    {
        $alerta = AlertaPreco::findOrFail($id);
        $alerta->delete();

        return response()->json(null, 204);
    }
}
