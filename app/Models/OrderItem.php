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
        'name', // Snapshot of name at time of order
        'price', // Snapshot of price
        'quantity',
        'options', // JSON: {"spiciness": "high", "extras": ["cheese"]}
        'subtotal'
    ];

    protected $casts = [
        'options' => 'array',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class)->withTrashed(); // Keep linking even if menu is soft-deleted
    }
}
