<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $baseUrl;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.paystack.base_url');
        $this->secretKey = config('services.paystack.secret_key');
    }

    /**
     * Step 1: Initialize the transaction and get the Authorization URL
     */
    public function initializeTransaction($email, $amount, $reference)
    {
        // Paystack expects amount in Kobo (multiply by 100)
        $amountInKobo = $amount * 100;

        $response = Http::withToken($this->secretKey)->post("{$this->baseUrl}/transaction/initialize", [
            'email' => $email,
            'amount' => $amountInKobo,
            'reference' => $reference,
            'callback_url' => route('payment.callback'), // We will create this route next
            'metadata' => [
                'cancel_action' => route('checkout.index') // If they cancel
            ]
        ]);

        return $response->json();
    }

    /**
     * Step 2: Verify the transaction after user returns
     */
    public function verifyTransaction($reference)
    {
        $response = Http::withToken($this->secretKey)->get("{$this->baseUrl}/transaction/verify/{$reference}");
        return $response->json();
    }

    /**
     * Step 3: Verify Paystack Signature (CRITICAL SECURITY)
     * This validates that the payment notification came from Paystack, not an attacker
     */
    public function verifySignature($signature)
    {
        // Get the raw request body
        $input = file_get_contents("php://input");
        
        // Compute HMAC-SHA512
        $computedSignature = hash_hmac('sha512', $input, $this->secretKey);
        
        // Compare signatures (use timing-safe comparison)
        return hash_equals($signature, $computedSignature);
    }

    /**
     * Validate payment amount matches order total
     * Allows small tolerance for rounding differences
     */
    public function validatePaymentAmount($paymentAmount, $expectedAmount)
    {
        // Paystack returns amount in Kobo
        $paymentInKobo = $paymentAmount;
        $expectedInKobo = $expectedAmount * 100;
        
        // Allow 1 Kobo difference for rounding (floating point precision)
        $difference = abs($paymentInKobo - $expectedInKobo);
        
        return $difference <= 1;
    }

    /**
     * Check if transaction is actually successful
     */
    public function isTransactionSuccessful($verificationData)
    {
        return isset($verificationData['data']) && 
               isset($verificationData['data']['status']) && 
               $verificationData['data']['status'] === 'success' &&
               isset($verificationData['status']) &&
               $verificationData['status'] === true;
    }
}