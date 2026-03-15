<?php

namespace App\Domain\Notifications\Services;

use App\Domain\Notifications\Support\NotificationTemplateCatalog;
use App\Models\NotificationTemplate;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Modules\Checkout\Services\CheckoutContentResolver;
use Throwable;

class OrderLifecycleNotificationService
{
    public function dispatchOrderCreated(Order $order): void
    {
        $this->dispatch(
            eventKey: 'orders.order_created',
            order: $order,
            targets: $this->buildTargets([
                $order->pickup_phone,
                $order->customer?->phone,
            ])
        );
    }

    public function dispatchBankTransferPending(Order $order): void
    {
        $this->dispatch(
            eventKey: 'orders.payment_pending_bank_transfer',
            order: $order,
            targets: $this->buildTargets([
                $order->pickup_phone,
                $order->customer?->phone,
            ])
        );
    }

    public function dispatchPickupCompleted(Order $order): void
    {
        $this->dispatch(
            eventKey: 'orders.pickup_completed',
            order: $order,
            targets: $this->buildTargets([
                $order->pickup_phone,
                $order->dropoff_phone,
                $order->customer?->phone,
            ])
        );
    }

    public function dispatchDeliveryCompleted(Order $order): void
    {
        $this->dispatch(
            eventKey: 'orders.delivery_completed',
            order: $order,
            targets: $this->buildTargets([
                $order->pickup_phone,
                $order->dropoff_phone,
                $order->customer?->phone,
            ])
        );
    }

    /**
     * @param  array<int, string|null>  $phones
     * @return array<int, array{channel:string,target:string}>
     */
    private function buildTargets(array $phones): array
    {
        $targets = [];
        $seen = [];

        foreach ($phones as $phone) {
            $value = trim((string) $phone);
            if ($value === '') {
                continue;
            }

            $normalized = $this->normalizePhone($value);
            if (strlen($normalized) < 10 || isset($seen[$normalized])) {
                continue;
            }

            $seen[$normalized] = true;
            $targets[] = [
                'channel' => 'sms',
                'target' => $value,
            ];
        }

        return $targets;
    }

    /**
     * @param  array<int, array{channel:string,target:string}>  $targets
     */
    private function dispatch(string $eventKey, Order $order, array $targets): void
    {
        if ($targets === []) {
            return;
        }

        $order->loadMissing('customer');
        $this->ensureSmsTemplateExists($eventKey);

        foreach ($targets as $target) {
            try {
                app(NotificationOrchestrator::class)->dispatch(
                    eventKey: $eventKey,
                    targets: [$target],
                    context: $this->buildContext($order, (string) $target['target'])
                );
            } catch (Throwable $e) {
                Log::warning('Order lifecycle notification dispatch failed.', [
                    'event_key' => $eventKey,
                    'order_id' => $order->id,
                    'target' => $target['target'] ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function buildContext(Order $order, string $targetPhone): array
    {
        $context = [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'pickup_address' => (string) $order->pickup_address,
            'dropoff_address' => (string) $order->dropoff_address,
            'pickup_name' => (string) ($order->pickup_name ?? ''),
            'dropoff_name' => (string) ($order->dropoff_name ?? ''),
            'total_amount' => number_format(((int) $order->total_amount) / 100, 2, ',', '.').' '.(string) $order->currency,
            'payment_method' => (string) ($order->payment_method ?? ''),
            'payment_state' => (string) ($order->payment_state ?? ''),
            'track_url' => $this->buildTrackUrl($order, $targetPhone),
        ];

        if ((string) ($order->payment_method ?? '') === 'bank_transfer') {
            $context = array_merge($context, $this->buildBankTransferContext());
        }

        return $context;
    }

    private function buildTrackUrl(Order $order, string $targetPhone): string
    {
        return route('checkout.tracking', [
            'order_no' => $order->order_no,
            'phone' => $this->normalizePhone($targetPhone),
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', trim($phone)) ?? '';
    }

    private function ensureSmsTemplateExists(string $eventKey): void
    {
        $defaultTemplate = NotificationTemplateCatalog::defaultSmsTemplates()[$eventKey] ?? null;
        if (! $defaultTemplate) {
            return;
        }

        $template = NotificationTemplate::query()->firstOrCreate(
            ['event_key' => $eventKey, 'channel' => 'sms'],
            [
                'subject' => null,
                'body' => $defaultTemplate['body'],
                'is_active' => true,
                'variables' => $defaultTemplate['variables'],
            ]
        );

        $legacyBody = NotificationTemplateCatalog::legacyTemplateBodies()[$eventKey] ?? null;
        if ($legacyBody !== null && (string) $template->body === $legacyBody) {
            $template->forceFill([
                'body' => $defaultTemplate['body'],
                'variables' => $defaultTemplate['variables'],
            ])->save();
        }
    }

    /**
     * @return array<string, string>
     */
    private function buildBankTransferContext(): array
    {
        $instructions = app(CheckoutContentResolver::class)->bankTransferInstructions();

        return [
            'bank_transfer_title' => (string) ($instructions['title'] ?? ''),
            'bank_transfer_body' => (string) ($instructions['body'] ?? ''),
            'bank_transfer_bank_name' => (string) ($instructions['bank_name'] ?? ''),
            'bank_transfer_account_holder' => (string) ($instructions['account_holder'] ?? ''),
            'bank_transfer_iban' => (string) ($instructions['iban'] ?? ''),
            'bank_transfer_reference_note' => (string) ($instructions['reference_note'] ?? ''),
            'bank_transfer_instruction' => $this->formatBankTransferInstruction($instructions),
        ];
    }

    /**
     * @param  array<string, mixed>  $instructions
     */
    private function formatBankTransferInstruction(array $instructions): string
    {
        $parts = [
            $instructions['title'] ?? null,
            ! empty($instructions['bank_name']) ? 'Banka: '.$instructions['bank_name'] : null,
            ! empty($instructions['account_holder']) ? 'Hesap Sahibi: '.$instructions['account_holder'] : null,
            ! empty($instructions['iban']) ? 'IBAN: '.$instructions['iban'] : null,
            $instructions['reference_note'] ?? null,
        ];

        return trim(implode(' ', array_filter(array_map(
            static fn ($value) => trim((string) $value),
            $parts
        ))));
    }
}
