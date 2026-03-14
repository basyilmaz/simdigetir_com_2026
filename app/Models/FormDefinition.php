<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormDefinition extends Model
{
    protected $fillable = [
        'key',
        'title',
        'description',
        'schema',
        'target_type',
        'success_message',
        'rate_limit_per_minute',
        'is_active',
    ];

    protected $casts = [
        'schema' => 'array',
        'rate_limit_per_minute' => 'integer',
        'is_active' => 'boolean',
    ];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}

