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

            // Aggregates — loaded via withAvg/withCount or loadAvg/loadCount
            'average_rating' => round($this->reviews_avg_rating ?? 0, 1),

            'total_reviews' => $this->reviews_count ?? 0,

            // User — always present (provider requires a user)
            'user' => $this->when($this->relationLoaded('user') && $this->user, [
                'id'         => $this->user?->id,
                'first_name' => $this->user?->first_name,
                'last_name'  => $this->user?->last_name,
                'email'      => $this->user?->email,
                'phone'      => $this->user?->phone,
            ]),

            // Department — nullable
            'department' => $this->when(
                $this->relationLoaded('department') && $this->department,
                fn () => [
                    'id'   => $this->department->id,
                    'name' => $this->department->name,
                ]
            ),

            // Hospital — nullable
            'hospital' => $this->when(
                $this->relationLoaded('hospital') && $this->hospital,
                fn () => [
                    'id'   => $this->hospital->id,
                    'name' => $this->hospital->name,
                ]
            ),

            // Specializations — only included when the relation is loaded
            'specializations' => $this->when(
                $this->relationLoaded('specializations'),
                fn () => $this->specializations->map(fn ($s) => [
                    'id'   => $s->id,
                    'name' => $s->name,
                ])->values()
            ),

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}
