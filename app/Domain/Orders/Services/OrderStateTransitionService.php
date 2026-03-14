<?php

namespace App\Domain\Orders\Services;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Support\OrderStateTransitionMap;
use App\Models\Order;
use App\Models\OrderStateLog;
use Illuminate\Support\Facades\DB;

class OrderStateTransitionService
{
    public function transition(
        Order $order,
        OrderState $toState,
        ?string $actorType = null,
        ?int $actorId = null,
        ?string $reason = null,
        array $metadata = []
    ): Order {
        return DB::transaction(function () use ($order, $toState, $actorType, $actorId, $reason, $metadata) {
            /** @var Order|null $locked */
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->first();
            if (! $locked) {
                throw new InvalidOrderTransitionException('Order bulunamadi.');
            }

            $fromState = OrderState::from((string) $locked->state);

            // Idempotent: same target transition returns current state without new log.
            if ($fromState === $toState) {
                return $locked;
            }

            if (! OrderStateTransitionMap::canTransition($fromState, $toState)) {
                throw new InvalidOrderTransitionException(
                    sprintf('Gecersiz transition: %s -> %s', $fromState->value, $toState->value)
                );
            }

            $locked->state = $toState->value;

            if ($toState === OrderState::PendingPayment) {
                $locked->payment_state = 'pending';
            } elseif ($toState === OrderState::Paid) {
                $locked->payment_state = 'succeeded';
            } elseif ($toState === OrderState::Failed) {
                $locked->payment_state = 'failed';
            }

            $locked->save();

            OrderStateLog::query()->create([
                'order_id' => $locked->id,
                'from_state' => $fromState->value,
                'to_state' => $toState->value,
                'actor_type' => $actorType,
                'actor_id' => $actorId,
                'reason' => $reason,
                'metadata' => $metadata,
                'created_at' => now(),
            ]);

            return $locked;
        });
    }
}

