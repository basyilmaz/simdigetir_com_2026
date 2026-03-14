<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CorporateAccountAddress extends Model
{
    protected $fillable = [
        'corporate_account_id',
        'label',
        'address',
        'lat',
        'lng',
        'is_default',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'is_default' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(CorporateAccount::class, 'corporate_account_id');
    }
}

