<?php

namespace App\Http\Controllers\Web\Admin;

use App\Services\Auditoria\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $antes = $user->only(['name', 'email', 'avatar_path']);
        $emailAlterado = $user->email !== $dados['email'];
        $avatarPath = $user->avatar_path;

        if ($request->hasFile('avatar')) {
            $avatarPath = $this->salvarAvatar($request);

            if ($user->avatar_path) {
                $this->removerAvatarAnterior($user->avatar_path);
            }
        }

        $user->forceFill([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'avatar_path' => $avatarPath,
            'email_verified_at' => $emailAlterado ? null : $user->email_verified_at,
        ])->save();

        $this->audit->registrar($request, $conta, 'seguranca', 'perfil_atualizado', 'Perfil do usuario atualizado.', $user, [
            'antes' => $antes,
            'depois' => $user->only(['name', 'email', 'avatar_path']),
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

    private function salvarAvatar(Request $request): string
    {
        $arquivo = $request->file('avatar');
        $diretorio = public_path('images/uploads/usuarios');

        File::ensureDirectoryExists($diretorio);

        $nomeArquivo = Str::slug(pathinfo($arquivo->getClientOriginalName(), PATHINFO_FILENAME));
        $nomeArquivo = ($nomeArquivo ?: 'avatar') . '-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6)) . '.' . $arquivo->extension();

        $arquivo->move($diretorio, $nomeArquivo);

        return 'images/uploads/usuarios/' . $nomeArquivo;
    }

    private function removerAvatarAnterior(string $avatarPath): void
    {
        $caminho = public_path($avatarPath);
        $diretorioPermitido = public_path('images/uploads/usuarios');

        if (str_starts_with(realpath(dirname($caminho)) ?: '', realpath($diretorioPermitido) ?: '') && File::exists($caminho)) {
            File::delete($caminho);
        }
    }
}
