<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'menu_id',
        'quantity',
        'unit_price',
        'total_price',
        'customizations',
        'special_instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'customizations' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // Accessors
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    // Helper Methods
    public function hasCustomizations(): bool
    {
        return !empty($this->customizations);
    }

    public function getCustomization($key): mixed
    {
        return $this->customizations[$key] ?? null;
    }
    // NEW: Boot method to handle menu updates
    protected static function boot()
    {
        parent::boot();

        // Increment order_count and decrement stock when an OrderItem is created
        static::created(function (OrderItem $item) {
            $menu = $item->menu;

            if ($menu) {
                // Increment order count
                $menu->increment('order_count', $item->quantity);

                // Decrement stock
                $menu->decrementStock($item->quantity);
            }
        });

        // Handle refunds or cancellations (optional, but good practice)
        static::deleted(function (OrderItem $item) {
            $menu = $item->menu;

            if ($menu) {
                // Restore stock when an order item is deleted (e.g., during cancellation/refund)
                $menu->restoreStock($item->quantity);
            }
        });
    }
}