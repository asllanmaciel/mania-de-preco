<?php

namespace App\Http\Controllers\Web\Admin\Financeiro;

use App\Http\Controllers\Web\Admin\AdminController;
use App\Models\CategoriaFinanceira;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoriaFinanceiraController extends AdminController
{
    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $busca = trim((string) $request->string('busca'));
        $tipoSelecionado = (string) $request->string('tipo');
        $statusSelecionado = (string) $request->string('status');

        $categorias = $conta->categoriasFinanceiras()
            ->withCount(['movimentacoes', 'contasPagar', 'contasReceber'])
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery
                        ->where('nome', 'like', "%{$busca}%")
                        ->orWhere('slug', 'like', "%{$busca}%")
                        ->orWhere('descricao', 'like', "%{$busca}%");
                });
            })
            ->when(in_array($tipoSelecionado, ['receita', 'despesa', 'ambos'], true), fn ($query) => $query->where('tipo', $tipoSelecionado))
            ->when($statusSelecionado === 'ativas', fn ($query) => $query->where('ativa', true))
            ->when($statusSelecionado === 'inativas', fn ($query) => $query->where('ativa', false))
            ->orderBy('tipo')
            ->orderBy('nome')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.financeiro.categorias.index', [
            'busca' => $busca,
            'tipoSelecionado' => $tipoSelecionado,
            'statusSelecionado' => $statusSelecionado,
            'categorias' => $categorias,
        ], $conta);
    }

    public function create(Request $request): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.financeiro.categorias.create', [
            'categoria' => new CategoriaFinanceira([
                'tipo' => 'despesa',
                'cor' => '#0f766e',
                'ativa' => true,
            ]),
        ], $conta);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $dados = $this->validarCategoria($request);

        $conta->categoriasFinanceiras()->create([
            ...$dados,
            'slug' => $this->gerarSlugUnico($conta->id, $dados['slug'] ?? $dados['nome']),
            'ativa' => array_key_exists('ativa', $dados) ? (bool) $dados['ativa'] : false,
        ]);

        return redirect()
            ->route('admin.financeiro.categorias.index')
            ->with('status', 'Categoria financeira cadastrada com sucesso.');
    }

    public function edit(Request $request, CategoriaFinanceira $categoria): View
    {
        $conta = $this->contaAtual($request);
        $this->garantirCategoriaDaConta($categoria, $conta->id);

        return $this->responder($request, 'admin.financeiro.categorias.edit', [
            'categoria' => $categoria,
        ], $conta);
    }

    public function update(Request $request, CategoriaFinanceira $categoria): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirCategoriaDaConta($categoria, $conta->id);

        $dados = $this->validarCategoria($request);

        $categoria->update([
            ...$dados,
            'slug' => $this->gerarSlugUnico($conta->id, $dados['slug'] ?? $dados['nome'], $categoria->id),
            'ativa' => array_key_exists('ativa', $dados) ? (bool) $dados['ativa'] : false,
        ]);

        return redirect()
            ->route('admin.financeiro.categorias.edit', $categoria)
            ->with('status', 'Categoria financeira atualizada com sucesso.');
    }

    public function destroy(Request $request, CategoriaFinanceira $categoria): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $this->garantirCategoriaDaConta($categoria, $conta->id);

        $categoria->loadCount(['movimentacoes', 'contasPagar', 'contasReceber']);

        if (($categoria->movimentacoes_count + $categoria->contas_pagar_count + $categoria->contas_receber_count) > 0) {
            return redirect()
                ->route('admin.financeiro.categorias.index')
                ->with('status', 'Essa categoria ja esta em uso e nao pode ser removida.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.financeiro.categorias.index')
            ->with('status', 'Categoria financeira removida do painel.');
    }

    private function validarCategoria(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'tipo' => ['required', 'in:receita,despesa,ambos'],
            'cor' => ['nullable', 'string', 'max:20'],
            'icone' => ['nullable', 'string', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'ativa' => ['nullable', 'boolean'],
        ]);
    }

    private function garantirCategoriaDaConta(CategoriaFinanceira $categoria, int $contaId): void
    {
        abort_unless((int) $categoria->conta_id === $contaId, 404);
    }

    private function gerarSlugUnico(int $contaId, string $valorBase, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($valorBase);
        $slugBase = $slugBase !== '' ? $slugBase : 'categoria-financeira';
        $slug = $slugBase;
        $contador = 1;

        while (
            CategoriaFinanceira::query()
                ->where('conta_id', $contaId)
                ->where('slug', $slug)
                ->when($ignorarId, fn ($query) => $query->where('id', '!=', $ignorarId))
                ->exists()
        ) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
