<?php

namespace Modules\AdsCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdConnection extends Model
{
    protected $fillable = [
        'platform',
        'name',
        'external_account_id',
        'status',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'token_expires_at' => 'datetime',
        'meta' => 'array',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(AdCampaign::class);
    }
}
