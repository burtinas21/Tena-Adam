<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'patient_id' => $this->patient_id,

            'encounter_id' => $this->encounter_id,

            'file_name' => $this->file_name,

            'file_url' => $this->file_url,

            'file_type' => $this->file_type,

            'file_size' => $this->file_size,

            'document_type' => $this->document_type,

            'description' => $this->description,

            'uploaded_by' => [

                'id' => $this->uploader->id,

                'first_name' => $this->uploader->first_name,

                'last_name' => $this->uploader->last_name,

            ],

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}