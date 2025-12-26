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
        'menu_name',        // Matches migration
        'menu_description', // Matches migration
        'unit_price',       // Matches migration
        'quantity',
        'total_price',      // Matches migration
        'customizations',   // Matches migration (JSON)
    ];

    protected $casts = [
        'customizations' => 'array',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class)->withTrashed();
    }
}
