<?php

namespace App\Enum;

enum CampaignStatusEnum: string
{
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case PAUSED = 'paused';
    
    public static function translate(string $value): string
    {
        return match ($value)
        {
            self::IN_PROGRESS->value => 'Em progresso',
            self::FINISHED->value => 'Finalziado',
            self::PAUSED->value => 'Pausado',
            default => $value
        };
    }    

    public function label(): string
    {
        return self::translate($this->value);
    }
}
