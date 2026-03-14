<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CorporateAccount extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'tax_no',
        'tax_office',
        'invoice_email',
        'billing_address',
        'status',
        'contract_meta',
    ];

    protected $casts = [
        'contract_meta' => 'array',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'corporate_account_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CorporateAccountAddress::class);
    }
}

