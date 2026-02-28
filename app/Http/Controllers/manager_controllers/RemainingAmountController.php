<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RemainingAmountController extends Controller
{
    /**
     * Show the Partial Payment & Remaining Balance page
     */
    public function index()
    {
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