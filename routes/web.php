<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Auth;

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

    // Chef specific routes
    Route::prefix('chef')->name('chef.')->middleware('chef')->group(function () {
        Route::get('/profile', function () {
            return view('chefs.profile');
        })->name('profile');
        Route::get('/menus', function () {
            return view('chefs.menus');
        })->name('menus');
        Route::get('/orders', function () {
            return view('chefs.orders');
        })->name('orders');
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
