<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController; // NEW
use App\Http\Controllers\Admin\AdminController;   // NEW
use App\Http\Controllers\Customer\CustomerController; // NEW
use App\Http\Controllers\Chef\MenuController;
use App\Http\Controllers\Chef\OrderController;
use App\Http\Controllers\Chef\ChefProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Pages
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'landingPage')->name('welcome');
    Route::get('/how-it-works', 'howItWorks')->name('how-it-works');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::get('/privacy', 'privacy')->name('privacy.policy');
    Route::get('/terms', 'terms')->name('terms.of.service');
});

// Authentication
Auth::routes(['verify' => true]);

// Public Chef Discovery
// Public Chef Routes (No auth required to view)
Route::get('/chefs', [CustomerController::class, 'index'])->name('chefs.index');
Route::get('/chefs/{chef}', [CustomerController::class, 'show'])->name('chefs.show');

// Subscriptions (Public Plans)
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- ADMIN ROUTES ---
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/users', 'users')->name('users');
            Route::get('/chefs', 'chefs')->name('chefs');
            Route::get('/orders', 'orders')->name('orders');
            Route::get('/reports', 'reports')->name('reports');
            Route::get('/settings', 'settings')->name('settings');
        });
    });

    // --- CHEF ROUTES ---
    Route::prefix('chef')->name('chef.')->middleware(['role:chef'])->group(function () {
        // Profile
        Route::controller(ChefProfileController::class)->group(function () {
            Route::get('/profile', 'show')->name('profile');
            Route::get('/profile/edit', 'edit')->name('profile.edit');
            Route::match(['post', 'put'], '/profile', 'update')->name('profile.update');
        });

        // Menus
        Route::resource('menus', MenuController::class);

        // --- Custom Menu Actions ---
        Route::prefix('menus/{menu}')->name('menus.')->group(function () {
            Route::patch('toggle', [MenuController::class, 'toggleAvailability'])->name('toggle');
            Route::post('toggle-featured', [MenuController::class, 'toggleFeatured'])->name('toggle-featured'); // <--- ADD THIS
        });

        Route::post('menus/bulk-update', [MenuController::class, 'bulkUpdate'])->name('menus.bulk-update');

        // Orders
        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    // --- CUSTOMER ROUTES ---
    Route::prefix('customer')->name('customer.')->middleware(['role:customer'])->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::get('/orders', 'orders')->name('orders');
            Route::get('/favorites', 'favorites')->name('favorites');
            Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        });
    });
});
