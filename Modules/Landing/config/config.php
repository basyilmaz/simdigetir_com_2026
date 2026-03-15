<?php

return [
    'name' => 'Landing',
    'quote_widget_enabled' => (bool) env('LANDING_QUOTE_WIDGET_ENABLED', true),
    'quote_widget' => [
        'request_timeout_seconds' => (float) env('LANDING_QUOTE_WIDGET_TIMEOUT_SECONDS', 8.2),
    ],
];
