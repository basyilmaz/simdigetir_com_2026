<?php

namespace Modules\AdsGoogle\Services;

use Illuminate\Support\Str;
use Modules\AdsCore\Models\AdConnection;

class GoogleOAuthService
{
    public function beginConnection(AdConnection $connection, string $redirectUrl): array
    {
        $state = Str::random(40);
        $meta = $connection->meta ?? [];
        $meta['oauth_state'] = $state;

        $connection->forceFill([
            'status' => 'connecting',
            'meta' => $meta,
        ])->save();

        return [
            'state' => $state,
            'auth_url' => 'https://accounts.google.com/o/oauth2/v2/auth?state='.$state.'&redirect_uri='.urlencode($redirectUrl),
        ];
    }

    public function completeConnection(AdConnection $connection, string $state, string $authorizationCode): array
    {
        $expected = (string) data_get($connection->meta, 'oauth_state', '');
        if ($expected === '' || ! hash_equals($expected, $state)) {
            return [
                'success' => false,
                'error' => 'invalid_oauth_state',
            ];
        }

        $maskedCode = substr(sha1($authorizationCode), 0, 20);
        $connection->forceFill([
            'status' => 'connected',
            'external_account_id' => $connection->external_account_id ?: 'google-account-'.$connection->id,
            'access_token' => 'google-access-'.$maskedCode,
            'refresh_token' => 'google-refresh-'.$maskedCode,
            'token_expires_at' => now()->addDays(30),
            'meta' => array_merge($connection->meta ?? [], ['oauth_state' => null]),
        ])->save();

        return [
            'success' => true,
            'token_expires_at' => optional($connection->token_expires_at)->toIso8601String(),
        ];
    }
}
