<?php

namespace App\Http\Requests\Transaction;

use App\Helper\PageRuleHelper;
use App\Helper\PerPageRuleHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexTransactionByGroupIdRequest extends FormRequest
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
            ...PageRuleHelper::rules(),
            ...PerPageRuleHelper::rules(),
            'id' => [
                'required',
                'uuid',
                'exists:group_transaction,id'
            ]
        ];
    }

    public function attributes(): array
    {
        return [
            ...PageRuleHelper::attributes(),
            ...PerPageRuleHelper::attributes(),
            'id' => 'ID do grupo'
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            ...PageRuleHelper::prepareForValidation($this),
            ...PerPageRuleHelper::prepareForValidation($this),
            'id' => $this->route('id')
        ]);
    }
}
