<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChefProfile;
use App\Services\SubscriptionService;
use App\Services\DiscountService;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptionService,
        protected DiscountService $discountService,
        protected BillingService $billingService,
    ) {}

    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|string|in:basic,plus,premium',
            'payment_method' => 'sometimes|string|in:wallet,paystack',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->subscribe($user, $request->tier, $request->payment_method);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        if (isset($result['authorization_url'])) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200);
        }

        return response()->json(['success' => true, 'data' => $result], 201);
    }

    public function verifyPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->verifyAndActivate($user, $request->reference);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function verifyUpgradePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->verifyAndApplyUpgrade($user, $request->reference);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function upgrade(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|string|in:plus,premium',
            'payment_method' => 'sometimes|string|in:wallet,paystack',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->upgrade($user, $request->tier, $request->payment_method);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        if (isset($result['authorization_url'])) {
            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function downgrade(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'tier' => 'required|string|in:basic,plus',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->downgrade($user, $request->tier);

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function cancel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'immediately' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->cancel($user, $request->boolean('immediately', false));

        if (!$result['success']) {
            return response()->json($result, 400);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $this->subscriptionService->getStatus($user);

        return response()->json(['success' => true, 'data' => $status]);
    }

    public function plans(): JsonResponse
    {
        $plans = \App\Models\SubscriptionPlan::where('user_type', 'customer')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($plan) => [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'monthly_price' => (float)$plan->monthly_price,
                'features' => $plan->features,
                'is_popular' => $plan->is_popular,
            ]);

        return response()->json(['success' => true, 'data' => $plans]);
    }

    public function calculateCheckout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subtotal' => 'required|numeric|min:0',
            'delivery_fee' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.chef_id' => 'sometimes|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $result = $this->subscriptionService->calculateCheckout(
            $user,
            $request->subtotal,
            $request->delivery_fee,
            $request->items
        );

        return response()->json(['success' => true, 'data' => $result]);
    }
}
