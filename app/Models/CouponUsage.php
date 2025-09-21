<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'discount_amount',
        'order_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'order_amount' => 'decimal:2',
    ];

    // Relationships
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCoupon($query, $couponId)
    {
        return $query->where('coupon_id', $couponId);
    }

    // Helper Methods
    public function getSavingsPercentage(): float
    {
        if ($this->order_amount == 0) return 0;
        return ($this->discount_amount / $this->order_amount) * 100;
    }
}