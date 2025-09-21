<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class ChefPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'payout_reference',
        'gross_amount',
        'commission_amount',
        'net_amount',
        'status',
        'payout_method',
        'bank_details',
        'transaction_reference',
        'failure_reason',
        'processed_at',
        'orders_included',
        'payout_period_start',
        'payout_period_end',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'bank_details' => 'array',
        'orders_included' => 'array',
        'processed_at' => 'datetime',
        'payout_period_start' => 'date',
        'payout_period_end' => 'date',
    ];

    // Relationships
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper Methods
    public function markAsCompleted($transactionReference = null): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
            'transaction_reference' => $transactionReference,
        ]);
    }

    public function getOrdersCount(): int
    {
        return count($this->orders_included);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PAYOUT_' . now()->format('YmdHis') . '_' . strtoupper(Str::random(6));
        } while (self::where('payout_reference', $reference)->exists());

        return $reference;
    }
}
