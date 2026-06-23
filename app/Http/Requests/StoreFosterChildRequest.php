<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFosterChildRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
            ],
            'fullname' => ['required', 'string', 'max:150'],
            'nickname' => ['required', 'string', 'max:10'],
            'birth_place' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string', 'size:1', 'in:M,F'],
        ];
    }
}
