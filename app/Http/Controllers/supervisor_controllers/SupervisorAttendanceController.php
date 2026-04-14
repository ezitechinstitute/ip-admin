<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorAttendanceController extends Controller
{
    public function index()
    {
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

        // ==========================================
        // 1. SECURITY: Get Allowed Technologies
        // ==========================================
        $permissionTechIds = \App\Models\SupervisorPermission::where('manager_id', $supervisorId)
            ->pluck('tech_id')
            ->toArray();

        $allowedTechNames = DB::table('technologies')
            ->whereIn('tech_id', $permissionTechIds)
            ->pluck('technology')
            ->toArray();

        if (empty($allowedTechNames)) {
            $allowedTechNames = ['___NO_ACCESS___']; 
        }

        // ==========================================
        // 2. DAILY ATTENDANCE LIST (Filtered securely)
        // ==========================================
        $attendance = DB::table('intern_attendance as ia')
            ->leftJoin('intern_accounts as acc', function ($join) {
                $join->on(
                    DB::raw('TRIM(LOWER(ia.eti_id))'),
                    '=',
                    DB::raw('TRIM(LOWER(acc.eti_id))')
                );
            })
            ->select(
                'ia.id',
                'ia.eti_id',
                'acc.name as intern_name',
                'acc.int_technology', // Added so you can show tech in the table
                'ia.start_shift',
                'ia.end_shift',
                'ia.duration',
                'ia.status'
            )
            ->whereIn('acc.int_technology', $allowedTechNames)
            ->orderByDesc('ia.id')
            ->limit(50) // Good practice to limit so the page doesn't crash on huge data
            ->get();

        // ==========================================
        // 3. ABSENCE TRACKING (Today's stats)
        // ==========================================
        $today = now()->toDateString();
        
        $absentCount = DB::table('intern_attendance as ia')
            ->join('intern_accounts as acc', 'ia.eti_id', '=', 'acc.eti_id')
            ->whereIn('acc.int_technology', $allowedTechNames)
            ->whereDate('ia.start_shift', $today)
            ->where('ia.status', 'Absent')
            ->count();

        // ==========================================
        // 4. LEAVE NOTIFICATIONS (Read-Only)
        // ==========================================
        $recentLeaves = DB::table('intern_leaves as l') 
            ->join('intern_accounts as acc', 'l.eti_id', '=', 'acc.eti_id')
            ->select('l.*', 'acc.name as intern_name')
            ->whereIn('acc.int_technology', $allowedTechNames)
            // Checking for NULL or 0 because leave_status is a tinyint(1)
            ->where(function($query) {
                $query->whereNull('l.leave_status')
                      ->orWhere('l.leave_status', 0);
            })
            ->orderByDesc('l.created_at')
            ->get();

        return view('content.supervisor.attendance', compact('attendance', 'absentCount', 'recentLeaves'));
    }
}