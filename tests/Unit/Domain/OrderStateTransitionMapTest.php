<?php

namespace Tests\Unit\Domain;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Support\OrderStateTransitionMap;
use PHPUnit\Framework\TestCase;

class OrderStateTransitionMapTest extends TestCase
{
    public function test_all_states_exist_in_transition_map(): void
    {
        $map = OrderStateTransitionMap::map();

        foreach (OrderState::cases() as $state) {
            $this->assertArrayHasKey($state->value, $map);
        }
    }

    public function test_happy_path_transitions_are_allowed(): void
    {
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::Draft, OrderState::PendingPayment));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::PendingPayment, OrderState::Paid));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::Paid, OrderState::Assigned));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::Assigned, OrderState::PickedUp));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::PickedUp, OrderState::Delivered));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::Delivered, OrderState::Closed));
        $this->assertTrue(OrderStateTransitionMap::canTransition(OrderState::Failed, OrderState::PendingPayment));
    }

    public function test_invalid_transition_is_blocked(): void
    {
        $this->assertFalse(OrderStateTransitionMap::canTransition(OrderState::Draft, OrderState::Delivered));
        $this->assertFalse(OrderStateTransitionMap::canTransition(OrderState::Closed, OrderState::Paid));
    }
}
