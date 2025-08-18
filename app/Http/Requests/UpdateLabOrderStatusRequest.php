<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLabOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('labOrder')) ?? false;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['created', 'received', 'testing', 'completed', 'archived'])],
        ];
    }
}
