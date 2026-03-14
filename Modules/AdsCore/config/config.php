<?php

return [
    'name' => 'AdsCore',
    'providers' => [
        'google' => 'Modules\\AdsGoogle\\Services\\GoogleAdsProvider',
        'meta' => 'Modules\\AdsMeta\\Services\\MetaAdsProvider',
        'mock' => 'Modules\\AdsCore\\Services\\Providers\\MockAdsProvider',
    ],
    'default_provider' => 'mock',
    'conversion' => [
        'enabled' => (bool) env('ADS_CONVERSION_ENABLED', true),
        'auto_push' => (bool) env('ADS_CONVERSION_AUTO_PUSH', false),
        'auto_push_mode' => env('ADS_CONVERSION_AUTO_PUSH_MODE', 'sync'), // sync|queue
        'auto_push_platforms' => array_values(array_filter(array_map(
            static fn (string $platform): string => trim(strtolower($platform)),
            explode(',', (string) env('ADS_CONVERSION_AUTO_PUSH_PLATFORMS', 'meta'))
        ))),
    ],
    'meta' => [
        'enabled' => (bool) env('META_CAPI_ENABLED', true),
        'graph_base_url' => env('META_GRAPH_BASE_URL', 'https://graph.facebook.com'),
        'graph_version' => env('META_GRAPH_VERSION', 'v22.0'),
        'pixel_id' => env('META_PIXEL_ID', ''),
        'access_token' => env('META_CAPI_ACCESS_TOKEN', ''),
        'test_event_code' => env('META_CAPI_TEST_EVENT_CODE', ''),
        'timeout_seconds' => (int) env('META_CAPI_TIMEOUT_SECONDS', 10),
    ],
];
