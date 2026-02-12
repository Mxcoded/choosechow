<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;

class TestRefund extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:refund';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Test the refund logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find a test user
        $user = User::first();
        
        if (!$user) {
            $this->error("No users found");
            return;
        }
        
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 10000]);
        
        $this->info("Initial balance: ₦" . number_format($wallet->balance, 2));
        
        // Test payout (should subtract)
        $wallet->logTransaction('payout', 1000, 'TEST-PAYOUT', 'Test payout');
        $wallet->refresh();
        $this->line("After payout of ₦1,000: ₦" . number_format($wallet->balance, 2));
        
        if ($wallet->balance == 9000) {
            $this->info("✓ Payout logic correct!");
        } else {
            $this->error("✗ Payout logic WRONG! Expected ₦9,000 but got ₦" . number_format($wallet->balance, 2));
        }
        
        // Test refund (should add back)
        $wallet->logTransaction('refund', 1000, 'TEST-REFUND', 'Test refund');
        $wallet->refresh();
        $this->line("After refund of ₦1,000: ₦" . number_format($wallet->balance, 2));
        
        if ($wallet->balance == 10000) {
            $this->info("✓ Refund logic correct!");
        } else {
            $this->error("✗ Refund logic WRONG! Expected ₦10,000 but got ₦" . number_format($wallet->balance, 2));
        }
    }
}
