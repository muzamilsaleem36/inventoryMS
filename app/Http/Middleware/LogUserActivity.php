<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivityLog;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Only log for authenticated users
        if (Auth::check()) {
            $this->logActivity($request, $response);
        }
        
        return $response;
    }
    
    /**
     * Log user activity
     */
    protected function logActivity(Request $request, Response $response): void
    {
        // Skip logging for certain routes
        $skipRoutes = [
            'api/health',
            'api/ping',
            'heartbeat',
            'debugbar',
            'telescope',
        ];
        
        $currentRoute = $request->route() ? $request->route()->getName() : $request->path();
        
        foreach ($skipRoutes as $skipRoute) {
            if (strpos($currentRoute, $skipRoute) !== false) {
                return;
            }
        }
        
        // Only log successful requests
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $action = $this->getActionFromRequest($request);
            $description = $this->getDescriptionFromRequest($request);
            
            if ($action && $description) {
                UserActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $action,
                    'description' => $description,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }
    }
    
    /**
     * Get action from request
     */
    protected function getActionFromRequest(Request $request): ?string
    {
        $method = $request->method();
        $route = $request->route() ? $request->route()->getName() : null;
        
        if ($route) {
            if (strpos($route, '.store') !== false) {
                return 'created';
            }
            if (strpos($route, '.update') !== false) {
                return 'updated';
            }
            if (strpos($route, '.destroy') !== false) {
                return 'deleted';
            }
            if (strpos($route, '.show') !== false) {
                return 'viewed';
            }
            if (strpos($route, '.index') !== false) {
                return 'listed';
            }
        }
        
        switch ($method) {
            case 'POST':
                return 'created';
            case 'PUT':
            case 'PATCH':
                return 'updated';
            case 'DELETE':
                return 'deleted';
            case 'GET':
                return 'viewed';
            default:
                return null;
        }
    }
    
    /**
     * Get description from request
     */
    protected function getDescriptionFromRequest(Request $request): ?string
    {
        $route = $request->route() ? $request->route()->getName() : null;
        $path = $request->path();
        
        if ($route) {
            $routeParts = explode('.', $route);
            $resource = ucfirst($routeParts[0] ?? 'Resource');
            $action = $routeParts[1] ?? 'action';
            
            $actionMap = [
                'index' => 'viewed list of',
                'show' => 'viewed details of',
                'create' => 'opened create form for',
                'store' => 'created new',
                'edit' => 'opened edit form for',
                'update' => 'updated',
                'destroy' => 'deleted',
            ];
            
            $actionText = $actionMap[$action] ?? $action;
            
            return ucfirst($actionText) . ' ' . strtolower($resource);
        }
        
        return 'Accessed ' . $path;
    }
} 