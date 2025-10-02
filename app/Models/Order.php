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
        'payment_reference', // FIX: Added missing field
        'delivery_address',
        'special_instructions',
        'estimated_delivery_time',
        'confirmed_at',
        'prepared_at', // FIX: Added missing field
        'delivered_at',
        'cancellation_reason', // FIX: Added missing field
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        // ... (existing casts) ...
        'total_amount' => 'decimal:2',
        'delivery_address' => 'array',
        'estimated_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime', // FIX: Added missing field
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
    // NEW: Helper to get status color for dashboard UI
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'delivered' => 'success',
            'pending' => 'secondary',
            'confirmed', 'preparing' => 'warning',
            'ready', 'out_for_delivery' => 'info',
            'cancelled', 'refunded' => 'danger',
            default => 'secondary',
        };
    }

    // NEW: Helper method for common status checks
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    // NEW: Method for status update
    public function markAsPrepared(): void
    {
        $this->update([
            'status' => 'prepared',
            'prepared_at' => now(),
        ]);
    }
}