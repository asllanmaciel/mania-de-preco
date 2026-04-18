<?php

namespace App\Services\Billing\Asaas;

use App\Models\Assinatura;
use App\Models\BillingWebhookEvent;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AsaasWebhookProcessor
{
    public function process(array $payload, ?string $authToken = null): BillingWebhookEvent
    {
        $this->validarToken($authToken);

        return DB::transaction(function () use ($payload) {
            $eventId = (string) Arr::get($payload, 'id');
            $eventType = (string) Arr::get($payload, 'event', 'desconhecido');

            $event = BillingWebhookEvent::firstOrCreate(
                [
                    'provider' => 'asaas',
                    'event_id' => $eventId !== '' ? $eventId : null,
                ],
                [
                    'event_type' => $eventType,
                    'status' => 'recebido',
                    'payload' => $payload,
                ]
            );

            if ($event->processed_at) {
                return $event;
            }

            $event->forceFill([
                'event_type' => $eventType,
                'payload' => $payload,
            ])->saveQuietly();

            $assinatura = $this->resolverAssinatura($payload);

            if ($assinatura) {
                $this->atualizarAssinatura($assinatura, $eventType, $payload);
            }

            $event->forceFill([
                'status' => 'processado',
                'processed_at' => now(),
                'error_message' => null,
            ])->saveQuietly();

            return $event;
        });
    }

    private function validarToken(?string $authToken): void
    {
        $expected = (string) config('billing.providers.asaas.webhook_token');

        if ($expected === '') {
            return;
        }

        if (! hash_equals($expected, (string) $authToken)) {
            throw new UnauthorizedHttpException('Asaas', 'Token do webhook do Asaas invalido.');
        }
    }

    private function resolverAssinatura(array $payload): ?Assinatura
    {
        $subscriptionId = Arr::get($payload, 'subscription.id')
            ?: Arr::get($payload, 'payment.subscription');

        if (! $subscriptionId) {
            return null;
        }

        return Assinatura::query()
            ->where('billing_provider', 'asaas')
            ->where('billing_subscription_id', $subscriptionId)
            ->first();
    }

    private function atualizarAssinatura(Assinatura $assinatura, string $eventType, array $payload): void
    {
        $billingStatus = match ($eventType) {
            'PAYMENT_OVERDUE' => 'OVERDUE',
            'PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED' => 'RECEIVED',
            'SUBSCRIPTION_INACTIVATED' => 'INACTIVE',
            'SUBSCRIPTION_DELETED' => 'DELETED',
            default => Arr::get($payload, 'subscription.status')
                ?: Arr::get($payload, 'payment.status')
                ?: $assinatura->billing_status,
        };

        $statusInterno = match ($eventType) {
            'PAYMENT_OVERDUE' => 'inadimplente',
            'PAYMENT_RECEIVED', 'PAYMENT_CONFIRMED', 'SUBSCRIPTION_CREATED', 'SUBSCRIPTION_UPDATED' => 'ativa',
            'SUBSCRIPTION_INACTIVATED' => 'cancelada',
            'SUBSCRIPTION_DELETED' => 'encerrada',
            default => $assinatura->status,
        };

        $billingPayload = $assinatura->billing_payload ?? [];
        $billingPayload['last_webhook'] = $payload;

        $assinatura->forceFill([
            'status' => $statusInterno,
            'billing_status' => $billingStatus,
            'billing_last_synced_at' => now(),
            'billing_payload' => $billingPayload,
        ])->saveQuietly();
    }
}
