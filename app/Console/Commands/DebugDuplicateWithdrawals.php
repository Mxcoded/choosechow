<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Withdrawal;

class DebugDuplicateWithdrawals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:duplicate-withdrawals {userId}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check for duplicate withdrawal requests from a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');
        
        $withdrawals = Withdrawal::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($withdrawals->isEmpty()) {
            $this->error("No withdrawals found for user #{$userId}");
            return;
        }
        
        $this->info("=== WITHDRAWALS FOR USER #{$userId} ===");
        $this->line("Total: {$withdrawals->count()}");
        
        foreach ($withdrawals as $w) {
            $this->line("\nWithdrawal #{$w->id}:");
            $this->line("  Status: {$w->status}");
            $this->line("  Amount: ₦{$w->amount}");
            $this->line("  Reference ID: '{$w->reference_id}'");
            $this->line("  Bank: {$w->bank_name}");
            $this->line("  Account: {$w->account_number}");
            $this->line("  Created: {$w->created_at->format('Y-m-d H:i:s')}");
        }
    }
}
