<?php

namespace App\Domain\Orders\Support;

use App\Domain\Orders\Enums\OrderState;

class OrderStateTransitionMap
{
    /**
     * @return array<string, array<string>>
     */
    public static function map(): array
    {
        return [
            OrderState::Draft->value => [
                OrderState::PendingPayment->value,
                OrderState::Cancelled->value,
            ],
            OrderState::PendingPayment->value => [
                OrderState::Paid->value,
                OrderState::Failed->value,
                OrderState::Cancelled->value,
            ],
            OrderState::Paid->value => [
                OrderState::Assigned->value,
                OrderState::Cancelled->value,
            ],
            OrderState::Assigned->value => [
                OrderState::Paid->value,
                OrderState::PickedUp->value,
                OrderState::Cancelled->value,
            ],
            OrderState::PickedUp->value => [
                OrderState::Delivered->value,
                OrderState::Failed->value,
            ],
            OrderState::Delivered->value => [
                OrderState::Closed->value,
            ],
            OrderState::Closed->value => [],
            OrderState::Cancelled->value => [
                OrderState::PendingPayment->value,
            ],
            OrderState::Failed->value => [
                OrderState::PendingPayment->value,
            ],
        ];
    }

    public static function canTransition(OrderState $from, OrderState $to): bool
    {
        return in_array($to->value, self::map()[$from->value] ?? [], true);
    }
}
