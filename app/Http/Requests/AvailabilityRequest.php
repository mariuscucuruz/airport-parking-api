<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvailabilityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dateStart' => 'bail|required|date_format:Y-m-d|after:tomorrow',
            'dateEnd' => 'bail|required|date_format:Y-m-d|after:dateStart',
        ];
    }

    public function messages(): array
    {
        return [
            'after' => 'Dates must be in the future',
        ];
    }
}
