<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

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
Route::get('/', [PageController::class, 'home'])->name('home');

// Static Pages handled by PageController
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy.policy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms.of.service');

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

// Authentication Routes (for future use)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Fallback route for development
Route::fallback(function () {
    return view('errors.404');
});