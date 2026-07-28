<?php

namespace App\Services;

use App\Models\Refund;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RefundService
{
    /**
     * Create refund request.
     */
    public function create(array $data): Refund
    {
        return DB::transaction(function () use ($data) {

            return Refund::create([

                'payment_id' => $data['payment_id'],

                'amount' => $data['amount'],

                'reason' => $data['reason'] ?? null,

                'status' => 'pending',

            ]);

        });
    }

    /**
     * Approve refund.
     */
    public function approve(Refund $refund, string $userId): Refund
    {
        $refund->update([

            'status' => 'approved',

            'approved_by' => $userId,

        ]);

        return $refund->fresh();
    }

    /**
     * Reject refund.
     */
    public function reject(Refund $refund): Refund
    {
        $refund->update([

            'status' => 'rejected',

        ]);

        return $refund->fresh();
    }

    /**
     * Process refund.
     */
    public function process(Refund $refund): Refund
    {
        DB::transaction(function () use ($refund) {

            $refund->update([

                'status' => 'processed',

                'refund_date' => now(),

            ]);

            $refund->payment->update([

                'status' => 'refunded',

            ]);

        });

        return $refund->fresh();
    }
}