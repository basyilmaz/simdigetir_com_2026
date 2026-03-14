<?php

namespace App\Domain\Orders\Events;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Shared\Events\AbstractDomainEvent;

class OrderDelivered extends AbstractDomainEvent
{
    public function __construct(
        public readonly int $orderId,
        public readonly ?string $proofType = null,
    ) {
        parent::__construct();
    }

    public function eventName(): string
    {
        return 'orders.order_delivered';
    }

    public function payload(): array
    {
        return [
            'order_id' => $this->orderId,
            'proof_type' => $this->proofType,
            'state' => OrderState::Delivered->value,
        ];
    }
}

