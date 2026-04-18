<?php

namespace App\Http\Controllers\Web\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Assinatura;
use App\Models\Conta;
use App\Services\Billing\BillingManager;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Throwable;

class AssinaturaBillingController extends Controller
{
    public function __invoke(Conta $conta, Assinatura $assinatura, BillingManager $billing): RedirectResponse
    {
        abort_unless($assinatura->conta_id === $conta->id, 404);

        try {
            $provider = $assinatura->billing_provider ?: $conta->billing_provider ?: null;
            $billing->gateway($provider)->syncAssinatura($assinatura);
        } catch (DomainException $exception) {
            return back()->with('status', $exception->getMessage());
        } catch (Throwable $exception) {
            report($exception);

            return back()->with('status', 'Nao foi possivel sincronizar a cobranca desta assinatura agora.');
        }

        return back()->with('status', 'Assinatura sincronizada com o provedor de cobranca.');
    }
}
