<?php

namespace App\Http\Controllers\Web\Admin;

use App\Support\Lancamento\ContaLaunchReadiness;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LancamentoController extends AdminController
{
    public function __invoke(Request $request, ContaLaunchReadiness $readiness): View
    {
        $conta = $this->contaAtual($request);
        $capacidades = $request->user()->capacidadesNaConta($conta);

        return $this->responder($request, 'admin.lancamento', [
            'prontidao' => $readiness->analisar($conta, $capacidades),
        ], $conta);
    }
}
