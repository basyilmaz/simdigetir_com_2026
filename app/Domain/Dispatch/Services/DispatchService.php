<?php

namespace App\Domain\Dispatch\Services;

use App\Domain\Orders\Enums\OrderState;
use App\Domain\Orders\Exceptions\InvalidOrderTransitionException;
use App\Domain\Orders\Services\OrderStateTransitionService;
use App\Models\Courier;
use App\Models\DispatchDecision;
use App\Models\Order;
use App\Models\OrderAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DispatchService
{
    /**
     * @param  array<int>  $excludeCourierIds
     */
    public function autoAssign(Order $order, ?int $decidedBy = null, array $excludeCourierIds = []): ?OrderAssignment
    {
        return DB::transaction(function () use ($order, $decidedBy, $excludeCourierIds) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! in_array($order->state, [OrderState::Paid->value], true)) {
                $this->logDecision($order, null, 'auto_assign', 'skipped', 'order_not_paid', $decidedBy);
                return null;
            }

            $courier = $this->pickBestCourier($order, $excludeCourierIds);
            if (! $courier) {
                $this->logDecision($order, null, 'auto_assign', 'failed', 'no_available_courier', $decidedBy);
                return null;
            }

            $assignment = OrderAssignment::query()->create([
                'order_id' => $order->id,
                'courier_id' => $courier->id,
                'status' => 'pending',
                'assignment_type' => 'auto',
                'assigned_at' => now(),
                'created_by' => $decidedBy,
            ]);

            app(OrderStateTransitionService::class)->transition(
                order: $order,
                toState: OrderState::Assigned,
                actorType: 'dispatch',
                actorId: $decidedBy,
                reason: 'auto_assignment',
                metadata: ['assignment_id' => $assignment->id]
            );

            $this->logDecision($order, $courier, 'auto_assign', 'assigned', 'best_score', $decidedBy, 100);
            return $assignment;
        });
    }

    public function manualAssign(Order $order, Courier $courier, string $reason, ?int $decidedBy = null): OrderAssignment
    {
        return DB::transaction(function () use ($order, $courier, $reason, $decidedBy) {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            // Cancel active assignment if exists.
            OrderAssignment::query()
                ->where('order_id', $order->id)
                ->whereIn('status', ['pending', 'accepted'])
                ->update(['status' => 'cancelled']);

            if ($order->state === OrderState::Assigned->value) {
                app(OrderStateTransitionService::class)->transition(
                    order: $order,
                    toState: OrderState::Paid,
                    actorType: 'dispatch',
                    actorId: $decidedBy,
                    reason: 'manual_reassignment_reset'
                );
            }

            $assignment = OrderAssignment::query()->create([
                'order_id' => $order->id,
                'courier_id' => $courier->id,
                'status' => 'pending',
                'assignment_type' => 'manual',
                'assigned_at' => now(),
                'assignment_note' => $reason,
                'created_by' => $decidedBy,
            ]);

            if ($order->state !== OrderState::Assigned->value) {
                app(OrderStateTransitionService::class)->transition(
                    order: $order,
                    toState: OrderState::Assigned,
                    actorType: 'dispatch',
                    actorId: $decidedBy,
                    reason: 'manual_assignment',
                    metadata: ['assignment_id' => $assignment->id]
                );
            }

            $this->logDecision($order, $courier, 'manual_assign', 'assigned', $reason, $decidedBy, 100);
            return $assignment;
        });
    }

    public function reassignOverdue(int $slaMinutes = 15, ?int $decidedBy = null): int
    {
        $count = 0;
        $cutoff = now()->subMinutes(max(1, $slaMinutes));

        /** @var Collection<int, OrderAssignment> $overdue */
        $overdue = OrderAssignment::query()
            ->where('status', 'pending')
            ->whereNotNull('assigned_at')
            ->where('assigned_at', '<=', $cutoff)
            ->with('order')
            ->get();

        foreach ($overdue as $assignment) {
            $order = $assignment->order;
            if (! $order || $order->state !== OrderState::Assigned->value) {
                continue;
            }

            DB::transaction(function () use ($assignment, $order, $decidedBy, &$count) {
                $assignment->status = 'cancelled';
                $assignment->assignment_note = 'sla_timeout_reassignment';
                $assignment->save();

                app(OrderStateTransitionService::class)->transition(
                    order: $order,
                    toState: OrderState::Paid,
                    actorType: 'dispatch',
                    actorId: $decidedBy,
                    reason: 'sla_timeout_reassignment_reset'
                );

                    $newAssignment = $this->autoAssign($order, $decidedBy, [$assignment->courier_id]);
                if ($newAssignment) {
                    $newAssignment->assignment_type = 'reassignment';
                    $newAssignment->save();
                    $count++;
                }
            });
        }

        return $count;
    }

    /**
     * @param  array<int>  $excludeCourierIds
     */
    private function pickBestCourier(Order $order, array $excludeCourierIds = []): ?Courier
    {
        return Courier::query()
            ->where('status', 'approved')
            ->when(! empty($excludeCourierIds), function ($query) use ($excludeCourierIds) {
                $query->whereNotIn('id', $excludeCourierIds);
            })
            ->whereHas('availability', function ($query) {
                $query->where('is_online', true);
            })
            ->withCount([
                'assignments as active_assignments_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'accepted']);
                },
            ])
            ->orderBy('active_assignments_count')
            ->orderBy('id')
            ->first();
    }

    private function logDecision(
        Order $order,
        ?Courier $courier,
        string $type,
        string $result,
        string $reason,
        ?int $decidedBy,
        ?int $score = null
    ): void {
        DispatchDecision::query()->create([
            'order_id' => $order->id,
            'courier_id' => $courier?->id,
            'decision_type' => $type,
            'result' => $result,
            'score' => $score,
            'reason' => $reason,
            'metadata' => [],
            'decided_by' => $decidedBy,
            'created_at' => now(),
        ]);
    }
}
