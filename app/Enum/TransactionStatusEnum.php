<?php

namespace App\Enum;

enum TransactionStatusEnum: string
{
    case PENDING = 'pending';
    case PAID = 'paid';

    public static function translate(string $value): string
    {
        return match($value) {
            self::PENDING->value => 'Pendente',
            self::PAID->value => 'Pago',
            default => $value
        };
    }

    public function label(): string
    {
        return self::translate($this->value);
    }
}
