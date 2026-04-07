<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternAttendanceController extends Controller
{
    public function index(Request $request)
{
    $intern = Auth::guard('intern')->user();
    
    if (!$intern) {
        return redirect()->route('login');
    }

    $etiId = $intern->eti_id;
    
    // Get all attendance records
    $attendance = DB::table('intern_attendance')
        ->where('eti_id', $etiId)
        ->orderBy('start_shift', 'desc')
        ->get();

    // Calculate stats
    $totalDays = $attendance->count();
    $presentDays = $attendance->where('status', 1)->count();
    $absentDays = $attendance->where('status', 0)->count();
    $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

    $stats = [
        'total_days' => $totalDays,
        'present_days' => $presentDays,
        'absent_days' => $absentDays,
        'attendance_percentage' => $attendancePercentage,
    ];

    // Get today's attendance
    $todayAttendance = DB::table('intern_attendance')
        ->where('eti_id', $etiId)
        ->whereDate('start_shift', Carbon::today())
        ->first();

    return view('pages.intern.attendance.index', compact('attendance', 'stats', 'intern', 'todayAttendance'));
}

    public function checkIn(Request $request)
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $etiId = $intern->eti_id;

        // Check if already checked in today
        $existing = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->whereDate('start_shift', Carbon::today())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already checked in today!');
        }

        $now = Carbon::now();

        DB::table('intern_attendance')->insert([
            'eti_id' => $etiId,
            'email' => $intern->email,
            'start_shift' => $now,
            'status' => 1,
            'created_at' => $now,
        ]);

        return back()->with('success', '✅ Checked in successfully at ' . $now->format('h:i A'));
    }

    public function checkOut(Request $request)
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $etiId = $intern->eti_id;

        // Find today's attendance record
        $attendanceRecord = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->whereDate('start_shift', Carbon::today())
            ->whereNull('end_shift')
            ->first();

        if (!$attendanceRecord) {
            return back()->with('error', 'No active check-in found for today!');
        }

        $endTime = Carbon::now();
        $startTime = Carbon::parse($attendanceRecord->start_shift);
        $durationHours = round($startTime->diffInMinutes($endTime) / 60, 2);

        DB::table('intern_attendance')
            ->where('id', $attendanceRecord->id)
            ->update([
                'end_shift' => $endTime,
                'duration' => $durationHours,
                'updated_at' => $endTime,
            ]);

        return back()->with('success', '✅ Checked out at ' . $endTime->format('h:i A') . ' | Total hours: ' . $durationHours);
    }
}