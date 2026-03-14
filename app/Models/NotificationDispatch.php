<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationDispatch extends Model
{
    protected $fillable = [
        'notification_template_id',
        'event_key',
        'channel',
        'target',
        'status',
        'error_message',
        'payload',
        'dispatched_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'dispatched_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }
}

