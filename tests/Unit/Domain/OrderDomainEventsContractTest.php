<?php

namespace Tests\Unit\Domain;

use App\Domain\Orders\Events\OrderAssigned;
use App\Domain\Orders\Events\OrderCreated;
use App\Domain\Orders\Events\OrderDelivered;
use App\Domain\Orders\Events\OrderPaid;
use App\Domain\Shared\Contracts\DomainEvent;
use PHPUnit\Framework\TestCase;

class OrderDomainEventsContractTest extends TestCase
{
    public function test_order_events_implement_domain_event_contract(): void
    {
        $events = [
            new OrderCreated(orderId: 101, customerId: 55),
            new OrderPaid(orderId: 101, paymentReference: 'pay_ref_1'),
            new OrderAssigned(orderId: 101, courierId: 88),
            new OrderDelivered(orderId: 101, proofType: 'otp'),
        ];

        foreach ($events as $event) {
            $this->assertInstanceOf(DomainEvent::class, $event);
            $this->assertNotSame('', $event->eventName());
            $this->assertIsArray($event->payload());
            $this->assertArrayHasKey('order_id', $event->payload());
            $this->assertNotNull($event->occurredAt());
        }
    }
}

