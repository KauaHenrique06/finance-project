<?php

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
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
            'event_id' => [
                'required',
                'uuid',
                'exists:events,id'
            ],
            'title' => [
                'required',
                'string'
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'is_split' => [
                'required',
                'boolean'
            ],
            'has_installment' => [
                'required_if:is_split,false',
                'boolean'
            ],
            'quantity_installment' => [
                'required_if_accepted:has_installment',
                'nullable',
                'integer',
                'min:2'
            ],
            'total_amount' => [
                'required',
                'numeric',
                'min:0.01'
            ],
            'due_date' => [
                'required',
                'date'
            ],
            'participant' => [
                'sometimes',
                'array'
            ],
            'participant.*' => [
                'required',
                'uuid',
                'exists:users,id'
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
            'event_id' => $this->route('id'),
            'has_installment' => filter_var($this->input('has_installment', false), FILTER_VALIDATE_BOOLEAN),
            ...$this->has('is_split')
                ? ['is_split' => filter_var($this->input('is_split'), FILTER_VALIDATE_BOOLEAN)]
                : []
        ]);
    }
}
