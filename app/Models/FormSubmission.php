<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormSubmission extends Model
{
    protected $fillable = [
        'form_definition_id',
        'payload',
        'status',
        'request_ip',
        'page_url',
        'referrer',
        'user_agent',
        'assigned_to',
        'follow_up_at',
        'internal_note',
    ];

    protected $casts = [
        'payload' => 'array',
        'follow_up_at' => 'datetime',
    ];

    public function formDefinition(): BelongsTo
    {
        return $this->belongsTo(FormDefinition::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
