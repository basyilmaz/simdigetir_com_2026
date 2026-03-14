<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optional Two Factor Authentication
    |--------------------------------------------------------------------------
    |
    | If a compatible Filament auth plugin provides "requiresTwoFactorAuthentication",
    | this flag enables it for the admin panel.
    |
    */
    'enable_optional_2fa' => (bool) env('ADMIN_PANEL_OPTIONAL_2FA', false),
];
