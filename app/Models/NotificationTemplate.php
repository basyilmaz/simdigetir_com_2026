<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'event_key',
        'channel',
        'subject',
        'body',
        'is_active',
        'variables',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    public function dispatches(): HasMany
    {
        return $this->hasMany(NotificationDispatch::class);
    }
}

