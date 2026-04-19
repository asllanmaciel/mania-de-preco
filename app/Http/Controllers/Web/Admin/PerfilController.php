<?php

namespace App\Http\Controllers\Web\Admin;

use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PerfilController extends AdminController
{
    public function __construct(private readonly AuditLogger $audit)
    {
    }

    public function edit(Request $request): View
    {
        return $this->responder($request, 'admin.perfil.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $conta = $this->contaAtual($request);

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $antes = $user->only(['name', 'email']);
        $emailAlterado = $user->email !== $dados['email'];

        $user->forceFill([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'email_verified_at' => $emailAlterado ? null : $user->email_verified_at,
        ])->save();

        $this->audit->registrar($request, $conta, 'seguranca', 'perfil_atualizado', 'Perfil do usuario atualizado.', $user, [
            'antes' => $antes,
            'depois' => $user->only(['name', 'email']),
        ]);

        return redirect()
            ->route('admin.perfil.edit')
            ->with('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $conta = $this->contaAtual($request);

        $dados = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->update([
            'password' => $dados['password'],
        ]);

        $this->audit->registrar($request, $conta, 'seguranca', 'senha_atualizada', 'Senha do usuario atualizada.', $user);

        return redirect()
            ->route('admin.perfil.edit')
            ->with('status', 'Senha atualizada com seguranca.');
    }
}
