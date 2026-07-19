<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'action' => $this->action,

            'target_table' => $this->target_table,

            'target_id' => $this->target_id,

            'details' => $this->details,

            'ip_address' => $this->ip_address,

            'user_agent' => $this->user_agent,

            'created_at' => $this->created_at,

            'user' => [

                'id' => $this->user?->id,

                'name' => trim(
                    ($this->user?->first_name ?? '') . ' ' .
                    ($this->user?->last_name ?? '')
                ),

                'email' => $this->user?->email,

            ],

        ];
    }
}