<?php

namespace App\Services\Billing\Asaas;

use App\Contracts\BillingGateway;
use App\Models\Assinatura;
use App\Models\Conta;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Arr;

class AsaasBillingGateway implements BillingGateway
{
    public function __construct(
        private readonly AsaasClient $client,
    ) {
    }

    public function provider(): string
    {
        return 'asaas';
    }

    public function syncConta(Conta $conta): Conta
    {
        $payload = $this->customerPayload($conta);

        $response = $conta->billing_customer_id
            ? $this->client->put("/customers/{$conta->billing_customer_id}", $payload)
            : $this->client->post('/customers', $payload);

        $conta->forceFill([
            'billing_provider' => $this->provider(),
            'billing_customer_id' => (string) Arr::get($response, 'id', $conta->billing_customer_id),
            'billing_synced_at' => now(),
            'billing_metadata' => [
                'customer' => $response,
            ],
        ])->saveQuietly();

        return $conta->fresh();
    }

    public function syncAssinatura(Assinatura $assinatura): Assinatura
    {
        $assinatura->loadMissing(['conta', 'plano']);

        $conta = $this->syncConta($assinatura->conta);
        $payload = $this->subscriptionPayload($assinatura, $conta);

        if ($assinatura->billing_subscription_id) {
            $response = $this->client->put(
                "/subscriptions/{$assinatura->billing_subscription_id}",
                $payload + [
                    'status' => $assinatura->status === 'cancelada' || $assinatura->status === 'encerrada' ? 'INACTIVE' : 'ACTIVE',
                    'updatePendingPayments' => true,
                ]
            );
        } else {
            $response = $this->client->post('/subscriptions', $payload);
        }

        $subscriptionId = (string) Arr::get($response, 'id', $assinatura->billing_subscription_id);
        $pagamentos = $subscriptionId !== ''
            ? $this->client->get("/subscriptions/{$subscriptionId}/payments", ['limit' => 1, 'offset' => 0])
            : [];
        $primeiroPagamento = Arr::first(Arr::get($pagamentos, 'data', []));

        $assinatura->forceFill([
            'billing_provider' => $this->provider(),
            'billing_subscription_id' => $subscriptionId,
            'billing_checkout_url' => $this->resolverCheckoutUrl($primeiroPagamento),
            'billing_status' => (string) Arr::get($response, 'status', $assinatura->billing_status ?: 'ACTIVE'),
            'billing_last_synced_at' => now(),
            'billing_payload' => [
                'subscription' => $response,
                'first_payment' => $primeiroPagamento,
            ],
        ])->saveQuietly();

        return $assinatura->fresh(['plano', 'conta']);
    }

    private function customerPayload(Conta $conta): array
    {
        $documento = preg_replace('/\D+/', '', (string) $conta->documento);

        if ($documento === '') {
            throw new DomainException('A conta precisa ter CPF ou CNPJ para sincronizar com o Asaas.');
        }

        return array_filter([
            'name' => $conta->razao_social ?: $conta->nome_fantasia,
            'cpfCnpj' => $documento,
            'email' => $conta->email,
            'mobilePhone' => $this->somenteDigitos($conta->telefone),
            'externalReference' => 'conta:' . $conta->id,
            'notificationDisabled' => false,
            'observations' => 'Conta sincronizada a partir do Mania de Preco.',
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function subscriptionPayload(Assinatura $assinatura, Conta $conta): array
    {
        if (! $conta->billing_customer_id) {
            throw new DomainException('A conta precisa estar sincronizada com um cliente no Asaas.');
        }

        return array_filter([
            'customer' => $conta->billing_customer_id,
            'billingType' => config('billing.providers.asaas.subscription_billing_type', 'UNDEFINED'),
            'value' => (float) $assinatura->valor,
            'nextDueDate' => $this->resolverProximoVencimento($assinatura)->toDateString(),
            'cycle' => $this->mapearCiclo($assinatura->ciclo_cobranca),
            'description' => $this->descricaoAssinatura($assinatura),
            'externalReference' => 'assinatura:' . $assinatura->id,
            'endDate' => $assinatura->cancelada_em?->toDateString(),
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function resolverProximoVencimento(Assinatura $assinatura): CarbonImmutable
    {
        $vencimento = $assinatura->expira_em ?: $assinatura->inicia_em;

        if (! $vencimento) {
            return CarbonImmutable::now()->addDays(1);
        }

        $data = CarbonImmutable::parse($vencimento);

        return $data->isPast() ? CarbonImmutable::now()->addDays(1) : $data;
    }

    private function mapearCiclo(string $ciclo): string
    {
        return match ($ciclo) {
            'anual' => 'YEARLY',
            default => 'MONTHLY',
        };
    }

    private function descricaoAssinatura(Assinatura $assinatura): string
    {
        $plano = $assinatura->plano?->nome ?: 'Plano Mania de Preco';

        return "{$plano} | conta {$assinatura->conta_id}";
    }

    private function resolverCheckoutUrl(mixed $pagamento): ?string
    {
        if (! is_array($pagamento)) {
            return null;
        }

        return Arr::get($pagamento, 'invoiceUrl')
            ?: Arr::get($pagamento, 'bankSlipUrl')
            ?: Arr::get($pagamento, 'transactionReceiptUrl');
    }

    private function somenteDigitos(?string $valor): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $valor);

        return $digitos !== '' ? $digitos : null;
    }
}
