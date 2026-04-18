<?php

namespace Tests\Feature;

use App\Models\Assinatura;
use App\Models\Conta;
use App\Models\Plano;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AsaasWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_asaas_webhook_updates_subscription_status(): void
    {
        config()->set('billing.providers.asaas.webhook_token', 'token-seguro');

        $conta = Conta::create([
            'nome_fantasia' => 'Conta Billing',
            'slug' => 'conta-billing',
            'documento' => '12.345.678/0001-99',
            'email' => 'billing@example.com',
            'status' => 'ativo',
            'billing_provider' => 'asaas',
            'billing_customer_id' => 'cus_123456',
        ]);

        $plano = Plano::create([
            'nome' => 'Scale',
            'slug' => 'scale',
            'descricao' => 'Plano scale',
            'valor_mensal' => 199.90,
            'valor_anual' => 1999.00,
            'status' => 'ativo',
        ]);

        $assinatura = Assinatura::create([
            'conta_id' => $conta->id,
            'plano_id' => $plano->id,
            'status' => 'ativa',
            'ciclo_cobranca' => 'mensal',
            'valor' => 199.90,
            'inicia_em' => now()->subMonth()->toDateString(),
            'expira_em' => now()->addDays(5)->toDateString(),
            'billing_provider' => 'asaas',
            'billing_subscription_id' => 'sub_123456',
            'billing_status' => 'ACTIVE',
        ]);

        $response = $this->postJson('/api/billing/webhooks/asaas', [
            'id' => 'evt_001',
            'event' => 'PAYMENT_OVERDUE',
            'payment' => [
                'id' => 'pay_001',
                'subscription' => 'sub_123456',
                'status' => 'OVERDUE',
            ],
        ], [
            'asaas-access-token' => 'token-seguro',
        ]);

        $response->assertOk()
            ->assertJson([
                'ok' => true,
                'event_id' => 'evt_001',
                'status' => 'processado',
            ]);

        $assinatura->refresh();

        $this->assertSame('inadimplente', $assinatura->status);
        $this->assertSame('OVERDUE', $assinatura->billing_status);
        $this->assertDatabaseHas('billing_webhook_events', [
            'provider' => 'asaas',
            'event_id' => 'evt_001',
            'status' => 'processado',
        ]);
    }

    public function test_asaas_webhook_rejects_invalid_token(): void
    {
        config()->set('billing.providers.asaas.webhook_token', 'token-seguro');

        $this->postJson('/api/billing/webhooks/asaas', [
            'id' => 'evt_002',
            'event' => 'PAYMENT_RECEIVED',
        ], [
            'asaas-access-token' => 'token-invalido',
        ])->assertUnauthorized();
    }
}
