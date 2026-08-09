<?php

namespace App\Helper;

class PerPageRuleHelper
{
    public static function rules(): array
    {
        return [
            'perPage' => [
                'nullable',
                'sometimes',
                'integer'
            ]
        ];
    }

    public static function attributes(): array
    {
        return [
            'perPage' => 'Por página',
        ];
    }

    public static function prepareForValidation($query)
    {
        return [
            'perPage' => $query->query('perPage', 10)
        ];
    }
}
