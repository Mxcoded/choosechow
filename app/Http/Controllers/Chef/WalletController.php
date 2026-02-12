<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ensure Wallet Exists (Self-Healing)
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00]
        );

        // 2. Get Transaction History
        $transactions = Transaction::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('chef.wallet.index', compact('wallet', 'transactions'));
    }

    public function requestPayout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000', // Min withdrawal ₦1,000
            'bank_name' => 'required|string',
            'account_number' => 'required|string|min:10',
            'account_name' => 'required|string',
        ]);

        $user = Auth::user();
        $wallet = $user->wallet;

        // 1. Check Balance
        if ($wallet->balance < $request->amount) {
            return back()->with('error', 'Insufficient funds in wallet.');
        }

        // 2. Prevent Duplicate Submissions - Check if identical withdrawal was just submitted
        $recentDuplicate = Withdrawal::where('user_id', $user->id)
            ->where('amount', $request->amount)
            ->where('bank_name', $request->bank_name)
            ->where('account_number', $request->account_number)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(1)) // Within last minute
            ->first();
        
        if ($recentDuplicate) {
            return back()->with('error', 'Duplicate withdrawal request detected. Please wait a moment and try again.');
        }

        // 3. Process Withdrawal (Database Transaction for safety)
        DB::transaction(function () use ($user, $wallet, $request) {
            
            // A. Generate ONE Reference ID for both records
            // This is the "Key" that links the Admin approval to this specific transaction.
            $reference = 'PAYOUT-' . strtoupper(uniqid());

            // B. Deduct Balance Immediately with audit trail (Atomic Operation)
            $wallet->logTransaction(
                'payout',
                $request->amount,
                $reference,
                'Withdrawal request pending approval - Awaiting bank transfer'
            );

            // C. Create Withdrawal Request (Save the reference!)
            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending',
                'reference_id' => $reference // <--- CRITICAL: Save the link here
            ]);

            // D. Log the Transaction (Use the SAME reference!)
            Transaction::create([
                'user_id' => $user->id,
                'reference' => $reference, // <--- CRITICAL: Must match above
                'type' => 'payout',
                'amount' => $request->amount,
                'description' => 'Withdrawal Request to ' . $request->bank_name,
                'status' => 'pending' // Pending admin approval
            ]);
        });

        return back()->with('success', 'Withdrawal request submitted successfully!');
    }
}