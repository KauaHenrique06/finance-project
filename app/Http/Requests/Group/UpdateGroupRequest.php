<?php

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
            'is_split' => [
                'required_with:total_amount,due_date,has_installment,quantity_installment',
                'boolean'
            ],
            'has_installment' => [
                'sometimes',
                'boolean'
            ],
            'quantity_installment' => [
                'required_if_accepted:has_installment',
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

    public function after(): array
    {
        return [
            function (Validator $validator)
            {
                if ($this->input('is_split') === true && $this->input('has_installment') === true)
                {
                    $validator->errors()->add(
                        'is_split',
                        "has_installment and is_split fields can't be true simultaneously"
                    );
                }
            }
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
            ...$this->has('is_split')
                ? ['is_split' => filter_var($this->input('is_split'), FILTER_VALIDATE_BOOLEAN)]
                : [],
            ...$this->has('has_installment')
                ? ['has_installment' => filter_var($this->input('has_installment'), FILTER_VALIDATE_BOOLEAN)]
                : []
        ]);
    }
}
