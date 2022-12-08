<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dateStart' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'after_or_equals:tomorrow'
            ],
            'dateEnd' => [
                'bail',
                'required',
                'date_format:Y-m-d',
                'after:dateStart'
            ],
            'email' => [
                'bail',
                'required',
                'email:rfc,dns'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'We need your email and the dates you want to book for.',
        ];
    }
}
