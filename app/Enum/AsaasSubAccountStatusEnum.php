<?php

namespace App\Enum;

enum AsaasSubAccountStatusEnum: string
{
    case PENDING = 'pending';
    case AWAITING_APPROVAL = 'awaiting_approval';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public static function translate(string $value): string
    {
        return match ($value)
        {
            self::PENDING->value => 'Pendente',
            self::AWAITING_APPROVAL->value => 'Aguardando aprovação',
            self::APPROVED->value => 'Aprovado',
            self::REJECTED->value => 'Rejeitado',
            default => $value
        };
    }

    public function label(): string
    {
        return self::translate($this->value);
    }
}
