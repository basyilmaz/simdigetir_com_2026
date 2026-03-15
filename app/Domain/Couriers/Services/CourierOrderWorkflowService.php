<?php

namespace App\Domain\Couriers\Services;

use App\Domain\Notifications\Services\OrderLifecycleNotificationService;
use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Models\OrderProof;
use App\Models\Order;
use App\Models\OrderAssignment;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CourierOrderWorkflowService
{
    public function accept(OrderAssignment $assignment, ?string $note = null): OrderAssignment
    {
        return DB::transaction(function () use ($assignment, $note) {
            $assignment = OrderAssignment::query()->lockForUpdate()->findOrFail($assignment->id);
            if ($assignment->status !== 'pending') {
                throw new RuntimeException('Assignment kabul edilemez durumda.');
            }

            $assignment->status = 'accepted';
            $assignment->accepted_at = now();
            $assignment->assignment_note = $note;
            $assignment->save();

            return $assignment;
        });
    }

    public function reject(OrderAssignment $assignment, string $reason): OrderAssignment
    {
        return DB::transaction(function () use ($assignment, $reason) {
            $assignment = OrderAssignment::query()->lockForUpdate()->with('order')->findOrFail($assignment->id);
            if (! in_array($assignment->status, ['pending', 'accepted'], true)) {
                throw new RuntimeException('Assignment reddedilemez durumda.');
            }

            $assignment->status = 'rejected';
            $assignment->rejected_at = now();
            $assignment->rejection_reason = $reason;
            $assignment->save();

            $order = $assignment->order;
            if ($order && $order->state === OrderState::Assigned->value) {
                app(OrderStateTransitionService::class)->transition(
                    order: $order,
                    toState: OrderState::Paid,
                    actorType: 'courier',
                    actorId: $assignment->courier_id,
                    reason: 'courier_rejected_assignment',
                    metadata: ['assignment_id' => $assignment->id]
                );
            }

            return $assignment;
        });
    }

    public function pickup(
        OrderAssignment $assignment,
        ?string $proofType = null,
        ?string $proofValue = null,
        ?string $fileUrl = null,
        array $metadata = []
    ): Order
    {
        $order = DB::transaction(function () use ($assignment, $proofType, $proofValue, $fileUrl, $metadata) {
            $assignment = OrderAssignment::query()->lockForUpdate()->with('order')->findOrFail($assignment->id);
            if ($assignment->status !== 'accepted') {
                throw new RuntimeException('Pickup icin assignment accepted olmali.');
            }

            $order = $assignment->order;
            if (! $order) {
                throw new RuntimeException('Order bulunamadi.');
            }

            if ($proofType !== null) {
                $this->createProof(
                    orderId: $order->id,
                    courierId: $assignment->courier_id,
                    stage: 'pickup',
                    proofType: $proofType,
                    proofValue: $proofValue,
                    fileUrl: $fileUrl,
                    metadata: $metadata
                );
            }

            return app(OrderStateTransitionService::class)->transition(
                order: $order,
                toState: OrderState::PickedUp,
                actorType: 'courier',
                actorId: $assignment->courier_id,
                reason: 'courier_pickup_confirmed',
                metadata: ['assignment_id' => $assignment->id]
            );
        });

        app(OrderLifecycleNotificationService::class)->dispatchPickupCompleted($order);

        return $order;
    }

    public function deliver(
        OrderAssignment $assignment,
        string $proofType,
        ?string $proofValue = null,
        ?string $fileUrl = null,
        array $metadata = []
    ): Order {
        $order = DB::transaction(function () use ($assignment, $proofType, $proofValue, $fileUrl, $metadata) {
            $assignment = OrderAssignment::query()->lockForUpdate()->with('order')->findOrFail($assignment->id);
            if (! in_array($assignment->status, ['accepted'], true)) {
                throw new RuntimeException('Teslimat icin assignment accepted olmali.');
            }

            $order = $assignment->order;
            if (! $order) {
                throw new RuntimeException('Order bulunamadi.');
            }
            if ($order->state !== OrderState::PickedUp->value) {
                throw new InvalidOrderTransitionException('Teslimat tamamlamak icin order picked_up olmalidir.');
            }

            if (! in_array($proofType, ['otp', 'signature', 'photo'], true)) {
                throw new RuntimeException('Gecersiz proof tipi.');
            }
            if ($proofType === 'otp' && ($proofValue === null || trim($proofValue) === '')) {
                throw new RuntimeException('OTP proof icin proof_value zorunludur.');
            }
            if (in_array($proofType, ['signature', 'photo'], true) && ($fileUrl === null || trim($fileUrl) === '')) {
                throw new RuntimeException('Signature/photo proof icin file_url zorunludur.');
            }

            $this->createProof(
                orderId: $order->id,
                courierId: $assignment->courier_id,
                stage: 'delivery',
                proofType: $proofType,
                proofValue: $proofValue,
                fileUrl: $fileUrl,
                metadata: $metadata
            );

            $updatedOrder = app(OrderStateTransitionService::class)->transition(
                order: $order,
                toState: OrderState::Delivered,
                actorType: 'courier',
                actorId: $assignment->courier_id,
                reason: 'courier_delivery_completed',
                metadata: ['assignment_id' => $assignment->id, 'proof_type' => $proofType]
            );

            $assignment->status = 'completed';
            $assignment->completed_at = now();
            $assignment->save();

            return $updatedOrder;
        });

        app(OrderLifecycleNotificationService::class)->dispatchDeliveryCompleted($order);

        return $order;
    }

    private function createProof(
        int $orderId,
        ?int $courierId,
        string $stage,
        string $proofType,
        ?string $proofValue = null,
        ?string $fileUrl = null,
        array $metadata = []
    ): void {
        if (! in_array($stage, ['pickup', 'delivery'], true)) {
            throw new RuntimeException('Gecersiz proof asamasi.');
        }

        if (! in_array($proofType, ['otp', 'signature', 'photo'], true)) {
            throw new RuntimeException('Gecersiz proof tipi.');
        }
        if ($proofType === 'otp' && ($proofValue === null || trim($proofValue) === '')) {
            throw new RuntimeException('OTP proof icin proof_value zorunludur.');
        }
        if (in_array($proofType, ['signature', 'photo'], true) && ($fileUrl === null || trim($fileUrl) === '')) {
            throw new RuntimeException('Signature/photo proof icin file_url zorunludur.');
        }

        OrderProof::query()->create([
            'order_id' => $orderId,
            'courier_id' => $courierId,
            'stage' => $stage,
            'proof_type' => $proofType,
            'proof_value' => $proofValue,
            'file_url' => $fileUrl,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
