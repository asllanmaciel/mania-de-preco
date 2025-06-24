<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        return Marca::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'logo' => 'nullable|string',
        ]);

        $marca = Marca::create($request->all());

        return response()->json($marca, 201);
    }

    public function show($id)
    {
        return Marca::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $marca = Marca::findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'logo' => 'nullable|string',
        ]);

        $marca->update($request->all());

        return response()->json($marca, 200);
    }

    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        $marca->delete();

        return response()->json(null, 204);
    }
}
