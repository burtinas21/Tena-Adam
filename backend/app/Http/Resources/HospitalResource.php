<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class HospitalResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id' => $this->id,

            'name' => $this->name,

            'code' => $this->code,

            'address' => $this->address,

            'city' => $this->city,

            'region' => $this->region,

            'phone' => $this->phone,

            'email' => $this->email,

            'website' => $this->website,

            'logo_url' => $this->logo_url,

            'registration_number'
                => $this->registration_number,

            'is_active'
                => $this->is_active,

            // Department names + doctor count for patient view
            'departments' => $this->whenLoaded('departments', function () {
                return $this->departments->map(fn($d) => [
                    'id'   => $d->id,
                    'name' => $d->name,
                    'healthcare_providers_count'
                          => $d->healthcareProviders ? $d->healthcareProviders->count() : 0,
                ]);
            }),

            'facilities'
                => FacilityResource::collection(
                    $this->whenLoaded('facilities')
                ),

            // Whether any doctor in this hospital offers telemedicine
            'has_telehealth' => $this->whenLoaded('departments', function () {
                foreach ($this->departments as $dept) {
                    if ($dept->healthcareProviders) {
                        foreach ($dept->healthcareProviders as $p) {
                            if ($p->is_telehealth_available) return true;
                        }
                    }
                }
                return false;
            }),

            'total_doctors' => $this->whenLoaded('departments', function () {
                return $this->departments->sum(fn($d) =>
                    $d->healthcareProviders ? $d->healthcareProviders->count() : 0
                );
            }),

            'created_at'
                => $this->created_at,

        ];

    }

}