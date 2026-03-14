<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'email',
        'vehicle_type',
        'status',
        'application_notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availability(): HasOne
    {
        return $this->hasOne(CourierAvailability::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CourierDocument::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function walletEntries(): HasMany
    {
        return $this->hasMany(CourierWalletEntry::class);
    }
}
