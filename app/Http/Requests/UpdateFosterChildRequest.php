<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFosterChildRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => [
                'sometimes',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
            ],
            'fullname' => ['sometimes', 'string', 'max:150'],
            'nickname' => ['sometimes', 'string', 'max:10'],
            'birth_place' => ['sometimes', 'string', 'max:50'],
            'birth_date' => ['sometimes', 'date'],
            'gender' => ['sometimes', 'string', 'size:1', 'in:M,F'],
        ];
    }
}
