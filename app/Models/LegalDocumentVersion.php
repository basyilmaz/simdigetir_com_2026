<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalDocumentVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'legal_document_id',
        'version',
        'content',
        'summary',
        'is_published',
        'published_at',
        'changed_by',
        'note',
        'created_at',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(LegalDocument::class, 'legal_document_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

