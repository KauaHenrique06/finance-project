<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
                'exists:events,id'
            ],
            'title' => [
                'sometimes',
                'string',
                'max:100'
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'instance_id' => [
                'sometimes',
                'uuid',
                'exists:whatsapp_instances,id'
            ],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id') 
        ]);
    }
}
