<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_reports_runtime_checks(): void
    {
        $response = $this->getJson(route('health'))
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('checks.aplicacao.ok', true)
            ->assertJsonPath('checks.banco.ok', true)
            ->assertJsonPath('checks.cache.ok', true)
            ->assertJsonPath('checks.storage.ok', true)
            ->assertJsonStructure([
                'status',
                'verificado_em',
                'checks' => [
                    'aplicacao' => ['ok', 'mensagem'],
                    'banco' => ['ok', 'mensagem'],
                    'cache' => ['ok', 'mensagem'],
                    'storage' => ['ok', 'mensagem'],
                ],
            ]);

        $cacheControl = (string) $response->headers->get('Cache-Control');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
    }
}
