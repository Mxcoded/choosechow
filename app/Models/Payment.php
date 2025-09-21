<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'user_id',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'type',
        'status',
        'payment_method',
        'gateway',
        'gateway_reference',
        'gateway_response',
        'gateway_fee',
        'failure_reason',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_fee' => 'decimal:2',
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    // Helper Methods
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function markAsSuccessful($gatewayResponse = null): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_response' => $gatewayResponse,
        ]);
    }

    public static function generateReference(): string
    {
        do {
            $reference = 'PAY_' . now()->format('YmdHis') . '_' . strtoupper(Str::random(8));
        } while (self::where('reference', $reference)->exists());

        return $reference;
    }
}