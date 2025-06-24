<?php

namespace App\Http\Controllers;

use App\Models\AvaliacaoLoja;
use Illuminate\Http\Request;

class AvaliacaoLojaController extends Controller
{
    public function index()
    {
        return AvaliacaoLoja::with(['loja', 'user'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'loja_id' => 'required|exists:lojas,id',
            'user_id' => 'required|exists:users,id',
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        $avaliacao = AvaliacaoLoja::create($request->all());

        return response()->json($avaliacao, 201);
    }

    public function show($id)
    {
        return AvaliacaoLoja::with(['loja', 'user'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $avaliacao = AvaliacaoLoja::findOrFail($id);

        $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        $avaliacao->update($request->all());

        return response()->json($avaliacao, 200);
    }

    public function destroy($id)
    {
        $avaliacao = AvaliacaoLoja::findOrFail($id);
        $avaliacao->delete();

        return response()->json(null, 204);
    }
}
