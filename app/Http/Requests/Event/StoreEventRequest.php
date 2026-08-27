<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:100'
            ],
            'description' => [
                'nullable',
                'sometimes',
                'string'
            ],
            'instance_id' => [
                'nullable',
                'uuid',
                'exists:whatsapp_instances,id'
            ],
            'group' => ['sometimes', 'array'],
            'group.*' => ['array:title,description,total_amount,has_installment,quantity_installment,due_date,participant'],
            'group.*.title' => ['required', 'string'],
            'group.*.description' => ['sometimes', 'string'],
            'group.*.total_amount' => ['required', 'numeric', 'min:0.01'],
            'group.*.has_installment' => ['required', 'boolean'],
            'group.*.quantity_installment' => ['required_if:group.*.has_installment,true', 'integer'],
            'group.*.due_date' => ['required', 'date'],
            'group.*.participant' => ['sometimes', 'array'],
            'group.*.participant.*' => ['required', 'uuid', 'exists:users,id']
        ];
    }
}
