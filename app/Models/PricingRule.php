<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $fillable = [
        'name',
        'rule_type',
        'priority',
        'conditions',
        'effect',
        'active_from',
        'active_until',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'effect' => 'array',
        'active_from' => 'datetime',
        'active_until' => 'datetime',
        'is_active' => 'boolean',
    ];
}

