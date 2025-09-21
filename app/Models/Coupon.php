<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'is_active',
        'starts_at',
        'expires_at',
        'applicable_to',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_to' => 'array',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now());
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereRaw('used_count < usage_limit');
            });
    }

    // Helper Methods
    public function canBeUsedBy($userId, $orderAmount = null): bool
    {
        if (!$this->is_active || $this->expires_at < now()) {
            return false;
        }

        if ($this->minimum_order_amount && $orderAmount < $this->minimum_order_amount) {
            return false;
        }

        if ($this->usage_limit_per_user) {
            $userUsageCount = CouponUsage::where('coupon_id', $this->id)
                ->where('user_id', $userId)
                ->count();

            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($orderAmount): float
    {
        $discount = match ($this->type) {
            'percentage' => $orderAmount * ($this->value / 100),
            'fixed_amount' => $this->value,
            'free_delivery' => 0,
            default => 0,
        };

        if ($this->maximum_discount_amount && $discount > $this->maximum_discount_amount) {
            $discount = $this->maximum_discount_amount;
        }

        return min($discount, $orderAmount);
    }
}
