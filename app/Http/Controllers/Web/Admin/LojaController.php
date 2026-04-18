<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Loja;
use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LojaController extends AdminController
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $busca = trim((string) $request->string('busca'));

        $lojas = $conta->lojas()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery
                        ->where('nome', 'like', "%{$busca}%")
                        ->orWhere('cidade', 'like', "%{$busca}%")
                        ->orWhere('uf', 'like', "%{$busca}%")
                        ->orWhere('tipo_loja', 'like', "%{$busca}%");
                });
            })
            ->withCount(['precos', 'avaliacoes'])
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.lojas.index', [
            'busca' => $busca,
            'lojas' => $lojas,
        ], $conta);
    }

    public function create(Request $request): View
    {
        return $this->responder($request, 'admin.lojas.create', [
            'loja' => new Loja(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validarLoja($request);
        $dados['conta_id'] = $conta->id;
        $dados['uf'] = $dados['uf'] ? strtoupper($dados['uf']) : null;

        $loja = Loja::create($dados);

        $this->audit->registrar($request, $conta, 'lojas', 'loja_criada', "Loja {$loja->nome} cadastrada.", $loja);

        return redirect()
            ->route('admin.lojas.index')
            ->with('status', 'Loja cadastrada com sucesso no painel.');
    }

    public function edit(Request $request, Loja $loja): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirLojaDaConta($loja, $conta);

        return $this->responder($request, 'admin.lojas.edit', [
            'loja' => $loja,
        ], $conta);
    }

    public function update(Request $request, Loja $loja): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirLojaDaConta($loja, $conta);

        $dados = $this->validarLoja($request, $loja);
        $dados['uf'] = $dados['uf'] ? strtoupper($dados['uf']) : null;

        $antes = $loja->only(['nome', 'status', 'tipo_loja', 'cidade', 'uf']);
        $loja->update($dados);

        $this->audit->registrar($request, $conta, 'lojas', 'loja_atualizada', "Loja {$loja->nome} atualizada.", $loja, [
            'antes' => $antes,
            'depois' => $loja->only(['nome', 'status', 'tipo_loja', 'cidade', 'uf']),
        ]);

        return redirect()
            ->route('admin.lojas.edit', $loja)
            ->with('status', 'Loja atualizada com sucesso.');
    }

    public function destroy(Request $request, Loja $loja): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirLojaDaConta($loja, $conta);

        $nome = $loja->nome;
        $this->audit->registrar($request, $conta, 'lojas', 'loja_removida', "Loja {$nome} removida.", $loja);
        $loja->delete();

        return redirect()
            ->route('admin.lojas.index')
            ->with('status', 'Loja removida do painel.');
    }

    private function validarLoja(Request $request, ?Loja $loja = null): array
    {
        $id = $loja?->id;

        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:30', 'unique:lojas,cnpj,' . $id],
            'telefone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'site' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:50'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'size:2'],
            'cep' => ['nullable', 'string', 'max:20'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'tipo_loja' => ['required', 'in:fisica,online,mista'],
            'status' => ['required', 'in:ativo,inativo'],
            'logo' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function garantirLojaDaConta(Loja $loja, $conta): void
    {
        abort_unless((int) $loja->conta_id === (int) $conta->id, 404);
    }
}
