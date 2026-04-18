<?php

namespace App\Services\Billing;

use App\Contracts\BillingGateway;
use App\Services\Billing\Asaas\AsaasBillingGateway;
use InvalidArgumentException;

class BillingManager
{
    public function gateway(?string $provider = null): BillingGateway
    {
        $provider = $provider ?: config('billing.default_provider');

        return match ($provider) {
            'asaas' => app(AsaasBillingGateway::class),
            default => throw new InvalidArgumentException("Provedor de billing nao suportado: {$provider}."),
        };
    }
}
