<?php

return [
    'name' => 'Checkout',
    'session_ttl_minutes' => (int) env('CHECKOUT_SESSION_TTL_MINUTES', 1440),
];
