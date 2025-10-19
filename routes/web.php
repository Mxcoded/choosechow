<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Chef\MenuController;
use App\Http\Controllers\Chef\OrderController;
use App\Http\Controllers\Chef\ChefProfileController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home/Landing Page
Route::get('/', [PageController::class, 'landingPage'])->name('welcome');

// Static Pages handled by PageController
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy.policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms.of.service');

// Authentication Routes with Email Verification
Auth::routes(['verify' => true]);

// Find Chefs Routes (will need ChefController later)
Route::prefix('chefs')->name('chefs.')->group(function () {
    Route::get('/', function () {
        return view('chefs.index');
    })->name('index');

    Route::get('/{id}', function ($id) {
        return view('chefs.show', compact('id'));
    })->name('show');
});

// Subscription Routes (will need SubscriptionController later)
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', function () {
        return view('subscriptions.index');
    })->name('index');

    Route::get('/plans', function () {
        return view('subscriptions.plans');
    })->name('plans');
});

// Protected Routes (require authentication and email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Admin routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/users', function () {
            return view('admin.users');
        })->name('users');
        Route::get('/chefs', function () {
            return view('admin.chefs');
        })->name('chefs');
        Route::get('/orders', function () {
            return view('admin.orders');
        })->name('orders');
        Route::get('/reports', function () {
            return view('admin.reports');
        })->name('reports');
        Route::get('/settings', function () {
            return view('admin.settings');
        })->name('settings');
    });
    // Chef specific routes
    Route::prefix('chef')->name('chef.')->middleware('chef')->group(function () {
        // REPLACE with new Profile Routes
        Route::get('/profile', [ChefProfileController::class, 'show'])->name('profile');
        Route::get('/profile/edit', [ChefProfileController::class, 'edit'])->name('profile.edit');
        // Uses POST if creating (profile does not exist) or PUT if updating (profile exists)
        Route::match(['post', 'put'], '/profile', [ChefProfileController::class, 'update'])->name('profile.update');
        // Menu routes
        Route::get('/menus', [MenuController::class, 'index'])->name('menus');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
        Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])->name('menus.edit');
        Route::put('/menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
        Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');
        Route::post('/menus/{menu}/toggle-featured', [MenuController::class, 'toggleFeatured'])->name('menus.toggle-featured');
        Route::post('/menus/bulk-update', [MenuController::class, 'bulkUpdate'])->name('menus.bulk-update');
        // Route::get('/orders', function () {
        //     return view('chefs.orders');
        // })->name('orders');

        // REPLACE with resource-like routing for Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    // Customer specific routes  
    Route::prefix('customer')->name('customer.')->middleware('customer')->group(function () {
        Route::get('/profile', function () {
            return view('customer.profile');
        })->name('profile');
        Route::get('/orders', function () {
            return view('customer.orders');
        })->name('orders');
        Route::get('/favorites', function () {
            return view('customer.favorites');
        })->name('favorites');
    });
});

// Fallback route for development
Route::fallback(function () {
    return view('errors.404');
});
