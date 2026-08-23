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
            'group.*' => ['array:title,description,total_amount'],
            'group.*.title' => ['required', 'string'],
            'group.*.description' => ['sometimes', 'string'],
            'group.*.total_amount' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
