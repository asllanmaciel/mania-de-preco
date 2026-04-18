<?php

namespace App\Http\Controllers\Web\Admin\Financeiro;

use App\Http\Controllers\Web\Admin\AdminController;
use App\Models\ContaFinanceira;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContaFinanceiraController extends AdminController
{
    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $busca = trim((string) $request->string('busca'));

        $contasFinanceiras = $conta->contasFinanceiras()
            ->with('loja')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery
                        ->where('nome', 'like', "%{$busca}%")
                        ->orWhere('tipo', 'like', "%{$busca}%")
                        ->orWhere('instituicao', 'like', "%{$busca}%");
                });
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.financeiro.contas.index', [
            'busca' => $busca,
            'contasFinanceiras' => $contasFinanceiras,
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.financeiro.contas.create', [
            'contaFinanceira' => new ContaFinanceira(),
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validarContaFinanceira($request, $conta->id);

        $conta->contasFinanceiras()->create([
            ...$dados,
            'saldo_inicial' => $dados['saldo_inicial'] ?? 0,
            'saldo_atual' => $dados['saldo_atual'] ?? ($dados['saldo_inicial'] ?? 0),
            'ativa' => array_key_exists('ativa', $dados) ? (bool) $dados['ativa'] : true,
        ]);

        return redirect()
            ->route('admin.financeiro.contas.index')
            ->with('status', 'Conta financeira cadastrada com sucesso.');
    }

    public function edit(Request $request, ContaFinanceira $conta): View
    {
        $contaAtual = $this->contaAtual($request);
        $this->garantirContaDaConta($conta, $contaAtual->id);

        return $this->responder($request, 'admin.financeiro.contas.edit', [
            'contaFinanceira' => $conta,
            'lojas' => $contaAtual->lojas()->orderBy('nome')->get(),
        ], $contaAtual);
    }

    public function update(Request $request, ContaFinanceira $conta): RedirectResponse
    {
        $contaAtual = $this->contaAtual($request);
        $this->garantirContaDaConta($conta, $contaAtual->id);

        $dados = $this->validarContaFinanceira($request, $contaAtual->id);

        $conta->update([
            ...$dados,
            'saldo_inicial' => $dados['saldo_inicial'] ?? $conta->saldo_inicial,
            'saldo_atual' => $dados['saldo_atual'] ?? $conta->saldo_atual,
            'ativa' => array_key_exists('ativa', $dados) ? (bool) $dados['ativa'] : false,
        ]);

        return redirect()
            ->route('admin.financeiro.contas.edit', $conta)
            ->with('status', 'Conta financeira atualizada com sucesso.');
    }

    public function destroy(Request $request, ContaFinanceira $conta): RedirectResponse
    {
        $contaAtual = $this->contaAtual($request);
        $this->garantirContaDaConta($conta, $contaAtual->id);

        $conta->delete();

        return redirect()
            ->route('admin.financeiro.contas.index')
            ->with('status', 'Conta financeira removida do painel.');
    }

    private function validarContaFinanceira(Request $request, int $contaId): array
    {
        return $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $contaId)],
            'nome' => ['required', 'string', 'max:255'],
            'tipo' => ['required', 'in:caixa,banco,cartao,carteira_digital'],
            'instituicao' => ['nullable', 'string', 'max:255'],
            'agencia' => ['nullable', 'string', 'max:50'],
            'numero' => ['nullable', 'string', 'max:50'],
            'saldo_inicial' => ['nullable', 'numeric'],
            'saldo_atual' => ['nullable', 'numeric'],
            'ativa' => ['nullable', 'boolean'],
        ]);
    }

    private function garantirContaDaConta(ContaFinanceira $contaFinanceira, int $contaId): void
    {
        abort_unless((int) $contaFinanceira->conta_id === $contaId, 404);
    }
}
