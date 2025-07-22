<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceMode = Setting::get('maintenance_mode', false);
        
        if ($maintenanceMode && $maintenanceMode !== '0') {
            $maintenanceMessage = Setting::get('maintenance_message', 'System is under maintenance. Please try again later.');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $maintenanceMessage,
                    'error' => 'maintenance_mode'
                ], 503);
            }
            
            return response()->view('errors.maintenance', [
                'message' => $maintenanceMessage
            ], 503);
        }

        return $next($request);
    }
} 