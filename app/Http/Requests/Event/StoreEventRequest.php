<?php

namespace App\Http\Requests\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Validator;
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
            'group.*' => ['array:title,description,total_amount,has_installment,quantity_installment,due_date,participant,is_split'],
            'group.*.title' => ['required', 'string'],
            'group.*.description' => ['sometimes', 'string'],
            'group.*.total_amount' => ['required', 'numeric', 'min:0.01'],
            'group.*.is_split' => ['required', 'boolean'],
            'group.*.has_installment' => ['required_if:group.*.is_split,false', 'boolean'],
            'group.*.quantity_installment' => ['required_if_accepted:group.*.has_installment', 'nullable', 'integer', 'min:2'],
            'group.*.due_date' => ['required', 'date'],
            'group.*.participant' => ['sometimes', 'array'],
            'group.*.participant.*' => ['required', 'uuid', 'exists:users,id']
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator)
            {
                $groups = $this->input('group') !== null
                    ? $this->input('group')
                    : [];
                
                foreach ($groups as $key => $group)
                {
                    if (!is_array($group))
                    {   
                        continue;
                    }

                    if ($group['is_split'] === true && $group['has_installment'] === true)
                    {
                        $validator->errors()->add(
                            "group.$key", 
                            "has_installment and is_split fields can't be true simultaneously"
                        );
                    }
                }
            }
        ];
    }
}
