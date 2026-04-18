<?php

namespace App\Services\Billing\Asaas;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use RuntimeException;

class AsaasClient
{
    public function __construct(
        private readonly HttpFactory $http,
    ) {
    }

    public function get(string $uri, array $query = []): array
    {
        return $this->request()->get($uri, $query)->throw()->json();
    }

    public function post(string $uri, array $payload = []): array
    {
        return $this->request()->post($uri, $payload)->throw()->json();
    }

    public function put(string $uri, array $payload = []): array
    {
        return $this->request()->put($uri, $payload)->throw()->json();
    }

    private function request(): PendingRequest
    {
        $apiKey = (string) config('billing.providers.asaas.api_key');
        $baseUrl = (string) config('billing.providers.asaas.base_url');

        if ($apiKey === '') {
            throw new RuntimeException('A chave da API do Asaas nao foi configurada.');
        }

        return $this->http
            ->acceptJson()
            ->contentType('application/json')
            ->baseUrl(rtrim($baseUrl, '/'))
            ->withHeader('access_token', $apiKey)
            ->timeout(15);
    }
}
