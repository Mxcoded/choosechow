<?php
// app/Models/SubscriptionPlan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'user_type',
        'description',
        'monthly_price',
        'yearly_price',
        'commission_rate',
        'features',
        'limits',
        'is_popular',
        'is_active',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getPriceFor($billingCycle): float
    {
        return $billingCycle === 'yearly' ? $this->yearly_price : $this->monthly_price;
    }

    public function hasFeature($feature): bool
    {
        return in_array($feature, $this->features ?? []);
    }
}