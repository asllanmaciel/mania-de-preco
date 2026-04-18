<?php

return [
    'default_provider' => env('BILLING_PROVIDER', 'asaas'),

    'providers' => [
        'asaas' => [
            'base_url' => env('ASAAS_BASE_URL', 'https://api-sandbox.asaas.com/v3'),
            'api_key' => env('ASAAS_API_KEY'),
            'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),
            'subscription_billing_type' => env('ASAAS_SUBSCRIPTION_BILLING_TYPE', 'UNDEFINED'),
        ],
    ],
];
