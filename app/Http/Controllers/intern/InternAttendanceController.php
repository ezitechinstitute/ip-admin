<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternAttendanceController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get attendance records
        $attendance = DB::table('intern_attendance')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('start_shift', 'desc')
            ->paginate(30);
        
        // Calculate statistics
        $totalDays = DB::table('intern_attendance')
            ->where('eti_id', $intern->eti_id)
            ->count();
        
        $presentDays = DB::table('intern_attendance')
            ->where('eti_id', $intern->eti_id)
            ->where('status', 1)
            ->count();
        
        $absentDays = DB::table('intern_attendance')
            ->where('eti_id', $intern->eti_id)
            ->where('status', 0)
            ->count();
        
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
        
        // Get current month attendance
        $currentMonth = Carbon::now()->format('Y-m');
        $monthlyAttendance = DB::table('intern_attendance')
            ->where('eti_id', $intern->eti_id)
            ->where('start_shift', 'LIKE', $currentMonth . '%')
            ->orderBy('start_shift', 'asc')
            ->get();
        
        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'attendance_percentage' => $attendancePercentage,
        ];
        
        return view('pages.intern.attendance.index', compact('attendance', 'stats', 'monthlyAttendance'));
    }
}