<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SecurityAuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // Only log for authenticated users and certain HTTP methods
        if (Auth::check() && in_array($request->method(), ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])) {
            $this->logActivity($request, $response);
        }

        return $response;
    }

    private function logActivity(Request $request, Response $response): void
    {
        try {
            $user = Auth::user();
            $path = $request->path();
            $method = $request->method();
            
            // Determine action based on route and method
            $action = $this->determineAction($path, $method);
            
            // Skip logging for certain routes to avoid noise
            if ($this->shouldSkipLogging($path)) {
                return;
            }

            // Determine if the request was successful
            $success = $response->getStatusCode() >= 200 && $response->getStatusCode() < 400;
            $errorMessage = null;
            
            if (!$success) {
                $errorMessage = 'HTTP ' . $response->getStatusCode();
            }

            SecurityAuditLog::logActivity(
                $user->userID,
                $action,
                null, // table_affected will be determined by specific actions
                null, // record_id will be determined by specific actions
                null, // old_values
                null, // new_values
                $success,
                $errorMessage
            );
            
        } catch (\Exception $e) {
            // Don't let logging failures break the application
            Log::error('Failed to log user activity: ' . $e->getMessage());
        }
    }

    private function determineAction(string $path, string $method): string
    {
        // Map common routes to actions
        $routeActions = [
            'dashboard' => 'view_dashboard',
            'reports' => 'view_reports',
            'reports/activity-logs' => 'view_activity_logs',
            'reports/security-logs' => 'view_security_logs', 
            'reports/user-activity' => 'view_user_activity',
            'reports/export' => 'export_activity_logs',
            'users' => $method === 'GET' ? 'view_users' : 'manage_users',
            'create-account' => $method === 'GET' ? 'view_create_account' : 'create_account',
            'profile' => $method === 'GET' ? 'view_profile' : 'update_profile',
        ];

        // Check for specific patterns
        foreach ($routeActions as $pattern => $action) {
            if (str_contains($path, $pattern)) {
                return $action;
            }
        }

        // Default action based on method
        return match($method) {
            'GET' => 'view_page',
            'POST' => 'create_data',
            'PUT', 'PATCH' => 'update_data', 
            'DELETE' => 'delete_data',
            default => 'unknown_action'
        };
    }

    private function shouldSkipLogging(string $path): bool
    {
        $skipPatterns = [
            'livewire',
            'api/csrf',
            '_debugbar',
            'telescope',
            'favicon.ico',
            'robots.txt',
            '.css',
            '.js',
            '.png',
            '.jpg',
            '.gif',
            '.svg'
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
