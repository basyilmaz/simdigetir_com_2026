<?php

namespace App\Support;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\RateLimiter;

class BulkActionRateLimiter
{
    public static function allow(string $actionKey, int $maxAttempts = 12, int $decaySeconds = 60): bool
    {
        $actor = (string) (auth()->id() ?: request()->ip() ?: 'guest');
        $key = "filament:bulk-action:{$actionKey}:{$actor}";

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return false;
        }

        RateLimiter::hit($key, $decaySeconds);

        return true;
    }

    public static function enforce(string $actionKey, int $maxAttempts = 12, int $decaySeconds = 60): bool
    {
        if (static::allow($actionKey, $maxAttempts, $decaySeconds)) {
            return true;
        }

        $actor = (string) (auth()->id() ?: request()->ip() ?: 'guest');
        $key = "filament:bulk-action:{$actionKey}:{$actor}";
        $seconds = RateLimiter::availableIn($key);

        Notification::make()
            ->title('Toplu işlem limiti aşıldı')
            ->body("Lütfen {$seconds} saniye sonra tekrar deneyin.")
            ->danger()
            ->send();

        return false;
    }
}
