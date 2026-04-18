<?php

namespace App\Http\Controllers;

use App\Models\AvaliacaoLoja;
use Illuminate\Http\Request;

class AvaliacaoLojaController extends Controller
{
    public function index(Request $request)
    {
        return AvaliacaoLoja::with(['loja', 'user'])
            ->where('user_id', $request->user()->id)
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'loja_id' => 'required|exists:lojas,id',
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        $avaliacao = AvaliacaoLoja::updateOrCreate(
            [
                'loja_id' => $request->loja_id,
                'user_id' => $request->user()->id,
            ],
            [
                'nota' => $request->nota,
                'comentario' => $request->comentario,
            ]
        );

        return response()->json($avaliacao, 201);
    }

    public function show(Request $request, $id)
    {
        $avaliacao = AvaliacaoLoja::with(['loja', 'user'])->findOrFail($id);
        abort_unless($avaliacao->user_id === $request->user()->id, 403, 'Você não pode acessar esta avaliação.');

        return $avaliacao;
    }

    public function update(Request $request, $id)
    {
        $avaliacao = AvaliacaoLoja::findOrFail($id);
        abort_unless($avaliacao->user_id === $request->user()->id, 403, 'Você não pode editar esta avaliação.');

        $request->validate([
            'nota' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string',
        ]);

        $avaliacao->update($request->only(['nota', 'comentario']));

        return response()->json($avaliacao, 200);
    }

    public function destroy(Request $request, $id)
    {
        $avaliacao = AvaliacaoLoja::findOrFail($id);
        abort_unless($avaliacao->user_id === $request->user()->id, 403, 'Você não pode remover esta avaliação.');
        $avaliacao->delete();

        return response()->json(null, 204);
    }
}
