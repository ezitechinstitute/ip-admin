<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to validate that the authenticated guard matches the session guard.
 * This prevents session collision issues when multiple guards are used simultaneously.
 */
class ValidateGuardSession
{
    /**
     * Handle an incoming request.
     * Validates that the current guard session matches the authenticated user.
     */
    public function handle(Request $request, Closure $next, string $guard = 'admin'): Response
    {
        // Get the stored guard from session
        $sessionGuard = session('auth_guard');
        
        // If no guard is stored in session and user is not authenticated, allow
        if (!$sessionGuard && !Auth::guard($guard)->check()) {
            return $next($request);
        }
        
        // If a guard is stored but it doesn't match the expected guard, logout and redirect
        if ($sessionGuard && $sessionGuard !== $guard) {
            // Session belongs to a different guard, clear it
            Auth::guard($guard)->logout();
            session()->invalidate();
            return redirect()->route('login')->withErrors(['error' => 'Session guard mismatch. Please login again.']);
        }
        
        // Verify that if a user is authenticated, the guard matches the session
        if (Auth::guard($guard)->check()) {
            $sessionGuard = session('auth_guard');
            if ($sessionGuard && $sessionGuard !== $guard) {
                Auth::guard($guard)->logout();
                return redirect()->route('login')->withErrors(['error' => 'Authentication mismatch.']);
            }
        }
        
        return $next($request);
    }
}
