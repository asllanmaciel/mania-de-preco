<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'aceite_termos' => 'accepted',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'termos_aceitos_em' => now(),
                'termos_versao' => config('legal.termos_versao'),
                'privacidade_versao' => config('legal.privacidade_versao'),
                'consentimento_ip' => $request->ip(),
                'consentimento_user_agent' => Str::limit((string) $request->userAgent(), 1024, ''),
            ]);

            $conta = Conta::create([
                'nome_fantasia' => $request->name,
                'slug' => $this->gerarSlugConta($request->name),
                'email' => $request->email,
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]);

            $conta->usuarios()->attach($user->id, [
                'papel' => 'owner',
                'ativo' => true,
                'ultimo_acesso_em' => now(),
            ]);

            return $user;
        });

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('contas'),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('contas'),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout efetuado com sucesso.']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('contas'));
    }

    private function gerarSlugConta(string $nome): string
    {
        $slugBase = Str::slug($nome);
        $slug = $slugBase !== '' ? $slugBase : 'conta';
        $contador = 1;

        while (Conta::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$contador}";
            $contador++;
        }

        return $slug;
    }
}
