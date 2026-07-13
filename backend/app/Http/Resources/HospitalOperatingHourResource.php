<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HospitalOperatingHourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'hospital_id'  => $this->hospital_id,
            'day_of_week'  => $this->day_of_week,
            'open_time'    => $this->open_time,
            'close_time'   => $this->close_time,
            'is_holiday'   => $this->is_holiday,
            'created_at'   => $this->created_at,
        ];
    }
}
