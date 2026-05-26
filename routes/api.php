<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\ChefController;
use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CuisineController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Chef\ChefDashboardController;
use App\Http\Controllers\Api\V1\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile App API v1 - ChooseChow
| Base URL: https://choosechow.com/api/v1
|
*/

Route::prefix('v1')->group(function () {

    // ==========================================
    //    PUBLIC ROUTES (No Authentication)
    // ==========================================
    
    // --- Authentication ---
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // --- Public Discovery ---
    Route::get('/chefs', [ChefController::class, 'index']);
    Route::get('/chefs/{id}', [ChefController::class, 'show']);
    Route::get('/chefs/{id}/menus', [ChefController::class, 'menus']);
    Route::get('/chefs/{id}/reviews', [ChefController::class, 'reviews']);
    
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/featured', [MenuController::class, 'featured']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    
    Route::get('/cuisines', [CuisineController::class, 'index']);
    Route::get('/dietary-preferences', [CuisineController::class, 'dietaryPreferences']);

    // --- Payment Webhook (No Auth - Paystack calls this) ---
    Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

    // ==========================================
    //    AUTHENTICATED ROUTES
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {

        // --- Auth Management ---
        Route::prefix('auth')->group(function () {
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);
            Route::post('/refresh', [AuthController::class, 'refresh']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
            Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
            Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
            Route::post('/device-token', [AuthController::class, 'updateDeviceToken']);
            Route::delete('/account', [AuthController::class, 'deleteAccount']);
        });

        // --- User Profile ---
        Route::get('/user', [UserController::class, 'show']);
        Route::put('/user', [UserController::class, 'update']);
        Route::post('/user/avatar', [UserController::class, 'updateAvatar']);
        Route::put('/user/preferences', [UserController::class, 'updatePreferences']);

        // --- Addresses ---
        Route::apiResource('addresses', AddressController::class);
        Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault']);

        // --- Cart ---
        Route::prefix('cart')->group(function () {
            Route::get('/', [CartController::class, 'index']);
            Route::post('/items', [CartController::class, 'store']);
            Route::put('/items/{id}', [CartController::class, 'update']);
            Route::delete('/items/{id}', [CartController::class, 'destroy']);
            Route::delete('/', [CartController::class, 'clear']);
            Route::get('/summary', [CartController::class, 'summary']);
            // Coupon
            Route::get('/coupon', [CartController::class, 'getCoupon']);
            Route::post('/coupon', [CartController::class, 'applyCoupon']);
            Route::delete('/coupon', [CartController::class, 'removeCoupon']);
        });

        // --- Orders ---
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/active', [OrderController::class, 'active']);
            Route::get('/time-slots', [OrderController::class, 'timeSlots']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::get('/{id}/track', [OrderController::class, 'track']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{id}/reorder', [OrderController::class, 'reorder']);
        });

        // --- Payment ---
        Route::prefix('payment')->group(function () {
            Route::get('/methods', [PaymentController::class, 'methods']);
            Route::post('/initialize', [PaymentController::class, 'initialize']);
            Route::match(['get', 'post'], '/verify', [PaymentController::class, 'verify']);
            Route::get('/history', [PaymentController::class, 'history']);
        });

        // --- Reviews ---
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/{id}', [ReviewController::class, 'show']);
        Route::put('/reviews/{id}', [ReviewController::class, 'update']);
        Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

        // --- Favorites ---
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{chefId}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{chefId}', [FavoriteController::class, 'destroy']);
        Route::get('/favorites/check/{chefId}', [FavoriteController::class, 'check']);

        // --- Notifications ---
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::get('/settings', [NotificationController::class, 'settings']);
            Route::put('/settings', [NotificationController::class, 'updateSettings']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
            Route::delete('/{id}', [NotificationController::class, 'destroy']);
        });

        // ==========================================
        //    CHEF/VENDOR ROUTES (Chef Role Required)
        // ==========================================
        Route::prefix('chef')->middleware('chef')->group(function () {
            // Dashboard
            Route::get('/dashboard', [ChefDashboardController::class, 'dashboard']);

            // Orders Management
            Route::get('/orders', [ChefDashboardController::class, 'orders']);
            Route::get('/orders/{id}', [ChefDashboardController::class, 'showOrder']);
            Route::put('/orders/{id}/status', [ChefDashboardController::class, 'updateOrderStatus']);

            // Menu Management
            Route::get('/menus', [ChefDashboardController::class, 'menus']);
            Route::post('/menus', [ChefDashboardController::class, 'createMenu']);
            Route::put('/menus/{id}', [ChefDashboardController::class, 'updateMenu']);
            Route::delete('/menus/{id}', [ChefDashboardController::class, 'deleteMenu']);
            Route::post('/menus/{id}/toggle-availability', [ChefDashboardController::class, 'toggleMenuAvailability']);

            // Earnings & Statistics
            Route::get('/earnings', [ChefDashboardController::class, 'earnings']);
            Route::get('/statistics', [ChefDashboardController::class, 'statistics']);

            // Reviews
            Route::get('/reviews', [ChefDashboardController::class, 'reviews']);

            // Profile Management
            Route::get('/profile', [ChefDashboardController::class, 'profile']);
            Route::put('/profile', [ChefDashboardController::class, 'updateProfile']);
            Route::post('/profile/setup', [ChefDashboardController::class, 'setupProfile']);

            // Business Settings
            Route::put('/bank-details', [ChefDashboardController::class, 'updateBankDetails']);
            Route::put('/operating-hours', [ChefDashboardController::class, 'updateOperatingHours']);

            // Verification & Documents
            Route::get('/documents', [ChefDashboardController::class, 'getDocuments']);
            Route::post('/documents', [ChefDashboardController::class, 'uploadDocuments']);
            Route::post('/request-verification', [ChefDashboardController::class, 'requestVerification']);

            // Availability Toggle
            Route::post('/toggle-availability', [ChefDashboardController::class, 'toggleAvailability']);
        });

        // ==========================================
        //    ADMIN ROUTES (Admin Role Required)
        // ==========================================
        Route::prefix('admin')->middleware('admin')->group(function () {
            // Dashboard
            Route::get('/dashboard', [AdminController::class, 'dashboard']);

            // Users Management
            Route::get('/users', [AdminController::class, 'users']);
            Route::get('/users/{id}', [AdminController::class, 'showUser']);
            Route::put('/users/{id}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
            Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);

            // Vendors Management
            Route::get('/vendors', [AdminController::class, 'vendors']);
            Route::get('/vendors/pending', [AdminController::class, 'pendingVendors']);
            Route::post('/vendors/{id}/approve', [AdminController::class, 'approveVendor']);
            Route::post('/vendors/{id}/reject', [AdminController::class, 'rejectVendor']);
            Route::post('/vendors/{id}/suspend', [AdminController::class, 'suspendVendor']);
            Route::post('/vendors/{id}/activate', [AdminController::class, 'activateVendor']);

            // Orders Management
            Route::get('/orders', [AdminController::class, 'orders']);

            // Activity Log
            Route::get('/activity', [AdminController::class, 'activity']);
        });

    });
});
