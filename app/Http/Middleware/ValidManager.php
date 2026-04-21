<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the manager is authenticated via the manager guard
        if (!Auth::guard('manager')->check()) {
            return redirect()->route('login')->withErrors(['error' => 'Please login as Manager first!']);
        }

        // Validate that the session guard matches the expected guard
        $sessionGuard = session('auth_guard');
        if ($sessionGuard && $sessionGuard !== 'manager' && $sessionGuard !== 'supervisor') {
            // Session belongs to a different guard, logout and redirect
            Auth::guard('manager')->logout();
            session()->invalidate();
            return redirect()->route('login')->withErrors(['error' => 'Session guard mismatch. Please login again.']);
        }

        // If session guard is not set, set it now based on loginas
        if (!$sessionGuard) {
            $loginas = session('loginas', 'Manager');
            $guardType = $loginas === 'Supervisor' ? 'supervisor' : 'manager';
            session(['auth_guard' => $guardType]);
        }

        return $next($request);
    }
}