<?php

namespace App\Domain\Notifications\Services;

use App\Models\NotificationDispatch;
use App\Models\NotificationTemplate;
use Illuminate\Support\Collection;

class NotificationOrchestrator
{
    /**
     * @param  array<int, array{channel:string,target:string}>  $targets
     * @param  array<string,mixed>  $context
     * @return Collection<int, NotificationDispatch>
     */
    public function dispatch(string $eventKey, array $targets, array $context = []): Collection
    {
        $gatewayManager = app(NotificationChannelGatewayManager::class);
        $templates = NotificationTemplate::query()
            ->where('event_key', $eventKey)
            ->where('is_active', true)
            ->get()
            ->keyBy('channel');

        $result = collect();
        foreach ($targets as $target) {
            $channel = (string) ($target['channel'] ?? '');
            $address = (string) ($target['target'] ?? '');
            if ($channel === '' || $address === '') {
                continue;
            }

            /** @var NotificationTemplate|null $template */
            $template = $templates->get($channel);
            $payload = [
                'subject' => $template?->subject,
                'body' => $this->renderBody($template?->body ?? '', $context),
                'context' => $context,
            ];

            $gatewayResult = $template
                ? $gatewayManager->resolve($channel)->send($address, $payload)
                : ['status' => 'failed', 'provider_message_id' => null, 'error_message' => 'template_not_found'];

            $dispatch = NotificationDispatch::query()->create([
                'notification_template_id' => $template?->id,
                'event_key' => $eventKey,
                'channel' => $channel,
                'target' => $address,
                'status' => (string) ($gatewayResult['status'] ?? 'failed'),
                'error_message' => $gatewayResult['error_message'] ?? null,
                'payload' => array_merge($payload, [
                    'provider_message_id' => $gatewayResult['provider_message_id'] ?? null,
                ]),
                'dispatched_at' => now(),
            ]);

            $result->push($dispatch);
        }

        return $result;
    }

    /**
     * @param  array<string,mixed>  $context
     */
    private function renderBody(string $body, array $context): string
    {
        $rendered = $body;
        foreach ($context as $key => $value) {
            if (! is_scalar($value)) {
                continue;
            }
            $rendered = str_replace('{'.$key.'}', (string) $value, $rendered);
        }

        return $rendered;
    }
}
