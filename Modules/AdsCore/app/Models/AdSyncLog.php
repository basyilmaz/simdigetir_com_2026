<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;

class AdSyncLog extends Model
{
    protected $fillable = [
        'platform',
        'action',
        'status',
        'target_type',
        'target_id',
        'error_message',
        'request_payload',
        'response_payload',
        'attempt_count',
        'processed_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'processed_at' => 'datetime',
    ];
}
