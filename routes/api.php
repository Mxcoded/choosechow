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
        });

        // --- Orders ---
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'index']);
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/active', [OrderController::class, 'active']);
            Route::get('/time-slots', [OrderController::class, 'timeSlots']);
            Route::get('/{id}', [OrderController::class, 'show']);
            Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
            Route::post('/{id}/reorder', [OrderController::class, 'reorder']);
        });

        // --- Reviews ---
        Route::get('/reviews', [ReviewController::class, 'index']);
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/{id}', [ReviewController::class, 'show']);

        // --- Favorites ---
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{chefId}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{chefId}', [FavoriteController::class, 'destroy']);
        Route::get('/favorites/check/{chefId}', [FavoriteController::class, 'check']);

        // --- Notifications ---
        Route::prefix('notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
            Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
        });

    });
});
