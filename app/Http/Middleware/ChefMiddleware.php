<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ChefMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Check Spatie Role first (NEW SYSTEM)
        if ($user->hasRole('chef')) {
            return $next($request);
        }
        
        // Fallback to user_type for backward compatibility (OLD SYSTEM)
        if (isset($user->user_type) && $user->user_type === 'chef') {
            return $next($request);
        }
        
        // Neither condition met - unauthorized
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Access denied. Chef access required.'], 403);
        }
        abort(403, 'Access denied. Chef access required.');
    }
}
