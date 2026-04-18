<?php

namespace App\Http\Controllers;

use App\Models\AlertaPreco;
use Illuminate\Http\Request;

class AlertaPrecoController extends Controller
{
    public function index(Request $request)
    {
        return AlertaPreco::with(['produto', 'user'])
            ->where('user_id', $request->user()->id)
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'preco_desejado' => 'required|numeric',
            'status' => 'nullable|in:ativo,atendido,inativo',
        ]);

        $alerta = AlertaPreco::create([
            'user_id' => $request->user()->id,
            'produto_id' => $request->produto_id,
            'preco_desejado' => $request->preco_desejado,
            'status' => $request->status ?? 'ativo',
        ]);

        return response()->json($alerta, 201);
    }

    public function show(Request $request, $id)
    {
        $alerta = AlertaPreco::with(['produto', 'user'])->findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Você não pode acessar este alerta.');

        return $alerta;
    }

    public function update(Request $request, $id)
    {
        $alerta = AlertaPreco::findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Você não pode editar este alerta.');

        $request->validate([
            'preco_desejado' => 'required|numeric',
            'status' => 'required|in:ativo,atendido,inativo',
        ]);

        $alerta->update($request->only(['preco_desejado', 'status']));

        return response()->json($alerta, 200);
    }

    public function destroy(Request $request, $id)
    {
        $alerta = AlertaPreco::findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Você não pode remover este alerta.');
        $alerta->delete();

        return response()->json(null, 204);
    }
}
