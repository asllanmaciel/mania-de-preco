<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Contracts\View\View;
use App\Models\User;
use App\Services\Auditoria\AuditLogger;
use App\Support\Access\ContaAccess;
use App\Support\Billing\ContaUsageMeter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipeController extends AdminController
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly ContaUsageMeter $usageMeter
    )
    {
    }

    public function index(Request $request)
    {
        $conta = $this->contaAtual($request);
        $busca = trim((string) $request->string('busca'));
        $papel = trim((string) $request->string('papel'));

        $usuarios = $conta->usuarios()
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($inner) use ($busca) {
                    $inner->where('users.name', 'like', "%{$busca}%")
                        ->orWhere('users.email', 'like', "%{$busca}%");
                });
            })
            ->when($papel !== '', fn ($query) => $query->wherePivot('papel', $papel))
            ->orderBy('users.name')
            ->paginate(12)
            ->withQueryString();

        $contagemPorPapel = $conta->usuarios()
            ->selectRaw('conta_user.papel, count(*) as total')
            ->groupBy('conta_user.papel')
            ->pluck('total', 'papel');

        return $this->responder($request, 'admin.equipe.index', [
            'usuarios' => $usuarios,
            'busca' => $busca,
            'papelSelecionado' => $papel,
            'contagemPorPapel' => $contagemPorPapel,
            'papeisDisponiveis' => $this->papeisDisponiveis(),
            'usoPlano' => $this->usageMeter->resumo($conta),
        ], $conta);
    }

    public function create(Request $request): View
    {
        return $this->responder($request, 'admin.equipe.create', [
            'membro' => null,
            'papeisDisponiveis' => $this->papeisDisponiveis(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $conta = $this->contaAtual($request);

        if (! $this->usageMeter->podeAdicionar($conta, 'usuarios')) {
            return redirect()
                ->route('admin.equipe.index')
                ->with('status', $this->usageMeter->mensagemBloqueio($conta, 'usuarios'));
        }

        $dados = $this->validarMembro($request);

        $usuario = User::where('email', $dados['email'])->first();

        if (! $usuario) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', 'min:6'],
            ]);

            $usuario = User::create([
                'name' => $dados['name'],
                'email' => $dados['email'],
                'password' => $dados['password'],
            ]);
        }

        $conta->usuarios()->syncWithoutDetaching([
            $usuario->id => [
                'papel' => $dados['papel'],
                'ativo' => $request->boolean('ativo', true),
                'ultimo_acesso_em' => null,
            ],
        ]);

        $this->audit->registrar(
            $request,
            $conta,
            'equipe',
            'membro_adicionado',
            "Membro {$usuario->email} adicionado a equipe.",
            $usuario,
            ['papel' => $dados['papel']]
        );

        return redirect()
            ->route('admin.equipe.index')
            ->with('status', 'Membro adicionado a equipe com sucesso.');
    }

    public function edit(Request $request, User $equipe): View
    {
        $conta = $this->contaAtual($request);
        $membro = $conta->usuarios()->where('users.id', $equipe->id)->firstOrFail();

        return $this->responder($request, 'admin.equipe.edit', [
            'membro' => $membro,
            'papeisDisponiveis' => $this->papeisDisponiveis(),
        ], $conta);
    }

    public function update(Request $request, User $equipe): RedirectResponse
    {
        $conta = $this->contaAtual($request);
        $membro = $conta->usuarios()->where('users.id', $equipe->id)->firstOrFail();
        $dados = $this->validarMembro($request, $membro);
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $membro->update([
            'name' => $dados['name'],
            'email' => $dados['email'],
        ]);

        if (! empty($dados['password'])) {
            $membro->update([
                'password' => $dados['password'],
            ]);
        }

        $conta->usuarios()->updateExistingPivot($membro->id, [
            'papel' => $dados['papel'],
            'ativo' => $request->boolean('ativo', true),
        ]);

        $this->audit->registrar(
            $request,
            $conta,
            'equipe',
            'membro_atualizado',
            "Membro {$membro->email} atualizado na equipe.",
            $membro,
            [
                'papel' => $dados['papel'],
                'ativo' => $request->boolean('ativo', true),
            ]
        );

        return redirect()
            ->route('admin.equipe.edit', $membro)
            ->with('status', 'Permissoes e dados do membro atualizados.');
    }

    private function validarMembro(Request $request, ?User $membro = null): array
    {
        $regrasEmail = ['required', 'email', 'max:255'];

        if ($membro) {
            $regrasEmail[] = Rule::unique('users', 'email')->ignore($membro->id);
        }

        return $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => $regrasEmail,
            'password' => ['nullable', 'string', 'min:6'],
            'papel' => ['required', Rule::in(array_keys($this->papeisDisponiveis()))],
        ]);
    }

    private function papeisDisponiveis(): array
    {
        return ContaAccess::roles();
    }
}
