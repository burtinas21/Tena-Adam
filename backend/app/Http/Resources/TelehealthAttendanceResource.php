<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TelehealthAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'session_id' => $this->session_id,
            'user_id' => $this->user_id,

            'joined_at' => $this->joined_at,
            'left_at' => $this->left_at,

            'device_type' => $this->device_type,
            'ip_address' => $this->ip_address,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
