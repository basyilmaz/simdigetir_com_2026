<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitemapEntry extends Model
{
    protected $fillable = [
        'path',
        'changefreq',
        'priority',
        'is_active',
        'lastmod_at',
    ];

    protected $casts = [
        'priority' => 'float',
        'is_active' => 'boolean',
        'lastmod_at' => 'datetime',
    ];
}

