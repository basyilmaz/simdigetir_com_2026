<?php

namespace App\Domain\Notifications\Gateways;

use App\Mail\TemplatedNotificationMail;
use App\Domain\Notifications\Contracts\NotificationChannelGateway;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class LaravelMailGateway implements NotificationChannelGateway
{
    public function send(string $target, array $payload): array
    {
        try {
            $subject = (string) ($payload['subject'] ?? config('app.name', 'Notification'));
            $body = (string) ($payload['body'] ?? '');
            $context = (array) ($payload['context'] ?? []);

            Mail::to($target)->send(new TemplatedNotificationMail(
                subjectLine: $subject,
                bodyText: $body,
                context: $context
            ));

            return [
                'status' => 'sent',
                'provider_message_id' => 'MAIL-'.Str::upper(Str::random(10)),
                'error_message' => null,
            ];
        } catch (Throwable $e) {
            return [
                'status' => 'failed',
                'provider_message_id' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }
}
