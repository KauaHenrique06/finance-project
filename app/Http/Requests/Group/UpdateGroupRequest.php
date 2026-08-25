<?php

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'uuid',
                'exists:groups,id'
            ],
            'title' => [
                'sometimes',
                'string'
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'has_installment' => [
                'sometimes',
                'boolean'
            ],
            'quantity_installment' => [
                'required_if:has_installment,true',
                'nullable',
                'integer',
                'min:2'
            ],
            'total_amount' => [
                'sometimes',
                'numeric',
                'min:0.01'
            ],
            'due_date' => [
                'sometimes',
                'date'
            ]
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }
}
