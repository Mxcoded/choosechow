<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUserVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check {email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Check user verification status';

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
        
        $this->info("User Details:");
        $this->line("  Email: {$user->email}");
        $this->line("  Status: {$user->status}");
        $this->line("  Email Verified At: " . ($user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'NULL'));
        $this->line("  Roles: " . implode(', ', $user->getRoleNames()->toArray() ?: ['none']));
        $this->line("  Has Verified Email: " . ($user->hasVerifiedEmail() ? 'YES' : 'NO'));
    }
}
