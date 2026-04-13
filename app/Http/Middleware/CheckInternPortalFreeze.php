<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInternPortalFreeze
{
    /**
     * Check if intern portal is frozen and prevent access to restricted features
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $intern = Auth::guard('intern')->user();

        if ($intern && $intern->isFrozen()) {
            // Check if this is an API request or view request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your internship portal is frozen due to unpaid invoice. Please contact your manager to resolve the payment.',
                    'portal_status' => 'frozen'
                ], 403);
            }

            // For view requests, redirect with message
            return redirect()->route('intern.dashboard')
                ->with('error', 'Your internship portal is frozen due to unpaid invoice. Please contact your manager to resolve the payment.');
        }

        return $next($request);
    }
}
