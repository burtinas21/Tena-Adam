<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'appointment_id' => $this->appointment_id,

            'patient_id' => $this->patient_id,

            'hospital_id' => $this->hospital_id,

            'amount' => $this->amount,

            'currency' => $this->currency,

            'status' => $this->status,

            'payment_method' => $this->payment_method,

            'transaction_id' => $this->transaction_id,

            'reference' => $this->reference,

            'payment_date' => $this->payment_date,

            'metadata' => $this->metadata,

            'appointment' => $this->whenLoaded('appointment'),

            'patient' => $this->whenLoaded('patient'),

            'hospital' => $this->whenLoaded('hospital'),

            'invoice' => InvoiceResource::make(
                $this->whenLoaded('invoice')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}