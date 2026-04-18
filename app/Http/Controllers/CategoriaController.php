<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaController extends Controller
{
    public function index()
    {
        return Categoria::orderBy('nome')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $categoria = Categoria::create([
            'nome' => $data['nome'],
            'slug' => $this->gerarSlugUnico($data['slug'] ?? $data['nome']),
            'descricao' => $data['descricao'] ?? null,
        ]);

        return response()->json($categoria, 201);
    }

    public function show(string $id)
    {
        return Categoria::with('produtos')->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $categoria = Categoria::findOrFail($id);

        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $categoria->update([
            'nome' => $data['nome'],
            'slug' => $this->gerarSlugUnico($data['slug'] ?? $data['nome'], $categoria->id),
            'descricao' => $data['descricao'] ?? null,
        ]);

        return response()->json($categoria, 200);
    }

    public function destroy(string $id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return response()->json(null, 204);
    }

    private function gerarSlugUnico(string $valorBase, ?int $ignorarId = null): string
    {
        $slugBase = Str::slug($valorBase);
        $slug = $slugBase !== '' ? $slugBase : 'categoria';
        $contador = 1;

        while (
            Categoria::where('slug', $slug)
                ->when($ignorarId, fn ($query) => $query->where('id', '!=', $ignorarId))
                ->exists()
        ) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
