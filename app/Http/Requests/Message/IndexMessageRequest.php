<?php

namespace App\Http\Requests\Message;

use App\Helper\PageRuleHelper;
use App\Helper\PerPageRuleHelper;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexMessageRequest extends FormRequest
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
            ...PageRuleHelper::rules(),
            ...PerPageRuleHelper::rules(),
        ];
    }

    public function attributes()
    {
         return [
            ...PageRuleHelper::attributes(),
            ...PerPageRuleHelper::attributes(),
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
            ...PageRuleHelper::prepareForValidation($this),
            ...PerPageRuleHelper::prepareForValidation($this),
        ]);
    }
}
