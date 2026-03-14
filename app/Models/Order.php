<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'order_no',
        'state',
        'payment_state',
        'pickup_name',
        'pickup_phone',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'dropoff_name',
        'dropoff_phone',
        'dropoff_address',
        'dropoff_lat',
        'dropoff_lng',
        'scheduled_at',
        'distance_meters',
        'duration_seconds',
        'vehicle_type',
        'notes',
        'subtotal_amount',
        'discount_amount',
        'surge_amount',
        'total_amount',
        'currency',
        'price_breakdown',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'pickup_lat' => 'float',
        'pickup_lng' => 'float',
        'dropoff_lat' => 'float',
        'dropoff_lng' => 'float',
        'notes' => 'array',
        'price_breakdown' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function stateLogs(): HasMany
    {
        return $this->hasMany(OrderStateLog::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(OrderAssignment::class);
    }

    public function dispatchDecisions(): HasMany
    {
        return $this->hasMany(DispatchDecision::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(OrderTrackingEvent::class);
    }

    public function deliveryProofs(): HasMany
    {
        return $this->hasMany(DeliveryProof::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function paymentRefunds(): HasMany
    {
        return $this->hasMany(PaymentRefund::class);
    }
}
