<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    /**
     * Transition a single order to a new status.
     * Wraps the update in a transaction and validates the business rule.
     */
    public function transition(Order $order, string $newStatus): Order
    {
        if (! $order->canTransitionTo($newStatus)) {
            throw ValidationException::withMessages([
                'status' => "La orden #{$order->order_number} no permite el cambio de «{$order->status}» a «{$newStatus}».",
            ]);
        }

        return DB::transaction(function () use ($order, $newStatus) {
            $order->update([
                'status'            => $newStatus,
                'status_changed_at' => now(),
            ]);

            return $order->fresh();
        });
    }

    /**
     * Bulk-transition a collection of orders.
     * Uses SELECT FOR UPDATE to prevent double-processing under concurrency.
     *
     * @return array{updated: int, skipped: int, errors: list<string>}
     */
    public function bulkTransition(Collection $records, string $newStatus): array
    {
        $result = ['updated' => 0, 'skipped' => 0, 'errors' => []];

        DB::transaction(function () use ($records, $newStatus, &$result) {
            $locked = Order::whereIn('id', $records->pluck('id'))
                ->lockForUpdate()
                ->get();

            foreach ($locked as $order) {
                if (! $order->canTransitionTo($newStatus)) {
                    $result['skipped']++;
                    $result['errors'][] = "#{$order->order_number}: «{$order->status}» → «{$newStatus}» no está permitido.";
                    continue;
                }

                $order->update([
                    'status'            => $newStatus,
                    'status_changed_at' => now(),
                ]);

                $result['updated']++;
            }
        });

        return $result;
    }
}
