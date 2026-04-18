<?php

namespace App\Http\Controllers\Web\Admin\Financeiro;

use App\Http\Controllers\Web\Admin\AdminController;
use App\Models\MovimentacaoFinanceira;
use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MovimentacaoFinanceiraController extends AdminController
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $tipo = (string) $request->string('tipo');

        $movimentacoes = $conta->movimentacoesFinanceiras()
            ->with(['loja', 'contaFinanceira', 'categoriaFinanceira'])
            ->when(in_array($tipo, ['receita', 'despesa', 'transferencia'], true), fn ($query) => $query->where('tipo', $tipo))
            ->latest('data_movimentacao')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.financeiro.lancamentos.index', [
            'tipoSelecionado' => $tipo,
            'movimentacoes' => $movimentacoes,
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.financeiro.lancamentos.create', [
            'movimentacao' => new MovimentacaoFinanceira(),
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'contasFinanceiras' => $conta->contasFinanceiras()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validarMovimentacao($request, $conta->id);

        $movimentacao = $conta->movimentacoesFinanceiras()->create([
            ...$dados,
            'user_id' => $request->user()->id,
        ]);

        $this->audit->registrar($request, $conta, 'financeiro', 'lancamento_criado', "Lancamento {$movimentacao->descricao} criado.", $movimentacao, [
            'tipo' => $movimentacao->tipo,
            'valor' => $movimentacao->valor,
            'status' => $movimentacao->status,
        ]);

        return redirect()
            ->route('admin.financeiro.lancamentos.index')
            ->with('status', 'Lancamento financeiro cadastrado com sucesso.');
    }

    public function edit(Request $request, MovimentacaoFinanceira $lancamento): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirLancamentoDaConta($lancamento, $conta->id);

        return $this->responder($request, 'admin.financeiro.lancamentos.edit', [
            'movimentacao' => $lancamento,
            'lojas' => $conta->lojas()->orderBy('nome')->get(),
            'contasFinanceiras' => $conta->contasFinanceiras()->orderBy('nome')->get(),
            'categorias' => $conta->categoriasFinanceiras()->orderBy('nome')->get(),
        ], $conta);
    }

    public function update(Request $request, MovimentacaoFinanceira $lancamento): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirLancamentoDaConta($lancamento, $conta->id);

        $dados = $this->validarMovimentacao($request, $conta->id);
        $antes = $lancamento->only(['descricao', 'tipo', 'valor', 'status', 'data_movimentacao']);
        $lancamento->update($dados);

        $this->audit->registrar($request, $conta, 'financeiro', 'lancamento_atualizado', "Lancamento {$lancamento->descricao} atualizado.", $lancamento, [
            'antes' => $antes,
            'depois' => $lancamento->only(['descricao', 'tipo', 'valor', 'status', 'data_movimentacao']),
        ]);

        return redirect()
            ->route('admin.financeiro.lancamentos.edit', $lancamento)
            ->with('status', 'Lancamento financeiro atualizado com sucesso.');
    }

    public function destroy(Request $request, MovimentacaoFinanceira $lancamento): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirLancamentoDaConta($lancamento, $conta->id);

        $descricao = $lancamento->descricao;
        $this->audit->registrar($request, $conta, 'financeiro', 'lancamento_removido', "Lancamento {$descricao} removido.", $lancamento);
        $lancamento->delete();

        return redirect()
            ->route('admin.financeiro.lancamentos.index')
            ->with('status', 'Lancamento removido do financeiro.');
    }

    private function validarMovimentacao(Request $request, int $contaId): array
    {
        return $request->validate([
            'loja_id' => ['nullable', Rule::exists('lojas', 'id')->where('conta_id', $contaId)],
            'conta_financeira_id' => ['required', Rule::exists('contas_financeiras', 'id')->where('conta_id', $contaId)],
            'categoria_financeira_id' => ['nullable', Rule::exists('categorias_financeiras', 'id')->where('conta_id', $contaId)],
            'tipo' => ['required', 'in:receita,despesa,transferencia'],
            'origem' => ['required', 'in:manual,venda,pagamento,ajuste'],
            'descricao' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'data_movimentacao' => ['required', 'date'],
            'status' => ['required', 'in:prevista,realizada,cancelada'],
            'observacoes' => ['nullable', 'string'],
        ]);
    }

    private function garantirLancamentoDaConta(MovimentacaoFinanceira $lancamento, int $contaId): void
    {
        abort_unless((int) $lancamento->conta_id === $contaId, 404);
    }
}
