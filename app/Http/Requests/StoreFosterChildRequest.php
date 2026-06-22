<?php

namespace App\Http\Requests;

use App\Models\FosterChild;
use App\Rules\UniqueBlindIndex;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFosterChildRequest extends FormRequest
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
            'nik' => [
                'required',
                'string',
                'size:16',
                'regex:/^[0-9]+$/',
                new UniqueBlindIndex(FosterChild::class, 'nik_hash'),
            ],
            'fullname' => ['required', 'string', 'max:150'],
            'nickname' => ['required', 'string', 'max:10'],
            'birth_place' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string', 'size:1', 'in:M,F'],
        ];
    }
}
