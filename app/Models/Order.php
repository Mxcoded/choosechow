<?php
// app/Models/Order.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'chef_id',
        'status',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'delivery_address',
        'special_instructions',
        'estimated_delivery_time',
        'confirmed_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_address' => 'array',
        'estimated_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    // Helper Methods
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) &&
            $this->created_at->diffInMinutes(now()) <= 30;
    }

    public function confirm(): void
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function getChefEarnings(): float
    {
        $subscription = $this->chef->activeSubscription;
        $commissionRate = $subscription ? $subscription->plan->commission_rate : 10;

        return $this->subtotal * (1 - ($commissionRate / 100));
    }
}