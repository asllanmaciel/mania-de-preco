<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use App\Models\ContaPagar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContaPagarController extends Controller
{
    use InterageComConta;

    public function index(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->contasPagar()
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
            'fornecedor_nome' => 'nullable|string|max:255',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'valor_pago' => 'nullable|numeric',
            'vencimento' => 'required|date',
            'pagamento_previsto_em' => 'nullable|date',
            'pago_em' => 'nullable|date',
            'status' => 'required|in:aberta,parcial,paga,vencida,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $contaPagar = $conta->contasPagar()->create([
            ...$data,
            'valor_pago' => $data['valor_pago'] ?? 0,
        ]);

        return response()->json($contaPagar->load(['loja', 'categoriaFinanceira']), 201);
    }

    public function show(Request $request, Conta $conta, ContaPagar $contas_pagar)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_pagar, $conta);

        return $contas_pagar->load(['loja', 'categoriaFinanceira']);
    }

    public function update(Request $request, Conta $conta, ContaPagar $contas_pagar)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_pagar, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $conta->id)],
            'fornecedor_nome' => 'nullable|string|max:255',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'valor_pago' => 'nullable|numeric',
            'vencimento' => 'required|date',
            'pagamento_previsto_em' => 'nullable|date',
            'pago_em' => 'nullable|date',
            'status' => 'required|in:aberta,parcial,paga,vencida,cancelada',
            'observacoes' => 'nullable|string',
        ]);

        $contas_pagar->update([
            ...$data,
            'valor_pago' => $data['valor_pago'] ?? $contas_pagar->valor_pago,
        ]);

        return response()->json($contas_pagar->load(['loja', 'categoriaFinanceira']), 200);
    }

    public function destroy(Request $request, Conta $conta, ContaPagar $contas_pagar)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_pagar, $conta);

        $contas_pagar->delete();

        return response()->json(null, 204);
    }
}
