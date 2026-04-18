<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use App\Models\MovimentacaoFinanceira;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MovimentacaoFinanceiraController extends Controller
{
    use InterageComConta;

    public function index(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->movimentacoesFinanceiras()
            ->with(['loja', 'contaFinanceira', 'categoriaFinanceira', 'usuario'])
            ->orderByDesc('data_movimentacao')
            ->paginate(20);
    }

    public function store(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'conta_financeira_id' => ['required', Rule::exists('contas_financeiras', 'id')->where('conta_id', $conta->id)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $conta->id)],
            'tipo' => 'required|in:receita,despesa,transferencia',
            'origem' => 'required|in:manual,venda,pagamento,ajuste',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_movimentacao' => 'required|date',
            'status' => 'required|in:prevista,realizada,cancelada',
            'observacoes' => 'nullable|string',
            'metadados' => 'nullable|array',
        ]);

        $movimentacao = $conta->movimentacoesFinanceiras()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($movimentacao->load(['loja', 'contaFinanceira', 'categoriaFinanceira', 'usuario']), 201);
    }

    public function show(Request $request, Conta $conta, MovimentacaoFinanceira $movimentacoes_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($movimentacoes_financeira, $conta);

        return $movimentacoes_financeira->load(['loja', 'contaFinanceira', 'categoriaFinanceira', 'usuario']);
    }

    public function update(Request $request, Conta $conta, MovimentacaoFinanceira $movimentacoes_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($movimentacoes_financeira, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'conta_financeira_id' => ['required', Rule::exists('contas_financeiras', 'id')->where('conta_id', $conta->id)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $conta->id)],
            'tipo' => 'required|in:receita,despesa,transferencia',
            'origem' => 'required|in:manual,venda,pagamento,ajuste',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_movimentacao' => 'required|date',
            'status' => 'required|in:prevista,realizada,cancelada',
            'observacoes' => 'nullable|string',
            'metadados' => 'nullable|array',
        ]);

        $movimentacoes_financeira->update($data);

        return response()->json($movimentacoes_financeira->load(['loja', 'contaFinanceira', 'categoriaFinanceira', 'usuario']), 200);
    }

    public function destroy(Request $request, Conta $conta, MovimentacaoFinanceira $movimentacoes_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($movimentacoes_financeira, $conta);

        $movimentacoes_financeira->delete();

        return response()->json(null, 204);
    }
}
