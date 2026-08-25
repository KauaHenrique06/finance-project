<?php

namespace App\Helper;

class PageRuleHelper
{
    public static function rules(): array
    {
        return [
            'page' => [
                'nullable',
                'sometimes',
                'integer'
            ]
        ];
    }

    public static function prepareForValidation($query)
    {
        return [
            'page' => $query->query('page', 1)
        ];
    }
}
