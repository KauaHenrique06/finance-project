<?php

namespace App\Http\Requests\Auth;

use App\Rules\ValidCpfRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'cpf' => [
                'required',
                'string',
                'unique:users,cpf',
                new ValidCpfRule
            ],
            'password' => [
                'required',
                Password::min(6)
                    ->mixedCase()
                    ->symbols()
                    ->numbers()
            ],
            'phone' => [
                'required',
                'string',
                'max:13'
            ],
            'birth_date' => [
                'required',
                'date'
            ],
            'profile_pic' => [
                'nullable',
                'sometimes',
                'image',
                'mimes:jpeg,png,webp,jpg',
                'max:10000'
            ],
            'address' => [
                'required',
                'array'
            ],
            'address.cep' => [
                'required_with:address',
                'string'
            ],
            'address.number' => [
                'required_with:address',
                'integer'
            ],
        ];
    }
}
