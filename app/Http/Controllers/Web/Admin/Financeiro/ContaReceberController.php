<?php

namespace App\Http\Controllers\Web\Admin\Financeiro;

use App\Http\Controllers\Web\Admin\AdminController;
use App\Models\ContaReceber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContaReceberController extends AdminController
{
    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $status = (string) $request->string('status');

        $titulos = $conta->contasReceber()
            ->with(['loja', 'categoriaFinanceira'])
            ->when(in_array($status, ['aberta', 'parcial', 'recebida', 'vencida', 'cancelada'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('vencimento')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.financeiro.contas-receber.index', [
            'statusSelecionado' => $status,
            'titulos' => $titulos,
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.financeiro.contas-receber.create', [
            'titulo' => new ContaReceber(),
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validar($request, $conta->id);

        $conta->contasReceber()->create([
            ...$dados,
            'valor_recebido' => $dados['valor_recebido'] ?? 0,
        ]);

        return redirect()
            ->route('admin.financeiro.contas-receber.index')
            ->with('status', 'Conta a receber cadastrada com sucesso.');
    }

    public function edit(Request $request, ContaReceber $contas_receber): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_receber, $conta->id);

        return $this->responder($request, 'admin.financeiro.contas-receber.edit', [
            'titulo' => $contas_receber,
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function update(Request $request, ContaReceber $contas_receber): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_receber, $conta->id);

        $dados = $this->validar($request, $conta->id);
        $contas_receber->update([
            ...$dados,
            'valor_recebido' => $dados['valor_recebido'] ?? $contas_receber->valor_recebido,
        ]);

        return redirect()
            ->route('admin.financeiro.contas-receber.edit', $contas_receber)
            ->with('status', 'Conta a receber atualizada com sucesso.');
    }

    public function destroy(Request $request, ContaReceber $contas_receber): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirTituloDaConta($contas_receber, $conta->id);

        $contas_receber->delete();

        return redirect()
            ->route('admin.financeiro.contas-receber.index')
            ->with('status', 'Conta a receber removida do painel.');
    }

    private function validar(Request $request, int $contaId): array
    {
        return $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $contaId)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $contaId)],
            'cliente_nome' => ['nullable', 'string', 'max:255'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor_total' => ['required', 'numeric', 'min:0'],
            'valor_recebido' => ['nullable', 'numeric', 'min:0'],
            'vencimento' => ['required', 'date'],
            'recebimento_previsto_em' => ['nullable', 'date'],
            'recebido_em' => ['nullable', 'date'],
            'status' => ['required', 'in:aberta,parcial,recebida,vencida,cancelada'],
            'observacoes' => ['nullable', 'string'],
        ]);
    }

    private function garantirTituloDaConta(ContaReceber $titulo, int $contaId): void
    {
        abort_unless((int) $titulo->conta_id === $contaId, 404);
    }
}
