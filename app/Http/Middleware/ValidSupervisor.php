<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if supervisor is logged in
        if (!session()->has('manager_id') || session('loginas') !== 'Supervisor') {
            return redirect()->route('login')->with('error', 'Please login as Supervisor to access this page.');
        }

        // Check if authenticated via manager guard
        if (!Auth::guard('manager')->check()) {
            return redirect()->route('login')->with('error', 'Please login as Supervisor to access this page.');
        }

        // Validate that the session guard matches supervisor
        $sessionGuard = session('auth_guard');
        if ($sessionGuard && $sessionGuard !== 'supervisor') {
            // Session guard doesn't match, clear and redirect
            Auth::guard('manager')->logout();
            session()->invalidate();
            return redirect()->route('login')->withErrors(['error' => 'Session guard mismatch. Please login again.']);
        }

        // Ensure auth_guard is set to supervisor
        if (!$sessionGuard) {
            session(['auth_guard' => 'supervisor']);
        }

        return $next($request);
    }
}