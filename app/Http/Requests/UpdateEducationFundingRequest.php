<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationFundingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'funding_source' => ['sometimes', 'string', 'in:government_scholarship,private_scholarship,family_support,self_funded,other'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3', 'alpha'],
            'funding_start_date' => ['sometimes', 'date'],
            'funding_end_date' => ['nullable', 'date', 'after_or_equal:funding_start_date'],
            'monthly_tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'urgent_needs' => ['nullable', 'string', 'max:5000'],
            'funding_status' => ['sometimes', 'string', 'in:active,inactive,pending'],
        ];
    }
}
