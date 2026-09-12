<?php

namespace App\Http\Requests\Asaas;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubAccountRequest extends FormRequest
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
            'income_value' => [
                'required',
                'numeric',
                'min:0'
            ]
        ];
    }
}
