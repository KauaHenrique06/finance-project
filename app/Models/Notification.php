<?php

namespace App\Models;

use App\Traits\HasUuidV7;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasUuidV7;

    protected $fillable = [
        'data',
        'read_at',
        'message',
        'type',
        'user_id',
    ]; 

    protected function casts() {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
            'message' => 'array',
            'type' => 'string',
            'user_id' => 'string',
        ];
    }
}
