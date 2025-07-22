<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class PosRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limiter = 'default'): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($limiter);
        $decayMinutes = $this->getDecayMinutes($limiter);
        
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $retryAfter = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Too many requests. Please try again later.',
                    'error' => 'rate_limit_exceeded',
                    'retry_after' => $retryAfter
                ], 429);
            }
            
            return response()->view('errors.rate-limit', [
                'retry_after' => $retryAfter
            ], 429);
        }
        
        RateLimiter::hit($key, $decayMinutes * 60);
        
        $response = $next($request);
        
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - RateLimiter::attempts($key)));
        $response->headers->set('X-RateLimit-Reset', now()->addMinutes($decayMinutes)->timestamp);
        
        return $response;
    }
    
    /**
     * Resolve request signature
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return sha1($user->id . '|' . $request->ip());
        }
        
        return sha1($request->ip());
    }
    
    /**
     * Get max attempts for limiter
     */
    protected function getMaxAttempts(string $limiter): int
    {
        $defaultLimits = [
            'default' => 100,
            'api' => 60,
            'login' => 5,
            'sales' => 200,
            'reports' => 10,
            'uploads' => 20,
        ];
        
        $configKey = "api_rate_limit_{$limiter}";
        $settingValue = Setting::get($configKey);
        
        if ($settingValue) {
            return (int) $settingValue;
        }
        
        return $defaultLimits[$limiter] ?? $defaultLimits['default'];
    }
    
    /**
     * Get decay minutes for limiter
     */
    protected function getDecayMinutes(string $limiter): int
    {
        $defaultDecay = [
            'default' => 1,
            'api' => 1,
            'login' => 15,
            'sales' => 1,
            'reports' => 5,
            'uploads' => 10,
        ];
        
        return $defaultDecay[$limiter] ?? $defaultDecay['default'];
    }
} 