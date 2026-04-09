<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManagerLeaveController extends Controller
{

  public function intern()
{
    $manager = Auth::guard('manager')->user();
    if (!$manager) return redirect()->route('login');
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_intern_leaves')) {
        return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Access Denied.']);
    }
    $managerId = $manager->manager_id;

    // Fetch manager permissions (tech + intern type)
    $allowedTechsData = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $managerId)
        ->where('technologies.status', 1)
        ->get();        

    $allowedTechNames = $allowedTechsData->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechsData->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // Fetch leaves only for interns in allowed techs + types
    $leaves = DB::table('intern_leaves')
        ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
            $q->whereIn('technology', $allowedTechNames);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);


    return view('pages.manager.attendance.intern', compact('leaves', 'manager'));
}



    // Approve leave
    public function approve($id)
    {
        DB::table('intern_leaves')
            ->where('leave_id', $id)
            ->update([
                'leave_status' => 1
            ]);

        return redirect()->back()->with('success', 'Leave Approved Successfully');
    }

    // Reject leave
    public function reject($id)
    {
        DB::table('intern_leaves')
            ->where('leave_id', $id)
            ->update([
                'leave_status' => 0
            ]);

        return redirect()->back()->with('success', 'Leave Rejected Successfully');
    }


 public function supervisor()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('login');
        if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_intern_leaves')) {
                return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Access Denied.']);
            }
        $managerId = $manager->manager_id;

        // 1️⃣ Get all supervisors assigned to this manager
        $assignedSupervisors = DB::table('manager_supervisor_assignments')
            ->where('manager_id', $managerId)
            ->pluck('supervisor_id'); // Only get the supervisor IDs

        // 2️⃣ Get leaves of these supervisors
        $leaves = DB::table('supervisor_leaves')
            ->whereIn('supervisor_id', $assignedSupervisors) // Only leaves of assigned supervisors
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pages.manager.attendance.supervisorLeaveApprove', compact('leaves', 'manager'));
    }

    /**
     * Approve a leave (optional: manager-level approval)
     */
    public function supervisorapprove($leaveId)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        DB::table('supervisor_leaves')
            ->where('leave_id', $leaveId)
            ->update([
                'leave_status' => 1, // Approved
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Leave approved successfully.');
    }

    /**
     * Reject a leave (optional)
     */
    public function supervisorreject($leaveId)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        DB::table('supervisor_leaves')
            ->where('leave_id', $leaveId)
            ->update([
                'leave_status' => 0, // Rejected
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Leave rejected successfully.');
    }
}
