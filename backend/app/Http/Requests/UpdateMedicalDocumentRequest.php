<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $document = $this->route('medicalDocument');

        return auth()->user()->can(
            'update',
            $document
        );
    }

    public function rules(): array
    {
        return [

            'file' => [
                'nullable',
                'file',
                'max:10240',
            ],

            'document_type' => [
                'sometimes',
                'in:lab_report,xray,mri,ct_scan,prescription,other,appointment_upload',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ];
    }
}