<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\CategoriaFinanceira;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaFinanceiraController extends Controller
{
    use InterageComConta;

    public function index(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->categoriasFinanceiras()->orderBy('tipo')->orderBy('nome')->get();
    }

    public function store(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'tipo' => 'required|in:receita,despesa,ambos',
            'cor' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:100',
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ]);

        $categoria = $conta->categoriasFinanceiras()->create([
            ...$data,
            'slug' => $this->gerarSlugUnico($conta, $data['slug'] ?? $data['nome']),
        ]);

        return response()->json($categoria, 201);
    }

    public function show(Request $request, Conta $conta, CategoriaFinanceira $categorias_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($categorias_financeira, $conta);

        return $categorias_financeira;
    }

    public function update(Request $request, Conta $conta, CategoriaFinanceira $categorias_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($categorias_financeira, $conta);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'tipo' => 'required|in:receita,despesa,ambos',
            'cor' => 'nullable|string|max:20',
            'icone' => 'nullable|string|max:100',
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ]);

        $categorias_financeira->update([
            ...$data,
            'slug' => $this->gerarSlugUnico($conta, $data['slug'] ?? $data['nome'], $categorias_financeira->id),
        ]);

        return response()->json($categorias_financeira, 200);
    }

    public function destroy(Request $request, Conta $conta, CategoriaFinanceira $categorias_financeira)
    {
        $this->garantirAcessoConta($request, $conta);
        $this->garantirRecursoDaConta($categorias_financeira, $conta);

        $categorias_financeira->delete();

        return response()->json(null, 204);
    }

    private function gerarSlugUnico(Conta $conta, string $valorBase, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($valorBase);
        $slug = $slugBase !== '' ? $slugBase : 'categoria-financeira';
        $contador = 1;

        while (
            CategoriaFinanceira::where('conta_id', $conta->id)
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
