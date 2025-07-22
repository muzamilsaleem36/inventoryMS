<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckStoreAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Admin can access all stores
        if ($user->hasRole('admin')) {
            return $next($request);
        }
        
        // Check if user has store access
        if (!$user->store_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You are not assigned to any store.',
                    'error' => 'no_store_access'
                ], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'You are not assigned to any store.');
        }
        
        // Check if requested store matches user's store (if store parameter exists)
        if ($request->route('store') && $request->route('store')->id !== $user->store_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'You do not have access to this store.',
                    'error' => 'store_access_denied'
                ], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to this store.');
        }

        return $next($request);
    }
} 