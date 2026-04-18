<?php

namespace App\Contracts;

use App\Models\Assinatura;
use App\Models\Conta;

interface BillingGateway
{
    public function provider(): string;

    public function syncConta(Conta $conta): Conta;

    public function syncAssinatura(Assinatura $assinatura): Assinatura;
}
