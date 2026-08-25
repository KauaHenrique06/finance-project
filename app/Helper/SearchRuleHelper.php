<?php

namespace App\Helper;

class SearchRuleHelper
{
    public static function rules(): array
    {
        return [
            'search' => [
                'nullable',
                'sometimes',
                'string'
            ]
        ];
    }

    public static function prepareForValidation($query, $defaultParam = '')
    {
        return [
            'search' => $query->query('search', $defaultParam)
        ];
    }
}
