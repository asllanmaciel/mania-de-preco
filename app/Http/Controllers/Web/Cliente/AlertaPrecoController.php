<?php

namespace App\Http\Controllers\Web\Cliente;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\Produto;
use App\Services\Precos\AlertaPrecoEvaluator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AlertaPrecoController extends Controller
{
    public function store(Request $request, AlertaPrecoEvaluator $evaluator): RedirectResponse
    {
        $dados = $request->validate([
            'produto_id' => [
                'required',
                Rule::exists('produtos', 'id')->where('status', 'ativo'),
            ],
            'preco_desejado' => ['required', 'numeric', 'gt:0'],
        ]);

        $produto = Produto::findOrFail($dados['produto_id']);

        $alerta = AlertaPreco::query()->updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'produto_id' => $produto->id,
            ],
            [
                'preco_desejado' => $dados['preco_desejado'],
                'status' => 'ativo',
            ]
        );

        $evaluator->avaliar($alerta);

        return redirect()
            ->route('cliente.dashboard')
            ->with('status', 'Alerta criado. Vamos acompanhar esse produto e destacar quando o preco bater sua meta.');
    }

    public function update(Request $request, AlertaPreco $alerta, AlertaPrecoEvaluator $evaluator): RedirectResponse
    {
        abort_unless($alerta->user_id === $request->user()->id, 403);

        $dados = $request->validate([
            'preco_desejado' => ['required', 'numeric', 'gt:0'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);

        $alerta->update($dados);

        if ($alerta->status === 'ativo') {
            $evaluator->avaliar($alerta);
        } else {
            $alerta->forceFill(['ultima_avaliacao_em' => now()])->saveQuietly();
        }

        return redirect()
            ->route('cliente.dashboard')
            ->with('status', 'Alerta atualizado com sucesso.');
    }

    public function destroy(Request $request, AlertaPreco $alerta): RedirectResponse
    {
        abort_unless($alerta->user_id === $request->user()->id, 403);

        $alerta->delete();

        return redirect()
            ->route('cliente.dashboard')
            ->with('status', 'Alerta removido do seu painel.');
    }
}
