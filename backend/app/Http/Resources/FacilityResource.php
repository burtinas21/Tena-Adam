<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class FacilityResource extends JsonResource
{


    public function toArray(Request $request): array
    {


        return [

            'id'
                => $this->id,


            'hospital_id'
                => $this->hospital_id,


            'name'
                => $this->name,


            'type'
                => $this->type,


            'status'
                => $this->status,


            'description'
                => $this->description,


            'created_at'
                => $this->created_at,


        ];


    }


}