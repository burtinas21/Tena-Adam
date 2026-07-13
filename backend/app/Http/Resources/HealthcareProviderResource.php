<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HealthcareProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'license_number' => $this->license_number,

            'consultation_fee' => $this->consultation_fee,

            'years_experience' => $this->years_experience,

            'practice_start_date' => $this->practice_start_date,

            'bio' => $this->bio,

            'profile_picture_url' => $this->profile_picture_url,

            'is_verified' => $this->is_verified,

            'is_telehealth_available' => $this->is_telehealth_available,

            'user' => [

                'id' => $this->user->id,

                'first_name' => $this->user->first_name,

                'last_name' => $this->user->last_name,

                'email' => $this->user->email,

                'phone' => $this->user->phone,

            ],

            'department' => [

                'id' => $this->department->id,

                'name' => $this->department->name,

            ],

            'hospital' => [

                'id' => $this->hospital->id,

                'name' => $this->hospital->name,

            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}