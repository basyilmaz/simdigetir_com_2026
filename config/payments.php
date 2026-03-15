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
        'paytr' => [
            'merchant_id' => env('PAYTR_MERCHANT_ID', ''),
            'merchant_key' => env('PAYTR_MERCHANT_KEY', ''),
            'merchant_salt' => env('PAYTR_MERCHANT_SALT', ''),
            'base_url' => env('PAYTR_BASE_URL', 'https://www.paytr.com'),
            'sandbox' => env('PAYTR_SANDBOX', true),
            'secret' => env('PAYTR_CALLBACK_SECRET', ''),
        ],
    ],
];
