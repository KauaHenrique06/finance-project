<?php

namespace App\Http\Requests\User;

use App\Rules\ValidCpfRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => [
                'nullable',
                'sometimes',
                'string',
            ],
            'email' => [
                'nullable',
                'sometimes',
                'email',
            ],
            'cpf' => [
                'nullable',
                'sometimes',
                'string',
                'unique:users,cpf',
                new ValidCpfRule()
            ],
            'profile_pic' => [
                'nullable',
                'sometimes',
                'mimes:jpg,jpeg,png,webp',
                'max:10000'
            ]
        ];
    }

    public function prepareForValidation(): array
    {
        return [
            $this->merge([
                'id' => $this->route('id')
            ])
        ];
    }
}
