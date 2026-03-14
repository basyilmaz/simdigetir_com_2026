<?php

namespace App\Domain\Notifications\Gateways;

use App\Domain\Notifications\Contracts\NotificationChannelGateway;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class NetgsmSmsGateway implements NotificationChannelGateway
{
    public function send(string $target, array $payload): array
    {
        $sandbox = (bool) config('services_integrations.sms.providers.netgsm.sandbox', true);
        if ($sandbox) {
            return [
                'status' => 'sent',
                'provider_message_id' => 'NETGSM-SB-'.Str::upper(Str::random(8)),
                'error_message' => null,
            ];
        }

        $baseUrl = (string) config('services_integrations.sms.providers.netgsm.base_url');
        $username = (string) config('services_integrations.sms.providers.netgsm.username');
        $password = (string) config('services_integrations.sms.providers.netgsm.password');
        $header = (string) config('services_integrations.sms.providers.netgsm.header');
        $body = (string) ($payload['body'] ?? '');

        if ($baseUrl === '' || $username === '' || $password === '' || $header === '') {
            return [
                'status' => 'failed',
                'provider_message_id' => null,
                'error_message' => 'Netgsm config is incomplete.',
            ];
        }

        try {
            $response = Http::timeout(10)->post(rtrim($baseUrl, '/').'/sms/send', [
                'usercode' => $username,
                'password' => $password,
                'gsmno' => preg_replace('/[^0-9]/', '', $target),
                'message' => $body,
                'msgheader' => $header,
            ]);

            if (! $response->successful()) {
                return [
                    'status' => 'failed',
                    'provider_message_id' => null,
                    'error_message' => 'Netgsm request failed: '.$response->status(),
                ];
            }

            $body = trim((string) $response->body());
            $isSuccess = $body !== '' && preg_match('/^20|^00|^OK/i', $body) === 1;
            $providerMessageId = null;
            if ($body !== '') {
                $parts = preg_split('/\s+|,|;/', $body);
                $providerMessageId = $parts[1] ?? $parts[0] ?? null;
            }

            if (! $isSuccess) {
                return [
                    'status' => 'failed',
                    'provider_message_id' => $providerMessageId,
                    'error_message' => 'Netgsm rejected message: '.$body,
                ];
            }

            return [
                'status' => 'sent',
                'provider_message_id' => $providerMessageId ?: 'NETGSM-'.Str::upper(Str::random(8)),
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
