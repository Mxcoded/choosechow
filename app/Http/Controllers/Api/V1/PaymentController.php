<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponse;

    /**
     * Get available payment methods.
     * 
     * GET /api/v1/payment/methods
     */
    public function methods(Request $request)
    {
        $methods = [
            [
                'id' => 'card',
                'name' => 'Debit/Credit Card',
                'description' => 'Pay with your bank card',
                'icon' => 'credit-card',
                'is_available' => true,
                'gateway' => 'paystack',
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Bank Transfer',
                'description' => 'Pay via bank transfer',
                'icon' => 'bank',
                'is_available' => true,
                'gateway' => 'paystack',
            ],
            [
                'id' => 'ussd',
                'name' => 'USSD',
                'description' => 'Pay using USSD code',
                'icon' => 'phone',
                'is_available' => true,
                'gateway' => 'paystack',
            ],
        ];

        return $this->success([
            'methods' => $methods,
            'default_method' => 'card',
        ]);
    }

    /**
     * Initialize a standalone payment (for tips, wallet top-up, etc.)
     * 
     * POST /api/v1/payment/initialize
     */
    public function initialize(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
            'type' => 'required|in:order_payment,wallet_topup,tip',
            'order_id' => 'required_if:type,order_payment|exists:orders,id',
            'metadata' => 'nullable|array',
        ]);

        $user = $request->user();
        $amount = $request->amount;

        // For order payments, verify the order
        if ($request->type === 'order_payment') {
            $order = Order::where('id', $request->order_id)
                ->where('user_id', $user->id)
                ->where('payment_status', 'pending')
                ->first();

            if (!$order) {
                return $this->error('Order not found or already paid', 404);
            }

            $amount = $order->total_amount;
        }

        $paymentData = $this->initializePaystackPayment($user, $amount, [
            'type' => $request->type,
            'order_id' => $request->order_id,
            'metadata' => $request->metadata,
        ]);

        if (!$paymentData['success']) {
            return $this->error($paymentData['message'], 400);
        }

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'order_id' => $request->order_id,
            'payable_type' => $request->type === 'order_payment' ? Order::class : null,
            'payable_id' => $request->order_id,
            'amount' => $amount,
            'reference' => $paymentData['reference'],
            'type' => $request->type,
            'gateway' => 'paystack',
            'status' => 'pending',
            'metadata' => $request->metadata,
        ]);

        return $this->success([
            'authorization_url' => $paymentData['authorization_url'],
            'access_code' => $paymentData['access_code'],
            'reference' => $paymentData['reference'],
        ], 'Payment initialized');
    }

    /**
     * Verify payment (callback from Paystack or manual verification).
     * 
     * GET /api/v1/payment/verify
     * POST /api/v1/payment/verify
     */
    public function verify(Request $request)
    {
        $reference = $request->input('reference') ?? $request->query('reference');

        if (!$reference) {
            return $this->error('Payment reference is required', 400);
        }

        // Find payment record
        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            return $this->error('Payment not found', 404);
        }

        // If already verified
        if ($payment->status === 'success') {
            return $this->success([
                'status' => 'success',
                'message' => 'Payment already verified',
                'payment' => $this->formatPayment($payment),
            ]);
        }

        // Verify with Paystack
        $verificationResult = $this->verifyPaystackPayment($reference);

        if (!$verificationResult['success']) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $verificationResult['message'],
            ]);

            return $this->error($verificationResult['message'], 400);
        }

        // Update payment
        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_reference' => $verificationResult['gateway_reference'],
            'gateway_response' => $verificationResult['data'],
            'gateway_fee' => $verificationResult['fees'] ?? 0,
        ]);

        // Update related order(s)
        if ($payment->order_id) {
            Order::where('id', $payment->order_id)->update([
                'status' => 'pending',
                'payment_status' => 'paid',
            ]);
        }

        // Also update orders by reference (for multi-order payments)
        Payment::where('reference', $reference)
            ->where('id', '!=', $payment->id)
            ->update([
                'status' => 'success',
                'paid_at' => now(),
                'gateway_reference' => $verificationResult['gateway_reference'],
            ]);

        $relatedOrders = Order::whereIn('id', 
            Payment::where('reference', $reference)->pluck('order_id')
        )->get();

        foreach ($relatedOrders as $order) {
            $order->update([
                'status' => 'pending',
                'payment_status' => 'paid',
            ]);
        }

        return $this->success([
            'status' => 'success',
            'message' => 'Payment verified successfully',
            'payment' => $this->formatPayment($payment->fresh()),
            'orders' => $relatedOrders->map(fn($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
            ]),
        ]);
    }

    /**
     * Paystack Webhook handler.
     * 
     * POST /api/v1/payment/webhook
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $paystackSignature = $request->header('x-paystack-signature');
        $payload = $request->getContent();
        $secretKey = config('services.paystack.secret_key');

        if (!$paystackSignature || !$secretKey) {
            Log::warning('Paystack webhook: Missing signature or secret key');
            return response()->json(['status' => 'error'], 400);
        }

        $computedSignature = hash_hmac('sha512', $payload, $secretKey);

        if (!hash_equals($computedSignature, $paystackSignature)) {
            Log::warning('Paystack webhook: Invalid signature');
            return response()->json(['status' => 'error'], 400);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        Log::info('Paystack webhook received', ['event' => $event, 'reference' => $data['reference'] ?? null]);

        switch ($event) {
            case 'charge.success':
                $this->handleSuccessfulCharge($data);
                break;
            case 'charge.failed':
                $this->handleFailedCharge($data);
                break;
            case 'transfer.success':
                // Handle vendor payout success
                break;
            case 'transfer.failed':
                // Handle vendor payout failure
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Get user's payment history.
     * 
     * GET /api/v1/payment/history
     */
    public function history(Request $request)
    {
        $payments = Payment::where('user_id', $request->user()->id)
            ->with(['payable'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return $this->successWithPagination(
            $payments->through(fn($p) => $this->formatPayment($p))
        );
    }

    // ===================== HELPER METHODS =====================

    /**
     * Initialize Paystack payment.
     */
    protected function initializePaystackPayment($user, $amount, $options = [])
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey) {
            return [
                'success' => false,
                'message' => 'Payment gateway not configured',
            ];
        }

        $reference = Payment::generateReference();

        try {
            $response = Http::withToken($paystackSecretKey)
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email' => $user->email,
                    'amount' => $amount * 100, // Paystack expects amount in kobo
                    'reference' => $reference,
                    'callback_url' => config('app.url') . '/api/v1/payment/verify',
                    'metadata' => array_merge([
                        'user_id' => $user->id,
                        'customer_name' => $user->full_name,
                    ], $options['metadata'] ?? []),
                ]);

            $data = $response->json();

            if ($response->successful() && $data['status']) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $data['data']['reference'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Failed to initialize payment',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack initialization error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment service error',
            ];
        }
    }

    /**
     * Verify Paystack payment.
     */
    protected function verifyPaystackPayment($reference)
    {
        $paystackSecretKey = config('services.paystack.secret_key');

        if (!$paystackSecretKey) {
            return [
                'success' => false,
                'message' => 'Payment gateway not configured',
            ];
        }

        try {
            $response = Http::withToken($paystackSecretKey)
                ->get("https://api.paystack.co/transaction/verify/{$reference}");

            $data = $response->json();

            if ($response->successful() && $data['status'] && $data['data']['status'] === 'success') {
                return [
                    'success' => true,
                    'gateway_reference' => $data['data']['id'],
                    'amount' => $data['data']['amount'] / 100,
                    'fees' => ($data['data']['fees'] ?? 0) / 100,
                    'data' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['data']['gateway_response'] ?? $data['message'] ?? 'Payment verification failed',
            ];

        } catch (\Exception $e) {
            Log::error('Paystack verification error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Payment verification error',
            ];
        }
    }

    /**
     * Handle successful charge webhook.
     */
    protected function handleSuccessfulCharge($data)
    {
        $reference = $data['reference'];
        $payment = Payment::where('reference', $reference)->first();

        if (!$payment || $payment->status === 'success') {
            return;
        }

        $payment->update([
            'status' => 'success',
            'paid_at' => now(),
            'gateway_reference' => $data['id'],
            'gateway_response' => $data,
            'gateway_fee' => ($data['fees'] ?? 0) / 100,
        ]);

        // Update related orders
        if ($payment->order_id) {
            Order::where('id', $payment->order_id)->update([
                'status' => 'pending',
                'payment_status' => 'paid',
            ]);
        }

        // Send notification to user
        // TODO: Notification::create([...])
    }

    /**
     * Handle failed charge webhook.
     */
    protected function handleFailedCharge($data)
    {
        $reference = $data['reference'];
        $payment = Payment::where('reference', $reference)->first();

        if (!$payment) {
            return;
        }

        $payment->update([
            'status' => 'failed',
            'failure_reason' => $data['gateway_response'] ?? 'Payment failed',
            'gateway_response' => $data,
        ]);
    }

    /**
     * Format payment for API response.
     */
    protected function formatPayment(Payment $payment): array
    {
        return [
            'id' => $payment->id,
            'reference' => $payment->reference,
            'amount' => (float) $payment->amount,
            'formatted_amount' => '₦' . number_format($payment->amount, 2),
            'type' => $payment->type,
            'status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'gateway' => $payment->gateway,
            'paid_at' => $payment->paid_at?->toISOString(),
            'created_at' => $payment->created_at->toISOString(),
            'order_id' => $payment->order_id,
        ];
    }
}
