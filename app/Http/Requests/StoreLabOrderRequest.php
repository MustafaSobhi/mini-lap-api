<?php

namespace App\Http\Requests;

use App\Models\LabOrder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLabOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', LabOrder::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'patient_name' => ['required', 'string', 'min:3'],
            'test_code'    => ['required', 'alpha_dash'],
            'priority'     => ['nullable', Rule::in(['normal', 'urgent'])],
            'scheduled_at' => ['required', 'date'],
        ];
    }
}
