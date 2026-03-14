<?php

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Shared\Events\AbstractDomainEvent;

class OrderAssigned extends AbstractDomainEvent
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $courierId,
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'orders.order_assigned';
    }

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'courier_id' => $this->courierId,
            'state' => OrderState::Assigned->value,
        ];
    }
}

