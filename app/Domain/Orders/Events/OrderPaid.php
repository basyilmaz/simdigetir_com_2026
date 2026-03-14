<?php

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Shared\Events\AbstractDomainEvent;

class OrderPaid extends AbstractDomainEvent
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $paymentReference,
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'orders.order_paid';
    }

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'payment_reference' => $this->paymentReference,
            'state' => OrderState::Paid->value,
        ];
    }
}

