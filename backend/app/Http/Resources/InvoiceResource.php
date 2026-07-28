<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'payment_id' => $this->payment_id,

            'invoice_number' => $this->invoice_number,

            'due_date' => $this->due_date,

            'status' => $this->status,

            'pdf_url' => $this->pdf_url,

            'payment' => PaymentResource::make(
                $this->whenLoaded('payment')
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}