<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChamadoSuporte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChamadoSuporteController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->string('busca'));
        $status = trim((string) $request->string('status'));
        $categoria = trim((string) $request->string('categoria'));

        $chamados = ChamadoSuporte::query()
            ->with(['conta', 'usuario'])
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($inner) use ($busca) {
                    $inner->where('protocolo', 'like', "%{$busca}%")
                        ->orWhere('nome', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%")
                        ->orWhere('empresa', 'like', "%{$busca}%")
                        ->orWhere('assunto', 'like', "%{$busca}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($categoria !== '', fn ($query) => $query->where('categoria', $categoria))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('super-admin.suporte.index', [
            'user' => $request->user(),
            'chamados' => $chamados,
            'busca' => $busca,
            'statusSelecionado' => $status,
            'categoriaSelecionada' => $categoria,
            'statusDisponiveis' => ChamadoSuporte::statusDisponiveis(),
            'categoriasDisponiveis' => ChamadoSuporte::categoriasDisponiveis(),
            'prioridadesDisponiveis' => ChamadoSuporte::prioridadesDisponiveis(),
            'metricas' => [
                'abertos' => ChamadoSuporte::whereNotIn('status', ['resolvido', 'fechado'])->count(),
                'criticos' => ChamadoSuporte::where('prioridade', 'critica')->whereNotIn('status', ['resolvido', 'fechado'])->count(),
                'resolvidos' => ChamadoSuporte::whereIn('status', ['resolvido', 'fechado'])->count(),
            ],
        ]);
    }

    public function update(Request $request, ChamadoSuporte $chamado): RedirectResponse
    {
        $dados = $request->validate([
            'status' => ['required', Rule::in(array_keys(ChamadoSuporte::statusDisponiveis()))],
            'prioridade' => ['required', Rule::in(array_keys(ChamadoSuporte::prioridadesDisponiveis()))],
            'observacao_interna' => ['nullable', 'string', 'max:5000'],
        ]);

        $chamado->fill($dados);

        if ($dados['status'] === 'respondido' && ! $chamado->respondido_em) {
            $chamado->respondido_em = now();
        }

        if (in_array($dados['status'], ['resolvido', 'fechado'], true) && ! $chamado->resolvido_em) {
            $chamado->resolvido_em = now();
        }

        $chamado->save();

        return redirect()
            ->route('super-admin.suporte.index')
            ->with('status', "Chamado {$chamado->protocolo} atualizado.");
    }
}
