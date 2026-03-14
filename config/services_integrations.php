<?php

return [
    'sms' => [
        'default' => env('SMS_DEFAULT_PROVIDER', 'mock'),
        'providers' => [
            'mock' => [],
            'netgsm' => [
                'username' => env('NETGSM_USERNAME', ''),
                'password' => env('NETGSM_PASSWORD', ''),
                'header' => env('NETGSM_HEADER', ''),
                'base_url' => env('NETGSM_BASE_URL', 'https://api.netgsm.com.tr'),
                'sandbox' => env('NETGSM_SANDBOX', true),
            ],
        ],
    ],
    'maps' => [
        'google_maps_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    ],
];

