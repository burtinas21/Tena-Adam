<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Report::class);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'type' => [
                'required',
                'in:appointment,patient,doctor,revenue,telehealth,custom',
            ],

            'query' => [
                'required',
                'string',
            ],

            'parameters' => [
                'nullable',
                'array',
            ],

            'schedule' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}