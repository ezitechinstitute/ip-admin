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
        // if (Auth::guard('manager')->check()) {
        //     return $next($request);
        // }
         if (Auth::guard('manager')->check()) {
            $manager = Auth::guard('manager')->user();

            // Check if the manager is logged in as Supervisor
            if ($manager->loginas === 'Manager') {
                return $next($request); // Allow access
            }
        }

        // Redirect to manager login if not authenticated
        return redirect()->route('login')->withErrors(['error' => 'Please login as Manager first!']);
    }
}