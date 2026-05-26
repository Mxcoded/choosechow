<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Http\Resources\ReviewResource;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Get user's reviews.
     * 
     * GET /api/v1/reviews
     */
    public function index(Request $request)
    {
        $reviews = Review::where('customer_id', $request->user()->id)
            ->with(['chef.chefProfile', 'order'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return $this->successWithPagination(
            $reviews->through(fn($review) => new ReviewResource($review))
        );
    }

    /**
     * Submit a review for an order.
     * 
     * POST /api/v1/reviews
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => ['required', 'exists:orders,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $user = $request->user();

        // Verify order belongs to user
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return $this->notFound('Order not found');
        }

        // Check if order is completed
        if (!in_array($order->status, ['delivered', 'completed'])) {
            return $this->error('You can only review completed orders', 400);
        }

        // Check if already reviewed
        $existingReview = Review::where('order_id', $order->id)
            ->where('customer_id', $user->id)
            ->first();

        if ($existingReview) {
            return $this->error('You have already reviewed this order', 400);
        }

        $review = Review::create([
            'order_id' => $order->id,
            'customer_id' => $user->id,
            'chef_id' => $order->chef_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Update chef's rating
        $this->updateChefRating($order->chef_id);

        return $this->created(
            new ReviewResource($review->load(['chef.chefProfile', 'order'])),
            'Review submitted successfully'
        );
    }

    /**
     * Get a specific review.
     * 
     * GET /api/v1/reviews/{id}
     */
    public function show(Request $request, $id)
    {
        $review = Review::where('customer_id', $request->user()->id)
            ->with(['chef.chefProfile', 'order'])
            ->findOrFail($id);

        return $this->success(new ReviewResource($review));
    }

    /**
     * Update a review.
     * 
     * PUT /api/v1/reviews/{id}
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator->errors());
        }

        $review = Review::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        // Check if review can still be edited (within 24 hours)
        if ($review->created_at->diffInHours(now()) > 24) {
            return $this->error('Reviews can only be edited within 24 hours of submission', 400);
        }

        $review->update($request->only(['rating', 'comment']));

        // Update chef's rating
        $this->updateChefRating($review->chef_id);

        return $this->success(
            new ReviewResource($review->fresh()->load(['chef.chefProfile', 'order'])),
            'Review updated successfully'
        );
    }

    /**
     * Delete a review.
     * 
     * DELETE /api/v1/reviews/{id}
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::where('customer_id', $request->user()->id)
            ->findOrFail($id);

        // Check if review can still be deleted (within 24 hours)
        if ($review->created_at->diffInHours(now()) > 24) {
            return $this->error('Reviews can only be deleted within 24 hours of submission', 400);
        }

        $chefId = $review->chef_id;
        $review->delete();

        // Update chef's rating
        $this->updateChefRating($chefId);

        return $this->success(null, 'Review deleted successfully');
    }

    /**
     * Update chef's average rating.
     */
    protected function updateChefRating($chefId)
    {
        $stats = Review::where('chef_id', $chefId)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total')
            ->first();

        $chef = \App\Models\User::find($chefId);
        if ($chef && $chef->chefProfile) {
            $chef->chefProfile->update([
                'rating' => round($stats->avg_rating, 2),
                'total_reviews' => $stats->total,
            ]);
        }
    }
}
