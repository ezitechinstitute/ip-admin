<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidSupervisor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if supervisor is logged in
        if (!session()->has('manager_id') || session('loginas') !== 'Supervisor') {
            return redirect()->route('login')->with('error', 'Please login as Supervisor to access this page.');
        }

        return $next($request);
    }
}