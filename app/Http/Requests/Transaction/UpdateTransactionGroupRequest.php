<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionGroupRequest extends FormRequest
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
                'exists:group_transaction,id'
            ],
            'title' => [
                'sometimes',
                'string'
            ],
            'description' => [
                'sometimes',
                'string'
            ],
            'has_installment' => [
                'sometimes',
                'boolean'
            ],
            'quantity_installment' => [
                'required_if:has_installment,true',
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

    public function attributes(): array
    {
        return [
            'id' => 'ID do grupo',
            'title' => 'Título',
            'description' => 'Descrição',
            'has_installment' => 'Tem parcela',
            'quantity_installment' => 'Quantidade de parcelas',
            'total_amount' => 'Valor total',
            'due_date' => 'Data do vencimento',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }
}
