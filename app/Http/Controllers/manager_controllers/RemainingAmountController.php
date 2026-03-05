<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemainingAmountController extends Controller
{
    /**
     * Show the Partial Payment & Remaining Balance page
     */
    public function index()
    {

        $manager = Auth::guard('manager')->user();

    if (!$manager) {
        return redirect()->route('manager.login');
    }

    // --- Privilege Check Start ---
    // 'view_remaining_balance' ki jagah apni database wali key use karein
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_remaining_amount')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Remaining Balance details.']);
    }

        // Dummy data for frontend testing
        $payments = [
            [
                'id' => 1,
                'paid' => 3000,
                'remaining' => 3000,
                'due_date' => '2026-03-15',
                'status' => 'Pending',
            ],
            [
                'id' => 2,
                'paid' => 6000,
                'remaining' => 0,
                'due_date' => '-',
                'status' => 'Paid',
            ],
        ];

        return view('pages.manager.remainingBalance.remainingBalance');
    }
}