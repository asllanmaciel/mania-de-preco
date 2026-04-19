<?php

namespace App\Http\Controllers\Web\Cliente;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\AvaliacaoLoja;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $alertas = AlertaPreco::query()
            ->with(['produto.marca', 'lojaReferencia'])
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();

        return view('cliente.dashboard', [
            'user' => $user,
            'alertas' => $alertas,
            'produtos' => Produto::query()
                ->where('status', 'ativo')
                ->with(['marca', 'categoria'])
                ->withMin('precos as menor_preco', 'preco')
                ->orderBy('nome')
                ->get(),
            'totalAlertas' => $alertas->count(),
            'alertasAtivos' => $alertas->where('status', 'ativo')->count(),
            'alertasAtendidos' => $alertas->where('status', 'atendido')->count(),
            'avaliacoes' => AvaliacaoLoja::query()
                ->with('loja')
                ->where('user_id', $user->id)
                ->latest('id')
                ->take(8)
                ->get(),
        ]);
    }
}
