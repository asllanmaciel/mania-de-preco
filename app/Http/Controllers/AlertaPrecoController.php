<?php

namespace App\Http\Controllers;

use App\Models\AlertaPreco;
use App\Services\Precos\AlertaPrecoEvaluator;
use Illuminate\Http\Request;

class AlertaPrecoController extends Controller
{
    public function index(Request $request)
    {
        return AlertaPreco::with(['produto', 'user', 'lojaReferencia'])
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->get();
    }

    public function store(Request $request, AlertaPrecoEvaluator $evaluator)
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

        return response()->json($evaluator->avaliar($alerta), 201);
    }

    public function show(Request $request, $id)
    {
        $alerta = AlertaPreco::with(['produto', 'user', 'lojaReferencia'])->findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Voce nao pode acessar este alerta.');

        return $alerta;
    }

    public function update(Request $request, $id, AlertaPrecoEvaluator $evaluator)
    {
        $alerta = AlertaPreco::findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Voce nao pode editar este alerta.');

        $request->validate([
            'preco_desejado' => 'required|numeric',
            'status' => 'required|in:ativo,atendido,inativo',
        ]);

        $alerta->update($request->only(['preco_desejado', 'status']));

        if ($alerta->status !== 'inativo') {
            $alerta = $evaluator->avaliar($alerta);
        } else {
            $alerta->forceFill([
                'ultima_avaliacao_em' => now(),
            ])->saveQuietly();
            $alerta->load(['produto', 'user', 'lojaReferencia']);
        }

        return response()->json($alerta, 200);
    }

    public function destroy(Request $request, $id)
    {
        $alerta = AlertaPreco::findOrFail($id);
        abort_unless($alerta->user_id === $request->user()->id, 403, 'Voce nao pode remover este alerta.');
        $alerta->delete();

        return response()->json(null, 204);
    }
}
