<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'payment_id' => $this->payment_id,

            'amount' => $this->amount,

            'reason' => $this->reason,

            'status' => $this->status,

            'approved_by' => $this->approved_by,

            'refund_date' => $this->refund_date,

            'payment' => PaymentResource::make(
                $this->whenLoaded('payment')
            ),

            'approver' => $this->whenLoaded('approver'),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}