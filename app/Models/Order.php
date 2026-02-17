<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'chef_id',
        'subtotal',
        'delivery_fee',
        'total_amount',
        'status',          // pending_payment, pending, preparing, ready, completed, cancelled
        'payment_status',  // pending, paid, failed
        'payment_method',
        'delivery_address',
        'phone_number',
        'notes',
        // Scheduling fields
        'delivery_type',      // 'asap' or 'scheduled'
        'scheduled_date',     // Date for scheduled delivery
        'scheduled_time_slot', // e.g., '12:00-13:00'
        'scheduled_for',      // Full datetime
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_for' => 'datetime',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    // ================== SCHEDULING HELPERS ==================

    /**
     * Check if this is a scheduled order
     */
    public function isScheduled(): bool
    {
        return $this->delivery_type === 'scheduled';
    }

    /**
     * Check if this is an ASAP order
     */
    public function isAsap(): bool
    {
        return $this->delivery_type === 'asap' || empty($this->delivery_type);
    }

    /**
     * Get formatted delivery time string
     */
    public function getDeliveryTimeDisplayAttribute(): string
    {
        if ($this->isAsap()) {
            return 'ASAP (30-45 mins)';
        }

        if ($this->scheduled_date && $this->scheduled_time_slot) {
            return $this->scheduled_date->format('D, M j') . ' at ' . $this->scheduled_time_slot;
        }

        if ($this->scheduled_for) {
            return $this->scheduled_for->format('D, M j \a\t g:i A');
        }

        return 'ASAP';
    }

    /**
     * Get available time slots for scheduling
     */
    public static function getAvailableTimeSlots(): array
    {
        return [
            '08:00-09:00' => '8:00 AM - 9:00 AM',
            '09:00-10:00' => '9:00 AM - 10:00 AM',
            '10:00-11:00' => '10:00 AM - 11:00 AM',
            '11:00-12:00' => '11:00 AM - 12:00 PM',
            '12:00-13:00' => '12:00 PM - 1:00 PM',
            '13:00-14:00' => '1:00 PM - 2:00 PM',
            '14:00-15:00' => '2:00 PM - 3:00 PM',
            '15:00-16:00' => '3:00 PM - 4:00 PM',
            '16:00-17:00' => '4:00 PM - 5:00 PM',
            '17:00-18:00' => '5:00 PM - 6:00 PM',
            '18:00-19:00' => '6:00 PM - 7:00 PM',
            '19:00-20:00' => '7:00 PM - 8:00 PM',
            '20:00-21:00' => '8:00 PM - 9:00 PM',
        ];
    }

    /**
     * Get available dates for scheduling (next 7 days)
     */
    public static function getAvailableDates(int $days = 7): array
    {
        $dates = [];
        $today = Carbon::today();

        for ($i = 0; $i < $days; $i++) {
            $date = $today->copy()->addDays($i);
            $dates[$date->format('Y-m-d')] = $i === 0 
                ? 'Today' 
                : ($i === 1 ? 'Tomorrow' : $date->format('D, M j'));
        }

        return $dates;
    }

    // ================== SCOPES ==================

    /**
     * Scope: Only scheduled orders
     */
    public function scopeScheduled($query)
    {
        return $query->where('delivery_type', 'scheduled');
    }

    /**
     * Scope: Only ASAP orders
     */
    public function scopeAsap($query)
    {
        return $query->where(function ($q) {
            $q->where('delivery_type', 'asap')
              ->orWhereNull('delivery_type');
        });
    }

    /**
     * Scope: Orders scheduled for a specific date
     */
    public function scopeScheduledFor($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }
}
