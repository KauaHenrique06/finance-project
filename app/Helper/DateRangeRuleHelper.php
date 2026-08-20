<?php

namespace App\Helper;

class DateRangeRuleHelper
{
    public static function rules(): array
    {
        return [
            'start_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d'
            ],
            'end_date' => [
                'nullable',
                'date',
                'date_format:Y-m-d',
                'after:start_date'
            ]
        ];
    }

    public static function attributes(): array
    {
        return [
            'start_date' => 'Data inicial',
            'end_date' => 'Data final',
        ];
    }
}
