<?php

namespace App\Http\Requests\Group;

use Illuminate\Contracts\Validation\ValidationRule;
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
                'required_if:is_split,true',
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
            ]
        ];
    }

    public function prepareForValidation()
    {
        // assure this fields has required
        $this->merge([
            'event_id' => $this->route('id'),
            'is_split' => $this->boolean('boolean'),
            'has_installmanet' => $this->boolean('boolean')
        ]);
    }
}
