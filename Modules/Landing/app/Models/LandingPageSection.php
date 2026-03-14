<?php

namespace Modules\Landing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPageSection extends Model
{
    protected $fillable = [
        'page_id',
        'key',
        'type',
        'title',
        'payload',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'payload' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'page_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LandingSectionItem::class, 'section_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(LandingSectionRevision::class, 'section_id');
    }
}
