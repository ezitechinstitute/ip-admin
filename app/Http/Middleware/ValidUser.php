<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if admin is authenticated
        if(!Auth::guard('admin')->check()){
            return redirect()->route('login')->withErrors(['error' => 'Please login first!']);
        }

        // Validate that the session guard matches the expected guard
        $sessionGuard = session('auth_guard');
        if ($sessionGuard && $sessionGuard !== 'admin') {
            // Session belongs to a different guard, logout and redirect
            Auth::guard('admin')->logout();
            session()->invalidate();
            return redirect()->route('login')->withErrors(['error' => 'Session guard mismatch. Please login again.']);
        }

        // If session guard is not set, set it now
        if (!$sessionGuard) {
            session(['auth_guard' => 'admin']);
        }
        
        return $next($request);
    }
}
