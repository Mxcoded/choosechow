<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Verify a user email address manually';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email '{$email}' not found.");
            return;
        }
        
        if ($user->email_verified_at) {
            $this->info("User '{$email}' is already verified.");
            return;
        }
        
        $user->update(['email_verified_at' => now()]);
        
        $this->info("User '{$email}' has been verified successfully!");
    }
}
