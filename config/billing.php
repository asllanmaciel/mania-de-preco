<?php

return [
    'default_provider' => env('BILLING_PROVIDER', 'asaas'),

    'providers' => [
        'asaas' => [
            'label' => 'Asaas',
            'status' => 'mvp',
            'base_url' => env('ASAAS_BASE_URL', 'https://api-sandbox.asaas.com/v3'),
            'api_key' => env('ASAAS_API_KEY'),
            'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),
            'subscription_billing_type' => env('ASAAS_SUBSCRIPTION_BILLING_TYPE', 'UNDEFINED'),
        ],
        'mercado_pago' => [
            'label' => 'Mercado Pago',
            'status' => 'roadmap',
            'base_url' => env('MERCADO_PAGO_BASE_URL', 'https://api.mercadopago.com'),
            'access_token' => env('MERCADO_PAGO_ACCESS_TOKEN'),
            'public_key' => env('MERCADO_PAGO_PUBLIC_KEY'),
            'webhook_secret' => env('MERCADO_PAGO_WEBHOOK_SECRET'),
        ],
    ],
];
