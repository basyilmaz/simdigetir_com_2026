<?php

namespace App\Domain\Finance\Services;

use App\Models\Courier;
use App\Models\CourierWalletEntry;
use App\Models\OrderAssignment;
use App\Models\SettlementBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SettlementService
{
    public function run(?int $courierId = null, int $commissionRateBps = 1000): SettlementBatch
    {
        return DB::transaction(function () use ($courierId, $commissionRateBps) {
            $batch = SettlementBatch::query()->create([
                'batch_no' => $this->nextBatchNo(),
                'status' => 'closed',
                'currency' => 'TRY',
                'closed_at' => now(),
            ]);

            $netTotal = 0;
            $assignments = OrderAssignment::query()
                ->where('status', 'completed')
                ->when($courierId !== null, fn ($q) => $q->where('courier_id', $courierId))
                ->with(['order', 'courier'])
                ->get();

            foreach ($assignments as $assignment) {
                if (! $assignment->order || ! $assignment->courier) {
                    continue;
                }

                $existing = CourierWalletEntry::query()
                    ->where('order_assignment_id', $assignment->id)
                    ->where('entry_type', 'earning')
                    ->exists();

                if ($existing) {
                    continue;
                }

                $gross = (int) $assignment->order->total_amount;
                $commission = -1 * (int) floor($gross * max(0, $commissionRateBps) / 10000);
                $net = $gross + $commission;

                $this->appendEntry($assignment->courier, [
                    'order_id' => $assignment->order_id,
                    'order_assignment_id' => $assignment->id,
                    'settlement_batch_id' => $batch->id,
                    'entry_type' => 'earning',
                    'amount' => $gross,
                    'currency' => (string) ($assignment->order->currency ?: 'TRY'),
                    'metadata' => ['source' => 'completed_assignment'],
                    'entry_at' => now(),
                ]);

                $this->appendEntry($assignment->courier, [
                    'order_id' => $assignment->order_id,
                    'order_assignment_id' => $assignment->id,
                    'settlement_batch_id' => $batch->id,
                    'entry_type' => 'commission',
                    'amount' => $commission,
                    'currency' => (string) ($assignment->order->currency ?: 'TRY'),
                    'metadata' => ['rate_bps' => $commissionRateBps],
                    'entry_at' => now(),
                ]);

                $netTotal += $net;
            }

            $batch->net_amount = $netTotal;
            $batch->save();

            return $batch;
        });
    }

    private function appendEntry(Courier $courier, array $payload): CourierWalletEntry
    {
        $lastBalance = (int) (CourierWalletEntry::query()
            ->where('courier_id', $courier->id)
            ->latest('id')
            ->value('balance_after') ?? 0);

        $amount = (int) $payload['amount'];
        $payload['courier_id'] = $courier->id;
        $payload['balance_after'] = $lastBalance + $amount;

        return CourierWalletEntry::query()->create($payload);
    }

    private function nextBatchNo(): string
    {
        return 'SET'.now()->format('YmdHis').Str::upper(Str::random(5));
    }
}

