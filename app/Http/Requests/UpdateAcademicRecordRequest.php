<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_year' => ['sometimes', 'string', 'regex:/^\d{4}\/\d{4}$/'],
            'semester' => ['sometimes', 'string', 'in:odd,even'],
            'gpa' => ['sometimes', 'numeric', 'min:0', 'max:4'],
            'class_rank' => ['sometimes', 'integer', 'min:1'],
            'achievements' => ['nullable', 'string', 'max:5000'],
            'caregiver_notes' => ['nullable', 'string', 'max:5000'],
            'report_card_path' => ['nullable', 'string', 'max:255'],
        ];
    }
}
