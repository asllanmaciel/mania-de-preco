<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InterageComConta;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContaController extends Controller
{
    use InterageComConta;

    public function index(Request $request)
    {
        return $request->user()
            ->contas()
            ->withCount('lojas')
            ->with(['assinaturas' => fn ($query) => $query->latest()->limit(1)])
            ->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'documento' => 'nullable|string|max:20|unique:contas,documento',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
        ]);

        $conta = Conta::create([
            ...$data,
            'slug' => $this->gerarSlugUnico($data['slug'] ?? $data['nome_fantasia']),
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $conta->usuarios()->attach($request->user()->id, [
            'papel' => 'owner',
            'ativo' => true,
            'ultimo_acesso_em' => now(),
        ]);

        return response()->json($conta->load('usuarios:id,name,email'), 201);
    }

    public function show(Request $request, Conta $conta)
    {
        $this->garantirAcessoConta($request, $conta);

        return $conta->load([
            'lojas',
            'assinaturas.plano',
            'categoriasFinanceiras',
            'contasFinanceiras',
        ]);
    }

    private function gerarSlugUnico(string $valorBase): string
    {
        $slugBase = Str::slug($valorBase);
        $slug = $slugBase !== '' ? $slugBase : 'conta';
        $contador = 1;

        while (Conta::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
