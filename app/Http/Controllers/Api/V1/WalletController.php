<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletTransactionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    use ApiResponse;

    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        return $this->success([
            'balance' => (float) $wallet->balance,
            'formatted_balance' => '₦' . number_format($wallet->balance, 2),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $user = $request->user();

        $logs = WalletTransactionLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 20));

        return $this->successWithPagination(
            $logs->through(fn($log) => [
                'id' => $log->id,
                'type' => $log->type,
                'amount' => (float) $log->amount,
                'formatted_amount' => (in_array($log->type, ['payout', 'subscription_payment', 'order_payment']) ? '-' : '+') . '₦' . number_format($log->amount, 2),
                'balance_before' => (float) $log->balance_before,
                'balance_after' => (float) $log->balance_after,
                'reference' => $log->reference,
                'description' => $log->description,
                'created_at' => $log->created_at->toISOString(),
            ])
        );
    }

    public function fund(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:100|max:1000000',
        ]);

        $user = $request->user();
        $amount = (float) $request->amount;

        $reference = Payment::generateReference();

        $payment = Payment::create([
            'reference' => $reference,
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'NGN',
            'type' => 'wallet_topup',
            'status' => 'pending',
            'payment_method' => 'paystack',
            'gateway' => 'paystack',
        ]);

        $paystackResult = $this->initializePaystack($user->email, $amount, $reference);

        if (!$paystackResult['success']) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $paystackResult['message'],
            ]);
            return $this->error($paystackResult['message'], 400);
        }

        $payment->update([
            'gateway_reference' => $paystackResult['gateway_reference'],
            'gateway_response' => $paystackResult['data'],
        ]);

        return $this->success([
            'authorization_url' => $paystackResult['authorization_url'],
            'reference' => $reference,
        ], 'Wallet funding initialized');
    }

    public function verifyFunding(Request $request): JsonResponse
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $user = $request->user();
        $reference = $request->reference;

        $payment = Payment::where('reference', $reference)
            ->where('user_id', $user->id)
            ->where('type', 'wallet_topup')
            ->first();

        if (!$payment) {
            return $this->error('Payment not found', 404);
        }

        if ($payment->isSuccessful()) {
            return $this->success(['balance' => (float) $user->wallet?->balance ?? 0], 'Payment already verified');
        }

        $verification = $this->verifyPaystack($reference);

        if (!$verification['success']) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $verification['message'],
            ]);
            return $this->error($verification['message'], 400);
        }

        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        $payment->markAsSuccessful($verification['data']);

        $wallet->logTransaction(
            'wallet_topup',
            $payment->amount,
            $reference,
            'Wallet funding via Paystack',
        );

        return $this->success([
            'balance' => (float) $wallet->fresh()->balance,
            'formatted_balance' => '₦' . number_format($wallet->fresh()->balance, 2),
        ], 'Wallet funded successfully');
    }

    protected function initializePaystack(string $email, float $amount, string $reference): array
    {
        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return ['success' => false, 'message' => 'Payment gateway not configured'];
        }

        try {
            $response = Http::withToken($secretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $email,
                    'amount' => $amount * 100,
                    'reference' => $reference,
                    'callback_url' => config('app.url') . '/api/v1/payment/verify',
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false)) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'gateway_reference' => $data['data']['reference'],
                    'data' => $data['data'],
                ];
            }

            return ['success' => false, 'message' => $data['message'] ?? 'Failed to initialize payment'];
        } catch (\Exception $e) {
            Log::error('Paystack wallet funding init error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment service error'];
        }
    }

    protected function verifyPaystack(string $reference): array
    {
        $secretKey = config('services.paystack.secret_key');

        if (!$secretKey) {
            return ['success' => false, 'message' => 'Payment gateway not configured'];
        }

        try {
            $response = Http::withToken($secretKey)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) && ($data['data']['status'] ?? '') === 'success') {
                return [
                    'success' => true,
                    'gateway_reference' => $data['data']['id'],
                    'amount' => ($data['data']['amount'] ?? 0) / 100,
                    'data' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['data']['gateway_response'] ?? $data['message'] ?? 'Payment verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack wallet funding verify error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment verification error'];
        }
    }
}
