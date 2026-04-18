<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Produto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProdutoController extends AdminController
{
    public function index(Request $request): View
    {
        $conta = $this->contaAtual($request);
        $busca = trim((string) $request->string('busca'));
        $status = (string) $request->string('status');

        $produtos = Produto::query()
            ->with(['categoria', 'marca'])
            ->withCount('precos')
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($subquery) use ($busca) {
                    $subquery
                        ->where('nome', 'like', "%{$busca}%")
                        ->orWhere('slug', 'like', "%{$busca}%")
                        ->orWhereHas('categoria', fn ($categoria) => $categoria->where('nome', 'like', "%{$busca}%"))
                        ->orWhereHas('marca', fn ($marca) => $marca->where('nome', 'like', "%{$busca}%"));
                });
            })
            ->when(in_array($status, ['ativo', 'inativo'], true), fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return $this->responder($request, 'admin.produtos.index', [
            'busca' => $busca,
            'statusSelecionado' => $status,
            'produtos' => $produtos,
        ], $conta);
    }

    public function create(Request $request): View
    {
        return $this->responder($request, 'admin.produtos.create', [
            'categorias' => Categoria::orderBy('nome')->get(),
            'marcas' => Marca::orderBy('nome')->get(),
            'produto' => new Produto(),
            'especificacoesTexto' => '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $this->validarProduto($request);

        $produto = Produto::create($this->montarPayloadProduto($dados));

        return redirect()
            ->route('admin.produtos.edit', $produto)
            ->with('status', 'Produto cadastrado com sucesso no catalogo.');
    }

    public function edit(Request $request, Produto $produto): View
    {
        return $this->responder($request, 'admin.produtos.edit', [
            'categorias' => Categoria::orderBy('nome')->get(),
            'marcas' => Marca::orderBy('nome')->get(),
            'produto' => $produto,
            'especificacoesTexto' => implode(PHP_EOL, $produto->especificacoes ?? []),
        ]);
    }

    public function update(Request $request, Produto $produto): RedirectResponse
    {
        $dados = $this->validarProduto($request, $produto);

        $produto->update($this->montarPayloadProduto($dados, $produto));

        return redirect()
            ->route('admin.produtos.edit', $produto)
            ->with('status', 'Produto atualizado com sucesso.');
    }

    public function destroy(Request $request, Produto $produto): RedirectResponse
    {
        $produto->delete();

        return redirect()
            ->route('admin.produtos.index')
            ->with('status', 'Produto removido do catalogo.');
    }

    private function validarProduto(Request $request, ?Produto $produto = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'categoria_id' => ['nullable', 'exists:categorias,id', 'required_without:nova_categoria_nome'],
            'nova_categoria_nome' => ['nullable', 'string', 'max:255', 'required_without:categoria_id'],
            'marca_id' => ['nullable', 'exists:marcas,id'],
            'nova_marca_nome' => ['nullable', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'especificacoes_texto' => ['nullable', 'string'],
            'imagem_principal' => ['nullable', 'string', 'max:2048'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
        ]);
    }

    private function montarPayloadProduto(array $dados, ?Produto $produto = null): array
    {
        $nomeCategoriaNova = trim((string) ($dados['nova_categoria_nome'] ?? ''));
        $nomeMarcaNova = trim((string) ($dados['nova_marca_nome'] ?? ''));

        $categoriaId = ! empty($dados['categoria_id'])
            ? (int) $dados['categoria_id']
            : Categoria::firstOrCreate(
                ['slug' => $this->gerarSlugCategoria($nomeCategoriaNova)],
                ['nome' => $nomeCategoriaNova]
            )->id;

        $marcaId = ! empty($dados['marca_id'])
            ? (int) $dados['marca_id']
            : ($nomeMarcaNova !== '' ? Marca::firstOrCreate(['nome' => $nomeMarcaNova])->id : null);

        return [
            'nome' => $dados['nome'],
            'slug' => $this->gerarSlugProduto($dados['nome'], $produto),
            'categoria_id' => $categoriaId,
            'marca_id' => $marcaId,
            'descricao' => ($dados['descricao'] ?? null) ?: null,
            'especificacoes' => $this->normalizarEspecificacoes($dados['especificacoes_texto'] ?? null),
            'imagem_principal' => trim((string) ($dados['imagem_principal'] ?? '')) ?: null,
            'status' => $dados['status'],
        ];
    }

    private function normalizarEspecificacoes(?string $texto): ?array
    {
        $linhas = collect(preg_split('/\r\n|\r|\n/', (string) $texto))
            ->map(fn ($linha) => trim((string) $linha))
            ->filter()
            ->values()
            ->all();

        return $linhas === [] ? null : $linhas;
    }

    private function gerarSlugProduto(string $nome, ?Produto $produto = null): string
    {
        $base = Str::slug($nome);
        $slugBase = $base !== '' ? $base : 'produto';
        $slug = $slugBase;
        $contador = 1;

        while (
            Produto::where('slug', $slug)
                ->when($produto, fn ($query) => $query->where('id', '!=', $produto->id))
                ->exists()
        ) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }

    private function gerarSlugCategoria(string $nome): string
    {
        $base = Str::slug($nome);
        $slugBase = $base !== '' ? $base : 'categoria';
        $slug = $slugBase;
        $contador = 1;

        while (Categoria::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
