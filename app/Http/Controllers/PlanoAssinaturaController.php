<?php

namespace App\Http\Controllers;

use App\Models\PlanoAssinatura;
use Illuminate\Http\Request;

class PlanoAssinaturaController extends Controller
{
    public function index()
    {
        return PlanoAssinatura::with('loja')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'loja_id' => 'required|exists:lojas,id',
            'nome_plano' => 'required|in:gratis,basico,premium,top',
            'valor' => 'required|numeric',
            'validade' => 'nullable|date',
            'status' => 'in:ativo,expirado,cancelado',
        ]);

        $plano = PlanoAssinatura::create($request->all());

        return response()->json($plano, 201);
    }

    public function show($id)
    {
        return PlanoAssinatura::with('loja')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $plano = PlanoAssinatura::findOrFail($id);

        $request->validate([
            'nome_plano' => 'required|in:gratis,basico,premium,top',
            'valor' => 'required|numeric',
            'validade' => 'nullable|date',
            'status' => 'in:ativo,expirado,cancelado',
        ]);

        $plano->update($request->all());

        return response()->json($plano, 200);
    }

    public function destroy($id)
    {
        $plano = PlanoAssinatura::findOrFail($id);
        $plano->delete();

        return response()->json(null, 204);
    }
}
