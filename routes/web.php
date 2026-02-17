<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Controllers
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Chef\MenuController;
use App\Http\Controllers\Chef\OrderController; 
use App\Http\Controllers\Chef\ChefProfileController;
use App\Http\Controllers\Chef\WalletController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
//    1. PUBLIC PAGES
// ==========================================
Route::controller(PageController::class)->group(function () {
    Route::get('/', 'landingPage')->name('welcome');
    Route::get('/about', 'about')->name('about');
    Route::get('/how-it-works', 'howItWorks')->name('how-it-works');
    Route::get('/contact', 'contact')->name('contact');
    
    // Legal Pages
    Route::get('/privacy-policy', 'privacy')->name('privacy');
    Route::get('/terms-of-service', 'terms')->name('terms');
    
    // Form Submissions
    Route::post('/contact', 'submitContact')->name('contact.submit');
    Route::post('/subscribe', 'subscribe')->name('newsletter.subscribe');
});

// --- CART ---
Route::controller(CartController::class)->group(function () {
    Route::get('cart', 'index')->name('cart.index');
    Route::get('add-to-cart/{id}', 'add')->name('add.to.cart');
    Route::match(['post', 'patch'], 'update-cart', 'update')->name('update.cart');
    Route::match(['post', 'delete'], 'remove-from-cart', 'remove')->name('remove.from.cart');
});

// --- CHEF LISTING & PUBLIC PROFILES ---
Route::get('/chef', [CustomerController::class, 'index'])->name('chef.index');
Route::get('/chefs', [CustomerController::class, 'index'])->name('chefs.index');

// CRITICAL FIX: The regex constraint prevents this route from eating dashboard routes.
// We added 'personal-info' to the list of exclusions.
Route::get('/chef/{chef}', [CustomerController::class, 'show'])
    ->name('chef.show')
    ->where('chef', '^(?!menus|orders|wallet|profile|personal-info).*$');

// --- SUBSCRIPTIONS ---
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::get('/subscriptions/plans', [SubscriptionController::class, 'plans'])->name('subscriptions.plans');

// --- AUTH ---
Auth::routes(['verify' => true]);


// ==========================================
//    2. AUTHENTICATED ROUTES
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // --- CHECKOUT ---
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/payment/callback', [CheckoutController::class, 'handleGatewayCallback'])->name('payment.callback');

    // ------------------------------------------
    //    ADMIN AREA
    // ------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
        
        Route::controller(AdminController::class)->group(function () {
            Route::get('/users', 'users')->name('users');
            Route::get('/chef', 'chef')->name('chef');
            Route::get('/orders', 'orders')->name('orders');
            
            // Reports & Settings
            Route::get('/reports', 'reports')->name('reports');
            Route::get('/newsletters', 'newsletters')->name('newsletters');
            Route::get('/newsletters/export', 'exportNewsletters')->name('newsletters.export');
            Route::delete('/newsletters/{id}', 'deleteNewsletter')->name('newsletters.delete');
            Route::get('/contact-submissions', 'contactSubmissions')->name('contact-submissions');
            Route::post('/contact-submissions/{id}/read', 'markContactRead')->name('contact-submissions.read');
            Route::post('/contact-submissions/{id}/resolve', 'resolveContact')->name('contact-submissions.resolve');
            Route::delete('/contact-submissions/{id}', 'deleteContact')->name('contact-submissions.delete');
            Route::get('/settings', 'settings')->name('settings');
            Route::post('/settings', 'updateSettings')->name('settings.update');

            // Actions
            Route::post('/users/{id}/toggle', 'toggleUserStatus')->name('users.toggle');
            Route::post('/chef/{id}/verify', 'verifyChef')->name('chef.verify');
            Route::get('/orders/{id}', 'showOrder')->name('orders.show');
        });

        Route::controller(WithdrawalController::class)->group(function () {
            Route::get('/withdrawals', 'index')->name('withdrawals.index');
            Route::post('/withdrawals/{id}/approve', 'approve')->name('withdrawals.approve');
            Route::post('/withdrawals/{id}/reject', 'reject')->name('withdrawals.reject');
        });
    });

    // ------------------------------------------
    //    CHEF AREA
    // ------------------------------------------
    Route::prefix('chef')->name('chef.')->middleware(['chef'])->group(function () {
        
        // Profile (Kitchen & Personal)
        Route::controller(ChefProfileController::class)->group(function () {
            Route::get('/profile', 'show')->name('profile');
            Route::get('/profile/edit', 'edit')->name('profile.edit');
            Route::match(['post', 'put'], '/profile', 'update')->name('profile.update');
            
            // Personal Info (These were being shadowed before!)
            Route::get('/personal-info', 'editPersonal')->name('personal.edit');
            Route::match(['post', 'patch'], '/personal-info', 'updatePersonal')->name('personal.update');
        });

        // Wallet
        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet');
        Route::post('/wallet/withdraw', [WalletController::class, 'requestPayout'])->name('wallet.withdraw');

        // Menus (Using alternate URLs to avoid ModSecurity blocking)
        Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
        Route::get('/menus/create', [MenuController::class, 'create'])->name('menus.create');
        Route::post('/menus', [MenuController::class, 'store'])->name('menus.store');
        Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
        Route::get('/menus/{menu}/manage', [MenuController::class, 'edit'])->name('menus.edit');  // Changed from /edit
        Route::match(['post', 'put', 'patch'], '/menus/{menu}/save', [MenuController::class, 'update'])->name('menus.update');  // Changed URL
        Route::match(['post', 'delete'], '/menus/{menu}/remove', [MenuController::class, 'destroy'])->name('menus.destroy');  // Changed from /destroy
        Route::match(['post', 'patch'], '/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');
        
        // Orders
        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders', 'index')->name('orders.index');
            Route::get('/orders/{id}', 'show')->name('orders.show');
            Route::match(['post', 'patch'], '/orders/{order}', 'update')->name('orders.update'); 
        });
    });

    // ------------------------------------------
    //    CUSTOMER AREA
    // ------------------------------------------
    Route::prefix('customer')->name('customer.')->middleware(['customer'])->group(function () {
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/profile', 'profile')->name('profile');
            Route::match(['post', 'patch'], '/profile', 'updateProfile')->name('profile.update');
            Route::get('/orders', 'orders')->name('orders');
            Route::get('/orders/{id}', 'showOrder')->name('orders.show');
            Route::get('/favorites', 'favorites')->name('favorites');
            
            Route::match(['post', 'patch'], '/orders/{order}/cancel', 'cancelOrder')->name('orders.cancel');
            Route::get('/orders/{order}/retry', 'retryPayment')->name('orders.retry');
        });
    });
});