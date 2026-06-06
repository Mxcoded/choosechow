<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to wallet transaction logs
     */
    public function transactionLogs()
    {
        return $this->hasMany(WalletTransactionLog::class, 'user_id', 'user_id');
    }

    /**
     * Log a wallet transaction with complete audit trail
     * 
     * @param string $type 'earning', 'payout', 'refund', 'adjustment', 'subscription_payment'
     * @param float $amount Amount to add/subtract
     * @param string|null $reference Order/payment/withdrawal ID for traceability
     * @param string|null $description Human-readable description
     * @return \App\Models\WalletTransactionLog
     */
    public function logTransaction(string $type, float $amount, ?string $reference = null, ?string $description = null)
    {
        $balanceBefore = $this->balance;
        
        // Update wallet balance based on transaction type
        if (in_array($type, ['payout', 'subscription_payment'])) {
            $this->balance -= $amount;
        } elseif ($type === 'refund') {
            $this->balance += $amount;
        } else {
            $this->balance += $amount;
        }
        
        $balanceAfter = $this->balance;
        $this->save();

        // Log the transaction
        return WalletTransactionLog::create([
            'user_id' => $this->user_id,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'reference' => $reference,
            'description' => $description,
        ]);
    }

    /**
     * Deduct from wallet balance and log the transaction.
     */
    public function deduct(float $amount, string $reference, string $description = ''): WalletTransactionLog
    {
        return $this->logTransaction('subscription_payment', $amount, $reference, $description);
    }

    /**
     * Check if wallet has sufficient balance.
     */
    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    /**
     * Get balance history for audit purposes
     * 
     * @param int $limit Number of recent transactions to fetch
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBalanceHistory($limit = 50)
    {
        return $this->transactionLogs()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Verify balance integrity by checking transaction logs
     * Returns true if current balance matches calculated balance from logs
     * 
     * @return bool
     */
    public function verifyBalanceIntegrity()
    {
        $calculatedBalance = $this->transactionLogs()
            ->orderBy('created_at', 'asc')
            ->get()
            ->reduce(function ($balance, $log) {
                if ($log->type === 'payout' || $log->type === 'refund') {
                    return $balance - $log->amount;
                }
                return $balance + $log->amount;
            }, 0);

        return abs($calculatedBalance - $this->balance) < 0.01; // Allow for float precision
    }
}
