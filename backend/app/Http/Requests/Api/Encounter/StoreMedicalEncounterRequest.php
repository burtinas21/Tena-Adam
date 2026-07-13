<?php

namespace App\Http\Requests\Api\Encounter;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicalEncounterRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

            'patient_id' => [
                'required',
                'uuid',
                'exists:patients,id',
            ],

            'doctor_id' => [
                'required',
                'uuid',
                'exists:healthcare_providers,id',
            ],

            'hospital_id' => [
                'required',
                'uuid',
                'exists:hospitals,id',
            ],

            'appointment_id' => [
                'nullable',
                'uuid',
                'exists:appointments,id',
            ],

            'encounter_date' => [
                'required',
                'date',
            ],

            'chief_complaint' => [
                'required',
                'string',
            ],

            'history' => [
                'nullable',
                'string',
            ],

            'physical_exam' => [
                'nullable',
                'string',
            ],

            'assessment' => [
                'required',
                'string',
            ],

            'diagnosis' => [
                'required',
                'string',
            ],

            'diagnosis_icd10' => [
                'nullable',
                'string',
                'max:20',
            ],

            'treatment_plan' => [
                'nullable',
                'string',
            ],

            'clinical_notes' => [
                'nullable',
                'string',
            ],

            'follow_up_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

        ];
    }
    public function messages(): array
    {
        return [

            'patient_id.required' =>
                'Patient is required.',

            'doctor_id.required' =>
                'Doctor is required.',

            'hospital_id.required' =>
                'Hospital is required.',

            'chief_complaint.required' =>
                'Chief complaint is required.',

            'assessment.required' =>
                'Assessment is required.',

            'diagnosis.required' =>
                'Diagnosis is required.',

            'follow_up_date.after_or_equal' =>
                'Follow-up date cannot be in the past.',
        ];
    }
}