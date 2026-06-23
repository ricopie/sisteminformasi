<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChildEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'education_status' => ['sometimes', Rule::enum(EducationStatus::class)],
            'school_level' => ['sometimes', Rule::enum(SchoolLevel::class)],
            'school_name' => ['sometimes', 'string', 'max:20'],
            'current_grade' => ['sometimes', 'string', 'max:12'],
            'major' => ['sometimes', 'string'],
            'student_id_number' => [
                'sometimes',
                'numeric',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
        ];
    }
}
