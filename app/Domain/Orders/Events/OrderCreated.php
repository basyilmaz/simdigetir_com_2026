<?php

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Shared\Events\AbstractDomainEvent;

class OrderCreated extends AbstractDomainEvent
{
    public function __construct(
        public readonly int $orderId,
        public readonly int $customerId,
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'orders.order_created';
    }

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'customer_id' => $this->customerId,
            'state' => OrderState::Draft->value,
        ];
    }
}

