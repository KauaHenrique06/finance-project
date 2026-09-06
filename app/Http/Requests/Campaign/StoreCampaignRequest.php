<?php

namespace App\Http\Requests\Campaign;

use App\Enum\CampaignStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCampaignRequest extends FormRequest
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
                'max:50'
            ],
            'description' => [
                'nullable',
                'sometimes',
                'string'
            ],
            'status' => [
                'sometimes',
                new Enum(CampaignStatusEnum::class)
            ],
            'limit' => [
                'nullable',
                'sometimes',
                'numeric',
                'min:50'
            ],
            'due_date' => [
                'nullable',
                'sometimes',
                'date'
            ]
        ];
    }
}
