<?php

namespace App\Http\Controllers\Web\Admin;

use App\Support\Onboarding\ContaOnboardingChecklist;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends AdminController
{
    public function __invoke(Request $request, ContaOnboardingChecklist $checklist): View
    {
        $conta = $this->contaAtual($request);

        return $this->responder($request, 'admin.onboarding', [
            'onboarding' => $checklist->build($conta, $request->user()->capacidadesNaConta($conta)),
        ], $conta);
    }
}
