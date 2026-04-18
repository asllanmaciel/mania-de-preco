<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Loja;
use App\Models\Preco;
use App\Models\Produto;
use App\Services\Auditoria\AuditLogger;
use App\Support\Billing\ContaUsageMeter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PrecoController extends AdminController
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly ContaUsageMeter $usageMeter
    )
    {
    }

    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $lojaId = $request->integer('loja_id');

        $lojasDaConta = $conta->lojas()->orderBy('nome')->get();

        $precos = Preco::query()
            ->whereIn('loja_id', $lojasDaConta->pluck('id'))
            ->with(['produto.categoria', 'loja'])
            ->when($lojaId > 0, fn ($query) => $query->where('loja_id', $lojaId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.precos.index', [
            'lojaIdSelecionada' => $lojaId,
            'lojasDaConta' => $lojasDaConta,
            'precos' => $precos,
            'usoPlano' => $this->usageMeter->resumo($conta),
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.precos.create', [
            'preco' => new Preco(),
            'produtos' => Produto::with(['categoria', 'marca'])->orderBy('nome')->get(),
            'lojasDaConta' => $conta->lojas()->orderBy('nome')->get(),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validarPreco($request, $conta);

        if (! $this->usageMeter->podeVincularProduto($conta, (int) $dados['produto_id'])) {
            return redirect()
                ->route('admin.precos.index')
                ->with('status', $this->usageMeter->mensagemBloqueio($conta, 'produtos'));
        }

        $preco = Preco::create($dados);

        $this->audit->registrar($request, $conta, 'precos', 'preco_criado', 'Preco registrado no comparador.', $preco, [
            'produto_id' => $preco->produto_id,
            'loja_id' => $preco->loja_id,
            'preco' => $preco->preco,
            'tipo_preco' => $preco->tipo_preco,
        ]);

        return redirect()
            ->route('admin.precos.index')
            ->with('status', 'Preco registrado com sucesso para a loja selecionada.');
    }

    public function edit(Request $request, Preco $preco): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirPrecoDaConta($preco, $conta);

        return $this->responder($request, 'admin.precos.edit', [
            'preco' => $preco->load(['produto', 'loja']),
            'produtos' => Produto::with(['categoria', 'marca'])->orderBy('nome')->get(),
            'lojasDaConta' => $conta->lojas()->orderBy('nome')->get(),
        ], $conta);
    }

    public function update(Request $request, Preco $preco): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirPrecoDaConta($preco, $conta);

        $dados = $this->validarPreco($request, $conta);

        if (! $this->usageMeter->podeVincularProduto($conta, (int) $dados['produto_id'], $preco)) {
            return redirect()
                ->route('admin.precos.edit', $preco)
                ->with('status', $this->usageMeter->mensagemBloqueio($conta, 'produtos'));
        }

        $antes = $preco->only(['produto_id', 'loja_id', 'preco', 'tipo_preco']);
        $preco->update($dados);

        $this->audit->registrar($request, $conta, 'precos', 'preco_atualizado', 'Preco atualizado no comparador.', $preco, [
            'antes' => $antes,
            'depois' => $preco->only(['produto_id', 'loja_id', 'preco', 'tipo_preco']),
        ]);

        return redirect()
            ->route('admin.precos.edit', $preco)
            ->with('status', 'Preco atualizado com sucesso.');
    }

    public function destroy(Request $request, Preco $preco): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirPrecoDaConta($preco, $conta);

        $this->audit->registrar($request, $conta, 'precos', 'preco_removido', 'Preco removido do comparador.', $preco, [
            'produto_id' => $preco->produto_id,
            'loja_id' => $preco->loja_id,
            'preco' => $preco->preco,
        ]);
        $preco->delete();

        return redirect()
            ->route('admin.precos.index')
            ->with('status', 'Preco removido do comparador.');
    }

    private function validarPreco(Request $request, $conta): array
    {
        return $request->validate([
            'produto_id' => ['required', 'exists:produtos,id'],
            'loja_id' => [
                'required',
                Rule::exists('lojas', 'id')->where(fn ($query) => $query->where('conta_id', $conta->id)),
            ],
            'preco' => ['required', 'numeric', 'min:0'],
            'tipo_preco' => ['required', Rule::in(['dinheiro', 'pix', 'boleto', 'cartao', 'parcelado'])],
            'url_produto' => ['nullable', 'url', 'max:255'],
        ]);
    }

    private function garantirPrecoDaConta(Preco $preco, $conta): void
    {
        $preco->loadMissing('loja');

        abort_unless((int) $preco->loja->conta_id === (int) $conta->id, 404);
    }
}
