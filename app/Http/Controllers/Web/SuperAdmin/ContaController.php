<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContaController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->string('busca'));
        $status = trim((string) $request->string('status'));

        $contas = Conta::query()
            ->withCount(['usuarios', 'lojas', 'movimentacoesFinanceiras'])
            ->with(['assinaturas' => fn ($query) => $query->with('plano')->latest('id')])
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($inner) use ($busca) {
                    $inner->where('nome_fantasia', 'like', "%{$busca}%")
                        ->orWhere('slug', 'like', "%{$busca}%")
                        ->orWhere('email', 'like', "%{$busca}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $statusResumo = Conta::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('super-admin.contas.index', [
            'user' => $request->user(),
            'busca' => $busca,
            'statusSelecionado' => $status,
            'contas' => $contas,
            'statusResumo' => $statusResumo,
        ]);
    }

    public function show(Request $request, Conta $conta): View
    {
        $conta->load([
            'usuarios' => fn ($query) => $query->orderBy('name'),
            'lojas' => fn ($query) => $query->withCount('precos')->orderBy('nome'),
            'assinaturas' => fn ($query) => $query->with('plano')->latest('id'),
            'contasFinanceiras' => fn ($query) => $query->orderBy('nome'),
            'movimentacoesFinanceiras' => fn ($query) => $query->latest('data_movimentacao')->take(8),
            'contasPagar' => fn ($query) => $query->latest('vencimento')->take(6),
            'contasReceber' => fn ($query) => $query->latest('vencimento')->take(6),
        ]);

        $assinaturaAtual = $conta->assinaturas->first();

        return view('super-admin.contas.show', [
            'user' => $request->user(),
            'conta' => $conta,
            'assinaturaAtual' => $assinaturaAtual,
            'metricas' => [
                'usuarios' => $conta->usuarios->count(),
                'lojas' => $conta->lojas->count(),
                'contas_financeiras' => $conta->contasFinanceiras->count(),
                'movimentacoes' => $conta->movimentacoesFinanceiras->count(),
                'pagar_aberto' => $conta->contasPagar->whereIn('status', ['aberta', 'parcial'])->count(),
                'receber_aberto' => $conta->contasReceber->whereIn('status', ['aberta', 'parcial'])->count(),
            ],
        ]);
    }
}
