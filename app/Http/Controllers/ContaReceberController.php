<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use App\Models\ContaReceber;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContaReceberController extends Controller
{
    use InterageComConta;

    public function index(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->contasReceber()
            ->with(['loja', 'categoriaFinanceira'])
            ->orderBy('vencimento')
            ->paginate(20);
    }

    public function store(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $conta->id)],
            'cliente_nome' => 'nullable|string|max:255',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'valor_recebido' => 'nullable|numeric',
            'vencimento' => 'required|date',
            'recebimento_previsto_em' => 'nullable|date',
            'recebido_em' => 'nullable|date',
            'status' => 'required|in:aberta,parcial,recebida,vencida,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $contaReceber = $conta->contasReceber()->create([
            ...$data,
            'valor_recebido' => $data['valor_recebido'] ?? 0,
        ]);

        return response()->json($contaReceber->load(['loja', 'categoriaFinanceira']), 201);
    }

    public function show(Request $request, Conta $conta, ContaReceber $contas_receber)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_receber, $conta);

        return $contas_receber->load(['loja', 'categoriaFinanceira']);
    }

    public function update(Request $request, Conta $conta, ContaReceber $contas_receber)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_receber, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $conta->id)],
            'cliente_nome' => 'nullable|string|max:255',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'valor_recebido' => 'nullable|numeric',
            'vencimento' => 'required|date',
            'recebimento_previsto_em' => 'nullable|date',
            'recebido_em' => 'nullable|date',
            'status' => 'required|in:aberta,parcial,recebida,vencida,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $contas_receber->update([
            ...$data,
            'valor_recebido' => $data['valor_recebido'] ?? $contas_receber->valor_recebido,
        ]);

        return response()->json($contas_receber->load(['loja', 'categoriaFinanceira']), 200);
    }

    public function destroy(Request $request, Conta $conta, ContaReceber $contas_receber)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_receber, $conta);

        $contas_receber->delete();

        return response()->json(null, 204);
    }
}
