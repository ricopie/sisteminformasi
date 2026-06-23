<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEducationHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_level' => ['sometimes', Rule::enum(SchoolLevel::class)],
            'school_name' => ['sometimes', 'string', 'max:255'],
            'admission_year' => ['sometimes', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'graduation_year' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:' . date('Y')],
            'status' => ['sometimes', Rule::enum(EducationStatus::class)],
            'dropout_reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
