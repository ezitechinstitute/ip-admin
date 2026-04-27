<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorAttendanceController extends Controller
{
    public function index(Request $request) 
    {
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

        // 1. SECURITY: Get Allowed Technologies
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

        $rawDate = trim($request->input('date'));
        $searchTerm = trim($request->input('search')); // Added for Big Data Search

        // Base Query for Attendance List
        $attendanceQuery = DB::table('intern_attendance as ia')
            ->leftJoin('intern_accounts as acc', function ($join) {
                $join->on(DB::raw('TRIM(LOWER(ia.eti_id))'), '=', DB::raw('TRIM(LOWER(acc.eti_id))'));
            })
            ->select('ia.id', 'ia.eti_id', 'acc.name as intern_name', 'acc.int_technology', 'ia.start_shift', 'ia.end_shift', 'ia.duration', 'ia.status')
            ->whereIn('acc.int_technology', $allowedTechNames);

        // --- NEW: Search Logic ---
        if (!empty($searchTerm)) {
            $attendanceQuery->where(function($q) use ($searchTerm) {
                $q->where('acc.name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('ia.eti_id', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('acc.int_technology', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Base Query for Absence KPI
        $absentQuery = DB::table('intern_attendance as ia')
            ->join('intern_accounts as acc', 'ia.eti_id', '=', 'acc.eti_id')
            ->whereIn('acc.int_technology', $allowedTechNames)
            ->where('ia.status', 'Absent');

        // 3. APPLY FILTER LOGIC
        if ($rawDate === 'all') {
            // No date filter needed
        } elseif (empty($rawDate)) {
            $defaultDate = now()->toDateString();
            $attendanceQuery->whereDate('ia.start_shift', $defaultDate);
            $absentQuery->whereDate('ia.start_shift', $defaultDate);
        } else {
            $attendanceQuery->whereDate('ia.start_shift', $rawDate);
            $absentQuery->whereDate('ia.start_shift', $rawDate);
        }

        // --- CRITICAL CHANGE HERE ---
        // Swapped ->get() for ->paginate(15). 
        // This fixes the "Method total does not exist" error and handles 1000s of rows easily.
        $attendance = $attendanceQuery->orderByDesc('ia.id')->paginate(15)->withQueryString();
        
        $absentCount = $absentQuery->count();

        // 4. LEAVE NOTIFICATIONS
        $recentLeaves = DB::table('intern_leaves as l') 
            ->join('intern_accounts as acc', 'l.eti_id', '=', 'acc.eti_id')
            ->select('l.*', 'acc.name as intern_name')
            ->whereIn('acc.int_technology', $allowedTechNames)
            ->where(function($query) {
                $query->whereNull('l.leave_status')
                      ->orWhere('l.leave_status', 0);
            })
            ->orderByDesc('l.created_at')
            ->get();

        return view('content.supervisor.attendance', compact('attendance', 'absentCount', 'recentLeaves'));
    }
}