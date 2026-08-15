<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappInstance extends Model
{
    use HasUuidV7;
    
    protected $fillable = [
        'user_id',
        'name',
        'qrcode_code',
        'qrcode_base64',
        'status',
        'evolution_id',
        'number',
        'owner_jid',
        'connected_at'
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'name' => 'string',
            'qrcode_code' => 'string',
            'qrcode_base64' => 'string',
            'status' => 'string',
            'evolution_id' => 'string',
            'number' => 'string',
            'owner_jid' => 'string',
            'connected_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
