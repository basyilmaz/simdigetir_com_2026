<?php

namespace Modules\Landing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingSectionItem extends Model
{
    protected $fillable = [
        'section_id',
        'item_key',
        'payload',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'payload' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingPageSection::class, 'section_id');
    }
}
