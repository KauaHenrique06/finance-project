<?php

namespace App\Enum;

enum ContributionStatusEnum: string
{
    case RECEIVED = 'received';
    case REFUND_IN_PROGRESS = 'refund_in_progress';
    case PARTIALLY_REFUNDED = 'partially_refunded';
    case REFUNDED = 'refunded';

    public static function translate(string $value): string
    {
        return match ($value)
        {
            self::RECEIVED->value => 'Recebido',
            self::REFUND_IN_PROGRESS->value => 'Estorno em andamento',
            self::PARTIALLY_REFUNDED->value => 'Estornado parcialmente',
            self::REFUNDED->value => 'Estornado',
            default => $value
        };
    }

    public function label(): string
    {
        return self::translate($this->value);
    }
}
