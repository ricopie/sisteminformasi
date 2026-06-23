<?php

namespace App\Http\Requests;

use App\Enums\EducationStatus;
use App\Enums\SchoolLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChildEducationRequest extends FormRequest
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
            'education_status' => ['required', Rule::enum(EducationStatus::class)],
            'school_level' => ['required', Rule::enum(SchoolLevel::class)],
            'school_name' => ['required', 'string', 'max:20'],
            'current_grade' => ['required', 'string', 'max:12'],
            'major' => ['required', 'string'],
            'student_id_number' => [
                'required',
                'numeric',
                'max:20',
                'regex:/^[0-9]+$/',
            ],
        ];
    }
}
