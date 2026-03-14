<?php

namespace App\Domain\Notifications\Services;

use App\Domain\Notifications\Contracts\NotificationChannelGateway;
use App\Domain\Notifications\Gateways\LaravelMailGateway;
use App\Domain\Notifications\Gateways\MockChannelGateway;
use App\Domain\Notifications\Gateways\NetgsmSmsGateway;

class NotificationChannelGatewayManager
{
    public function resolve(string $channel): NotificationChannelGateway
    {
        $channel = strtolower(trim($channel));

        return match ($channel) {
            'sms' => $this->resolveSmsGateway(),
            'email' => app(LaravelMailGateway::class),
            'push' => app(MockChannelGateway::class),
            default => app(MockChannelGateway::class),
        };
    }

    private function resolveSmsGateway(): NotificationChannelGateway
    {
        $provider = (string) config('services_integrations.sms.default', 'mock');

        return match (strtolower(trim($provider))) {
            'netgsm' => app(NetgsmSmsGateway::class),
            default => app(MockChannelGateway::class),
        };
    }
}

