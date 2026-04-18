<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conta;
use App\Support\Access\ContaAccess;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class AdminController extends Controller
{
    protected function contaAtual(Request $request): Conta
    {
        return $request->user()
            ->contas()
            ->wherePivot('ativo', true)
            ->firstOrFail();
    }

    protected function dadosLayout(Request $request, ?Conta $conta = null): array
    {
        $conta ??= $this->contaAtual($request);

        return [
            'user' => $request->user(),
            'conta' => $conta,
            'assinaturaAtual' => $conta->assinaturas()->latest('id')->first(),
            'papelAtualConta' => $request->user()->papelNaConta($conta),
            'capacidadesConta' => $request->user()->capacidadesNaConta($conta),
            'access' => ContaAccess::class,
        ];
    }

    protected function responder(Request $request, string $view, array $dados = [], ?Conta $conta = null): View
    {
        return view($view, array_merge($this->dadosLayout($request, $conta), $dados));
    }
}
