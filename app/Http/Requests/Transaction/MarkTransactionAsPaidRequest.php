<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MarkTransactionAsPaidRequest extends FormRequest
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
                'exists:transactions,id'
            ],
            'is_paid' => [
                'required',
                'boolean'
            ],
            'payer_id' => [
                'required',
                'uuid',
                'exists:users,id'
            ]
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID da parcela',
            'is_paid' => 'Foi pago',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }
}
