<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check Spatie Role first (NEW SYSTEM)
        if ($user->hasRole('customer')) {
            return $next($request);
        }
        
        // Fallback to user_type for backward compatibility (OLD SYSTEM)
        if (isset($user->user_type) && $user->user_type === 'customer') {
            return $next($request);
        }
        
        // Neither condition met - unauthorized
        abort(403, 'Access denied. Customer access required.');
    }
}
