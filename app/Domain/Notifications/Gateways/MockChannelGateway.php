<?php

namespace App\Domain\Notifications\Gateways;

use App\Domain\Notifications\Contracts\NotificationChannelGateway;
use Illuminate\Support\Str;

class MockChannelGateway implements NotificationChannelGateway
{
    public function send(string $target, array $payload): array
    {
        return [
            'status' => 'sent',
            'provider_message_id' => 'MOCK-'.Str::upper(Str::random(10)),
            'error_message' => null,
        ];
    }
}

