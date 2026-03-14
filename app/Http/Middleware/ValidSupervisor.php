<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidSupervisor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via manager guard
        if (Auth::guard('manager')->check()) {
            $supervisor = Auth::guard('manager')->user();

            // Check if the manager is logged in as Supervisor
            if ($supervisor->loginas === 'Supervisor') {
                return $next($request); // Allow access
            }
        }

        // Not authenticated as manager
        return redirect()->route('login')->withErrors(['error' => 'Please login first!']);
    }
}