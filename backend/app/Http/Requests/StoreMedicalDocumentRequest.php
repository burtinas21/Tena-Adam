<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->can(
            'create',
            \App\Models\MedicalDocument::class
        );
    }

    public function rules(): array
    {
        return [

            'patient_id' => [
                'required',
                'exists:patients,id',
            ],

            'encounter_id' => [
                'nullable',
                'exists:medical_encounters,id',
            ],

            'file' => [
                'required',
                'file',
                'max:10240',
            ],

            'document_type' => [
                'required',
                'in:lab_report,xray,mri,ct_scan,prescription,other',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ];
    }
}