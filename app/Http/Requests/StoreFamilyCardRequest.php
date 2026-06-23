<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyCardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'family_card_number' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
            ],
            'head_of_family_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:150'],
            'rt' => ['required', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'rw' => ['required', 'string', 'max:3', 'regex:/^[0-9]+$/'],
            'village' => ['required', 'string', 'max:150'],
            'sub_district' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:150'],
            'province' => ['required', 'string', 'max:150'],
            'postal_code' => ['required', 'string', 'size:5', 'regex:/^[0-9]+$/'],
        ];
    }
}
