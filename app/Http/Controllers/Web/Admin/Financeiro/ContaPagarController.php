<?php

namespace App\Http\Controllers\Web\Admin\Financeiro;

use App\Http\Controllers\Web\Admin\AdminController;
use App\Models\ContaPagar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContaPagarController extends AdminController
{
    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $status = (string) $request->string('status');

        $titulos = $conta->contasPagar()
            ->with(['loja', 'categoriaFinanceira'])
            ->when(in_array($status, ['aberta', 'parcial', 'paga', 'vencida', 'cancelada'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('vencimento')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.financeiro.contas-pagar.index', [
            'statusSelecionado' => $status,
            'titulos' => $titulos,
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.financeiro.contas-pagar.create', [
            'titulo' => new ContaPagar(),
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validar($request, $conta->id);

        $conta->contasPagar()->create([
            ...$dados,
            'valor_pago' => $dados['valor_pago'] ?? 0,
        ]);

        return redirect()
            ->route('admin.financeiro.contas-pagar.index')
            ->with('status', 'Conta a pagar cadastrada com sucesso.');
    }

    public function edit(Request $request, ContaPagar $contas_pagar): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_pagar, $conta->id);

        return $this->responder($request, 'admin.financeiro.contas-pagar.edit', [
            'titulo' => $contas_pagar,
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function update(Request $request, ContaPagar $contas_pagar): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_pagar, $conta->id);

        $dados = $this->validar($request, $conta->id);
        $contas_pagar->update([
            ...$dados,
            'valor_pago' => $dados['valor_pago'] ?? $contas_pagar->valor_pago,
        ]);

        return redirect()
            ->route('admin.financeiro.contas-pagar.edit', $contas_pagar)
            ->with('status', 'Conta a pagar atualizada com sucesso.');
    }

    public function destroy(Request $request, ContaPagar $contas_pagar): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_pagar, $conta->id);

        $contas_pagar->delete();

        return redirect()
            ->route('admin.financeiro.contas-pagar.index')
            ->with('status', 'Conta a pagar removida do painel.');
    }

    private function validar(Request $request, int $contaId): array
    {
        return $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $contaId)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $contaId)],
            'fornecedor_nome' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor_total' => ['required', 'numeric', 'min:0'],
            'valor_pago' => ['nullable', 'numeric', 'min:0'],
            'vencimento' => ['required', 'date'],
            'pagamento_previsto_em' => ['nullable', 'date'],
            'pago_em' => ['nullable', 'date'],
            'status' => ['required', 'in:aberta,parcial,paga,vencida,cancelada'],
            'observacoes' => ['nullable', 'string'],
        ]);
    }

    private function garantirTituloDaConta(ContaPagar $titulo, int $contaId): void
    {
        abort_unless((int) $titulo->conta_id === $contaId, 404);
    }
}
