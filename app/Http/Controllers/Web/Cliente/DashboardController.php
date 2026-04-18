<?php

namespace App\Http\Controllers\Web\Cliente;

use App\Http\Controllers\Controller;
use App\Models\AlertaPreco;
use App\Models\AvaliacaoLoja;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('cliente.dashboard', [
            'user' => $user,
            'alertas' => AlertaPreco::query()
                ->with(['produto', 'lojaReferencia'])
                ->where('user_id', $user->id)
                ->latest('id')
                ->take(8)
                ->get(),
            'avaliacoes' => AvaliacaoLoja::query()
                ->with('loja')
                ->where('user_id', $user->id)
                ->latest('id')
                ->take(8)
                ->get(),
        ]);
    }
}
