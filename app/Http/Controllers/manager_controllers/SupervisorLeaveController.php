<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorLeaveController extends Controller
{
    /**
     * Display a paginated list of leaves for this supervisor.
     */
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        // Fetch leaves assigned to this supervisor
        $leaves = DB::table('intern_leaves')
            ->where('manager_id', $manager->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.manager.leaves.index', compact('leaves'));
    }

    /**
     * Approve a leave.
     */
    public function approve($leaveId)
    {
        $supervisor = Auth::guard('supervisor')->user();
        if (!$supervisor) return redirect()->route('supervisor.login');

        DB::table('intern_leaves')
            ->where('leave_id', $leaveId)
            ->where('supervisor_id', $supervisor->id)
            ->update([
                'leave_status' => 1, // 1 = approved
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Leave approved successfully.');
    }

    /**
     * Reject a leave.
     */
    public function reject($leaveId)
    {
        $supervisor = Auth::guard('supervisor')->user();
        if (!$supervisor) return redirect()->route('supervisor.login');

        DB::table('intern_leaves')
            ->where('leave_id', $leaveId)
            ->where('supervisor_id', $supervisor->id)
            ->update([
                'leave_status' => 0, // 0 = rejected
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Leave rejected successfully.');
    }
}
