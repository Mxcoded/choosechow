<?php
// app/Models/UserSubscription.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'billing_cycle',
        'amount',
        'trial_ends_at',
        'current_period_start',
        'current_period_end',
        'cancelled_at',
        'ends_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'trial_ends_at' => 'datetime',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'cancelled_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'trial']) &&
            $this->current_period_end->isFuture();
    }

    public function cancel($immediately = false): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => $immediately ? now() : $this->current_period_end,
        ]);
    }
}
