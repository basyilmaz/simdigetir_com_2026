<?php

namespace Modules\Checkout\Models;

use App\Models\PricingQuote;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutSession extends Model
{
    protected $fillable = [
        'token',
        'customer_id',
        'pricing_quote_id',
        'status',
        'current_step',
        'payload',
        'expires_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'token';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function pricingQuote(): BelongsTo
    {
        return $this->belongsTo(PricingQuote::class, 'pricing_quote_id');
    }
}
