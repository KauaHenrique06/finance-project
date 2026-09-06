<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignContribution extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'campaign_id',
        'contributor_id_id',
        'status',
        'amount',
        'paid_at'
    ];

    protected function casts(): array
    {
        return [
            'campaign_id' => 'string',
            'contributor_id_id' => 'string',
            'status' => 'string',
            'amount' => 'decimal:2',
            'paid_at' => 'date'
        ];
    }

    public function contributor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contributor_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
