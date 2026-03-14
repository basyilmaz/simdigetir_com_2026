<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierDocument extends Model
{
    protected $fillable = [
        'courier_id',
        'document_type',
        'file_url',
        'status',
        'review_note',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}

