<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
                'string'
            ],
            'description' => [
                'required',
                'string'
            ],
            'has_installment' => [
                'required',
                'boolean'
            ],
            'quantity_installment' => [
                'required_if:has_installment,true',
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

    public function attributes(): array
    {
        return [
            'title' => 'Título',
            'description' => 'Descrição',
            'has_installment' => 'Tem parcela',
            'quantity_installment' => 'Quantidade de parcelas',
            'total_amount' => 'Valor total',
            'due_date' => 'Data do vencimento',
        ];
    }
}
