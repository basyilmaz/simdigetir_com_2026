<?php

namespace Modules\Landing\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'meta',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'is_active' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(LandingPageSection::class, 'page_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(LandingSectionRevision::class, 'page_id');
    }
}
