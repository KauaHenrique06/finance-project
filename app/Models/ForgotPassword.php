<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForgotPassword extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'user_id',
        'expires_at',
        'access_token',
        'used'
    ];

    protected function casts() {
        return [
            'user_id' => 'string',
            'expires_at' => 'date',
            'access_token' => 'string',
            'used' => 'boolean'
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
 