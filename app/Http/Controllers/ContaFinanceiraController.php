<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use App\Models\ContaFinanceira;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContaFinanceiraController extends Controller
{
    use InterageComConta;

    public function index(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->contasFinanceiras()->with('loja')->orderBy('nome')->get();
    }

    public function store(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:caixa,banco,cartao,carteira_digital',
            'instituicao' => 'nullable|string|max:255',
            'agencia' => 'nullable|string|max:50',
            'numero' => 'nullable|string|max:50',
            'saldo_inicial' => 'nullable|numeric',
            'saldo_atual' => 'nullable|numeric',
            'ativa' => 'boolean',
        ]);

        $contaFinanceira = $conta->contasFinanceiras()->create([
            ...$data,
            'saldo_inicial' => $data['saldo_inicial'] ?? 0,
            'saldo_atual' => $data['saldo_atual'] ?? ($data['saldo_inicial'] ?? 0),
            'ativa' => $data['ativa'] ?? true,
        ]);

        return response()->json($contaFinanceira->load('loja'), 201);
    }

    public function show(Request $request, Conta $conta, ContaFinanceira $contas_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_financeira, $conta);

        return $contas_financeira->load('loja');
    }

    public function update(Request $request, Conta $conta, ContaFinanceira $contas_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_financeira, $conta);

        $data = $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $conta->id)],
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:caixa,banco,cartao,carteira_digital',
            'instituicao' => 'nullable|string|max:255',
            'agencia' => 'nullable|string|max:50',
            'numero' => 'nullable|string|max:50',
            'saldo_inicial' => 'nullable|numeric',
            'saldo_atual' => 'nullable|numeric',
            'ativa' => 'boolean',
        ]);

        $contas_financeira->update([
            ...$data,
            'saldo_inicial' => $data['saldo_inicial'] ?? $contas_financeira->saldo_inicial,
            'saldo_atual' => $data['saldo_atual'] ?? $contas_financeira->saldo_atual,
            'ativa' => $data['ativa'] ?? $contas_financeira->ativa,
        ]);

        return response()->json($contas_financeira->load('loja'), 200);
    }

    public function destroy(Request $request, Conta $conta, ContaFinanceira $contas_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($contas_financeira, $conta);

        $contas_financeira->delete();

        return response()->json(null, 204);
    }
}
