<?php

namespace App\Domain\Notifications\Contracts;

interface NotificationChannelGateway
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{status:string,provider_message_id:?string,error_message:?string}
     */
    public function send(string $target, array $payload): array;
}

