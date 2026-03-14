<?php

namespace Modules\Landing\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingSectionRevision extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'page_id',
        'section_id',
        'changed_by',
        'snapshot',
        'note',
        'created_at',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'created_at' => 'datetime',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(LandingPage::class, 'page_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(LandingPageSection::class, 'section_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
