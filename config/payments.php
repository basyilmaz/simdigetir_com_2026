<?php

return [
    'default_provider' => env('PAYMENT_DEFAULT_PROVIDER', 'mockpay'),
    'providers' => [
        'mockpay' => [
            'secret' => env('PAYMENT_MOCKPAY_SECRET', 'mockpay-secret'),
        ],
        'iyzico' => [
            'api_key' => env('IYZICO_API_KEY', ''),
            'secret_key' => env('IYZICO_SECRET_KEY', ''),
            'base_url' => env('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'),
            'sandbox' => env('IYZICO_SANDBOX', true),
            'secret' => env('IYZICO_CALLBACK_SECRET', ''),
        ],
    ],
];
