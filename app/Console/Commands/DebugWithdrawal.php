<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Withdrawal;
use App\Models\Transaction;

class DebugWithdrawal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:withdrawal {id}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Debug withdrawal and transaction records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $withdrawalId = $this->argument('id');
        
        $withdrawal = Withdrawal::find($withdrawalId);
        
        if (!$withdrawal) {
            $this->error("Withdrawal #{$withdrawalId} not found.");
            return;
        }
        
        $this->info("=== WITHDRAWAL RECORD ===");
        $this->line("ID: {$withdrawal->id}");
        $this->line("User ID: {$withdrawal->user_id}");
        $this->line("Reference ID: '{$withdrawal->reference_id}'");
        $this->line("Reference ID Length: " . strlen($withdrawal->reference_id));
        $this->line("Status: {$withdrawal->status}");
        $this->line("Amount: {$withdrawal->amount}");
        
        $this->info("\n=== MATCHING TRANSACTIONS ===");
        $transaction = Transaction::where('reference', $withdrawal->reference_id)->first();
        
        if ($transaction) {
            $this->line("✓ FOUND matching transaction!");
            $this->line("  ID: {$transaction->id}");
            $this->line("  Reference: '{$transaction->reference}'");
            $this->line("  Reference Length: " . strlen($transaction->reference));
            $this->line("  Status: {$transaction->status}");
            $this->line("  Type: {$transaction->type}");
            $this->line("  Amount: {$transaction->amount}");
        } else {
            $this->error("✗ NO matching transaction found!");
            
            $this->info("\n=== ALL TRANSACTIONS FOR THIS USER ===");
            $allTransactions = Transaction::where('user_id', $withdrawal->user_id)->get();
            
            if ($allTransactions->isEmpty()) {
                $this->line("No transactions for user #{$withdrawal->user_id}");
            } else {
                foreach ($allTransactions as $t) {
                    $match = $t->reference === $withdrawal->reference_id ? "✓ MATCH" : "✗ NO MATCH";
                    $this->line("{$match} | Ref: '{$t->reference}' (len: " . strlen($t->reference) . ") | Status: {$t->status}");
                }
            }
        }
    }
}
