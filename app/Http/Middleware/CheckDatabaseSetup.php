<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class CheckDatabaseSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for auto-setup routes
        if ($request->is('auto-setup*') || $request->is('api/*')) {
            return $next($request);
        }

        // Check if auto setup is enabled
        if (!env('AUTO_SETUP_ENABLED', true)) {
            return $next($request);
        }

        // Check if setup is already completed
        if (env('SETUP_COMPLETED', false)) {
            return $next($request);
        }

        // Try to check database connection and tables
        try {
            // Test database connection
            DB::connection()->getPdo();
            
            // Check if core tables exist
            if (!Schema::hasTable('users') || !Schema::hasTable('settings') || !Schema::hasTable('stores')) {
                // Database exists but tables don't - redirect to auto setup
                return redirect()->route('auto-setup.index');
            }
            
            // Check if setup is actually completed by looking for admin user
            $adminExists = DB::table('users')->where('role', 'admin')->exists();
            if (!$adminExists) {
                return redirect()->route('auto-setup.index');
            }
            
            // Everything looks good, update environment
            $this->updateEnvironmentValue('SETUP_COMPLETED', 'true');
            
        } catch (\Exception $e) {
            // Database connection failed - redirect to auto setup
            return redirect()->route('auto-setup.index');
        }

        return $next($request);
    }

    /**
     * Update environment value
     */
    private function updateEnvironmentValue($key, $value)
    {
        $envFile = base_path('.env');
        
        if (file_exists($envFile)) {
            $env = file_get_contents($envFile);
            
            // Update existing key
            if (strpos($env, $key . '=') !== false) {
                $env = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $env);
            } else {
                // Add new key
                $env .= "\n" . $key . '=' . $value;
            }
            
            file_put_contents($envFile, $env);
        }
    }
} 