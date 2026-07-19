<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewRatingResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'id' =>
                $this->id,


            'rating' =>
                $this->rating,


            'comment' =>
                $this->comment,


            'is_anonymous' =>
                $this->is_anonymous,


            'patient' => [

                'id' =>
                    $this->patient?->id,

                'name' =>
                    $this->patient?->user?->first_name
                    .' '.
                    $this->patient?->user?->last_name,

            ],


            'doctor' => [

                'id' =>
                    $this->doctor?->id,

                'name' =>
                    $this->doctor?->user?->first_name
                    .' '.
                    $this->doctor?->user?->last_name,

            ],


            'appointment_id' =>
                $this->appointment_id,


            'created_at' =>
                $this->created_at,


            'updated_at' =>
                $this->updated_at,

        ];

    }

}