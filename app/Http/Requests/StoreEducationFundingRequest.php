<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEducationFundingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foster_child_id' => ['required', 'string', 'exists:foster_child,id'],
            'funding_source' => ['required', 'string', 'in:government_scholarship,private_scholarship,family_support,self_funded,other'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'funding_start_date' => ['required', 'date'],
            'funding_end_date' => ['nullable', 'date', 'after_or_equal:funding_start_date'],
            'monthly_tuition_fee' => ['nullable', 'numeric', 'min:0'],
            'urgent_needs' => ['nullable', 'string', 'max:5000'],
            'funding_status' => ['required', 'string', 'in:active,inactive,pending'],
        ];
    }
}
