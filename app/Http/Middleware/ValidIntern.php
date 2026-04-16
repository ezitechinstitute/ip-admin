<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidIntern
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the intern is authenticated via the intern guard
        if (Auth::guard('intern')->check()) {
            return $next($request);
        }

        // Redirect to login if not authenticated
        return redirect()->route('login')->withErrors(['error' => 'Please login as Intern first!']);
    }
}
