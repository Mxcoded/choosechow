<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransactionLog extends Model
{
    use HasFactory;

    protected $table = 'wallet_transaction_logs';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'reference',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship to the user who owns the wallet
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get earnings
     */
    public function scopeEarnings($query)
    {
        return $query->where('type', 'earning');
    }

    /**
     * Scope to get payouts
     */
    public function scopePayouts($query)
    {
        return $query->where('type', 'payout');
    }

    /**
     * Scope to get refunds
     */
    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
