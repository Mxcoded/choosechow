<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'chef_id',
        'total_amount',
        'status',          // pending_payment, pending, preparing, ready, completed, cancelled
        'payment_status',  // pending, paid, failed
        'payment_method',
        'delivery_address',
        'phone_number',
        'notes'
    ];

    /**
     * Relationship: Order belongs to a Customer (User)
     * This was the missing part causing your error!
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Order belongs to a Chef (User)
     */
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    /**
     * Relationship: Order has many Items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}