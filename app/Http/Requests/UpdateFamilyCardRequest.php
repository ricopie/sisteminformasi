<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'family_card_number' => [
                'sometimes',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
            ],
            'head_of_family_name' => ['sometimes', 'string', 'max:150'],
            'street' => ['sometimes', 'string', 'max:150'],
            'rt' => ['sometimes', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'rw' => ['sometimes', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'village' => ['sometimes', 'string', 'max:150'],
            'sub_district' => ['sometimes', 'string', 'max:150'],
            'city' => ['sometimes', 'string', 'max:150'],
            'province' => ['sometimes', 'string', 'max:150'],
            'postal_code' => ['sometimes', 'string', 'size:5', 'regex:/^[0-9]+$/'],
        ];
    }
}
