<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        return Produto::with(['categoria', 'marca', 'precos'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:produtos',
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'descricao' => 'nullable|string',
            'especificacoes' => 'nullable|array',
            'imagem_principal' => 'nullable|string',
            'status' => 'in:ativo,inativo',
        ]);

        $produto = Produto::create($request->all());

        return response()->json($produto, 201);
    }

    public function show($id)
    {
        return Produto::with(['categoria', 'marca', 'precos'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:produtos,slug,' . $id,
            'categoria_id' => 'required|exists:categorias,id',
            'marca_id' => 'nullable|exists:marcas,id',
            'descricao' => 'nullable|string',
            'especificacoes' => 'nullable|array',
            'imagem_principal' => 'nullable|string',
            'status' => 'in:ativo,inativo',
        ]);

        $produto->update($request->all());

        return response()->json($produto, 200);
    }

    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        $produto->delete();

        return response()->json(null, 204);
    }
}
