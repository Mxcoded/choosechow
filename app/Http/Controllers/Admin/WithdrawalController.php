<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Mail\PayoutApprovedMail;

class WithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = Withdrawal::with('user.chefProfile')
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->latest()
            ->paginate(15);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function approve($id)
    {
        $withdrawal = Withdrawal::findOrFail($id);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Request already processed.');
        }

        \Log::info("Approving withdrawal #{$withdrawal->id} with reference: {$withdrawal->reference_id}");

        // 1. Mark Withdrawal as Approved
        $withdrawal->update(['status' => 'approved']);

        // 2. UPDATE THE ORIGINAL TRANSACTION using raw update for reliability
        $updated = Transaction::where('reference', $withdrawal->reference_id)
            ->update([
                'status' => 'completed',
                'description' => 'Withdrawal Request Approved - Payment Sent'
            ]);

        if ($updated > 0) {
            \Log::info("Successfully updated {$updated} transaction(s) with reference: {$withdrawal->reference_id}");
        } else {
            // This shouldn't happen if the code is working correctly
            // Log all transactions for this user to debug
            $allTransactions = Transaction::where('user_id', $withdrawal->user_id)->get();
            \Log::warning("No transactions found for withdrawal #{$withdrawal->id} with reference: {$withdrawal->reference_id}. User has " . $allTransactions->count() . " total transactions.");
            foreach ($allTransactions as $t) {
                \Log::info("  - Transaction: {$t->reference} | Status: {$t->status} | Type: {$t->type}");
            }
        }

        // 3. SEND EMAIL NOTIFICATION
        try {
            Mail::to($withdrawal->user->email)->send(new PayoutApprovedMail($withdrawal));
        } catch (\Exception $e) {
            \Log::error('Payout Email Failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Withdrawal marked as PAID & Transaction Updated.');
    }

    public function reject($id)
    {
        DB::transaction(function () use ($id) {
            $withdrawal = Withdrawal::findOrFail($id);

            if ($withdrawal->status !== 'pending') {
                return back()->with('error', 'Request already processed.');
            }

            // 1. Return Money to Wallet with audit trail
            $wallet = Wallet::where('user_id', $withdrawal->user_id)->first();
            if ($wallet) {
                $wallet->logTransaction(
                    'refund',
                    $withdrawal->amount,
                    'WITHDRAWAL-' . $withdrawal->id,
                    'Withdrawal request rejected - funds returned'
                );
            }

            // 2. Mark Withdrawal as Rejected
            $withdrawal->update(['status' => 'rejected']);
            
            // 3. FIND ORIGINAL TRANSACTION & MARK FAILED
            $transaction = Transaction::where('reference', $withdrawal->reference_id)->first();

            if ($transaction) {
                // We mark the original debit as 'failed' or 'rejected' so it shows the money didn't leave permanently
                $transaction->update([
                    'status' => 'failed', // or 'rejected'
                    'description' => 'Withdrawal Rejected - Refunded'
                ]);
            }

            // 4. (Optional) Log a specific Refund Record if you want explicit accounting
            // Only do this if you want a separate green "Deposit" line in their history.
            // Otherwise, step 3 is enough to "cancel" the red line.
            /*
            Transaction::create([
                'user_id' => $withdrawal->user_id,
                'type' => 'refund',
                'amount' => $withdrawal->amount,
                'reference' => 'REFUND-' . $withdrawal->id,
                'description' => 'Refund for rejected withdrawal',
                'status' => 'completed'
            ]);
            */
        });

        return back()->with('success', 'Withdrawal rejected. Money refunded to Chef wallet.');
    }
}