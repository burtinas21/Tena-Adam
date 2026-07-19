<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class ReportResource extends JsonResource
{

    /**
     * Transform resource into array.
     */
    public function toArray(Request $request): array
    {

        return [

            'id' =>
                $this->id,


            'name' =>
                $this->name,


            'type' =>
                $this->type,


            'parameters' =>
                $this->parameters,


            'schedule' =>
                $this->schedule,


            'last_run_at' =>
                $this->last_run_at,


            'is_active' =>
                $this->is_active,


            'created_by' =>
                $this->created_by,


            'created_at' =>
                $this->created_at,


            'updated_at' =>
                $this->updated_at,

        ];

    }

}