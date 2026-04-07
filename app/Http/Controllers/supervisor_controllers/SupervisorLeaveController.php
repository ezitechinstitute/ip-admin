<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class SupervisorLeaveController extends Controller
{
    public function index()
{
    $leaves = \Illuminate\Support\Facades\DB::table('intern_leaves')
        ->select(
            'leave_id',
            'name',
            'email',
            'reason',
            'from_date',
            'to_date',
            'leave_status'
        )
        ->orderByDesc('leave_id')
        ->limit(20)
        ->get()
        ->map(function ($leave) {
            
            // 🔥 Transform intern data to match Blade expectations
            $leave->supervisor_id = $leave->name; // show name instead
            $leave->leave_type = 'Intern';        // fake leave type
            $leave->status = $leave->leave_status; // map status

            return $leave;
        });

    return view('content.supervisor.leaves', compact('leaves'));
}
}