<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalDocument extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'summary',
        'version',
        'is_published',
        'published_at',
        'updated_by',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(LegalDocumentVersion::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

