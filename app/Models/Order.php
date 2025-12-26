<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

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
        'payment_reference',
        'delivery_address',
        'special_instructions',
        'estimated_delivery_time',
        'confirmed_at',
        'prepared_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'metadata',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'metadata' => 'array',
        'estimated_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // --- RELATIONSHIPS ---

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Optional: If you track payments in a separate table
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // --- HELPERS (Required by Controller/View) ---

    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'payment_status' => 'paid', // Assume cash on delivery is paid now, or verify online payment
        ]);
    }

    public function getChefEarnings()
    {
        // Logic: Chef gets Subtotal - Platform Fees (if any). 
        // For now, let's return Subtotal. 
        return $this->subtotal;
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'confirmed', 'preparing' => 'info',
            'ready', 'out_for_delivery' => 'primary',
            'delivered' => 'success',
            'cancelled', 'refunded' => 'danger',
            default => 'secondary',
        };
    }
}
