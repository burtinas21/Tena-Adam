<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $this->user()->can('update', $report);
    }

    public function rules(): array
    {
        return [

            'name' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'type' => [
                'sometimes',
                'in:appointment,patient,doctor,revenue,telehealth,custom',
            ],

            'query' => [
                'sometimes',
                'string',
            ],

            'parameters' => [
                'sometimes',
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