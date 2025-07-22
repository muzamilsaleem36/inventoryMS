<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckSetupComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip setup check for setup routes and static assets
        if ($request->routeIs('setup.*') || 
            $request->routeIs('login') || 
            $request->routeIs('logout') ||
            $request->is('setup/*') ||
            $request->is('css/*') ||
            $request->is('js/*') ||
            $request->is('images/*')) {
            return $next($request);
        }

        // Check if setup is completed
        $setupCompleted = Setting::where('key', 'setup_completed')
            ->where('value', true)
            ->exists();

        if (!$setupCompleted) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
} 