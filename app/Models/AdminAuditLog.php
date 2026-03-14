<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'changed_by',
        'request_ip',
        'request_url',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
