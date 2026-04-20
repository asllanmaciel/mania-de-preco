<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'aceite_termos' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => $dados['password'],
            'termos_aceitos_em' => now(),
            'termos_versao' => config('legal.termos_versao'),
            'privacidade_versao' => config('legal.privacidade_versao'),
            'consentimento_ip' => $request->ip(),
            'consentimento_user_agent' => Str::limit((string) $request->userAgent(), 1024, ''),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->intended(route('cliente.dashboard'))
            ->with('status', 'Conta criada com sucesso. Agora voce ja pode acompanhar alertas e ofertas pelo seu painel.');
    }
}
