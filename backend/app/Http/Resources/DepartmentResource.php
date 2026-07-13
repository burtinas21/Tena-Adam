<?php

namespace App\Http\Resources;


use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class DepartmentResource extends JsonResource
{


    public function toArray(Request $request): array
    {


        return [

            'id' => $this->id,


            'hospital_id'
                => $this->hospital_id,


            'name'
                => $this->name,


            'description'
                => $this->description,


            'head_doctor_id'
                => $this->head_doctor_id,


            'parent_department_id'
                => $this->parent_department_id,


            'is_active'
                => $this->is_active,


            'created_at'
                => $this->created_at,

        ];

    }


}