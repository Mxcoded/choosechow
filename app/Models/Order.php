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
        'status', // pending, confirmed, preparing, ready, out_for_delivery, delivered, cancelled
        'subtotal',
        'delivery_fee',
        'service_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_reference',
        'delivery_address', // JSON
        'special_instructions',
        'estimated_delivery_time',
        'confirmed_at',
        'prepared_at',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected $casts = [
        'delivery_address' => 'array',
        'estimated_delivery_time' => 'datetime',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    // --- HELPERS ---

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
