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
}