<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share 'cartCount' with EVERY view in the app
        View::composer('*', function ($view) {
            $cartCount = 0;
            
            if (Auth::check()) {
                // Sum the 'quantity' column, so 2 plates of rice counts as 2, not 1 item.
                $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
            }
            
            $view->with('cartCount', $cartCount);
        });
    }
}
