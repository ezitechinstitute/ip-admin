<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternAttendanceController extends Controller
{
    private $officeLatitude = 33.6037;   // Eziline Software House, Rawalpindi (Google Maps Verified)
private $officeLongitude = 73.0267;  // Google Maps Verified
private $officeRadius = 200;          // 200m buffer for GPS error

    public function index(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }

        $etiId = $intern->eti_id;
        $internshipType = $intern->internship_type ?? 'Remote';
        
        $allAttendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->orderBy('start_shift', 'desc')
            ->get();
        
        $attendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->orderBy('start_shift', 'desc')
            ->paginate(10);

        $totalDays = $allAttendance->count();
        $presentDays = $allAttendance->where('status', 1)->count();
        $absentDays = $allAttendance->where('status', 0)->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'attendance_percentage' => $attendancePercentage,
            'monthly_percentage' => $attendancePercentage,
            'monthly_present' => $presentDays,
            'monthly_total' => $totalDays,
            'internship_type' => $internshipType,
        ];

        $todayAttendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->whereDate('start_shift', Carbon::today())
            ->first();

        $currentMonthParam = $request->get('month', date('Y-m'));

        return view('pages.intern.attendance.index', compact(
            'attendance', 'allAttendance', 'stats', 'intern', 'todayAttendance', 'currentMonthParam'
        ));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function isWithinOfficeRadius($latitude, $longitude)
    {
        $distance = $this->calculateDistance(
            $this->officeLatitude, 
            $this->officeLongitude, 
            $latitude, 
            $longitude
        );
        
        return [
            'within' => $distance <= $this->officeRadius,
            'distance' => round($distance, 0),
            'max_radius' => $this->officeRadius
        ];
    }

   public function checkIn(Request $request)
{
    $intern = Auth::guard('intern')->user();

    if (!$intern) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    $etiId = $intern->eti_id;
    $internshipType = $intern->internship_type ?? 'Remote';

    $now = Carbon::now('Asia/Karachi');
    $currentHour = (int) $now->format('H');

    $existing = DB::table('intern_attendance')
        ->where('eti_id', $etiId)
        ->whereDate('start_shift', $now->toDateString())
        ->first();

    if ($existing) {
        return back()->with('error', 'You have already checked in today!');
    }

    if ($currentHour < 9 || $currentHour >= 21) {
        return back()->with('error', "⏰ Check-in allowed 9:00 AM - 9:00 PM PKT only. Current PKT: {$now->format('h:i A')}");
    }

    $updateData = [
        'eti_id' => $etiId,
        'email' => $intern->email,
        'start_shift' => $now,
        'status' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ];

    // Onsite - GPS Required
    if ($internshipType == 'Onsite') {
        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            return back()->with('error', '📍 Location access required. Enable GPS.');
        }
        
        $accuracy = $request->accuracy ?? 999;
        if ($accuracy > 100) {
            return back()->with('error', "📍 GPS accuracy poor: {$accuracy}m. Go near window.");
        }
        
        $locationCheck = $this->isWithinOfficeRadius($request->latitude, $request->longitude);
        
        if (!$locationCheck['within']) {
            return back()->with('error', "📍 You are {$locationCheck['distance']}m away. Required: within {$locationCheck['max_radius']}m.");
        }
        
        $updateData['checkin_latitude'] = $request->latitude;
        $updateData['checkin_longitude'] = $request->longitude;
        $updateData['checkin_accuracy'] = $accuracy;
        $updateData['checkin_method'] = 'gps';
        $message = "✅ Checked in at {$now->format('h:i A')} - {$locationCheck['distance']}m from office";
    } else {
        $updateData['checkin_method'] = 'remote_time';
        $message = "✅ Checked in at {$now->format('h:i A')} (Remote)";
    }
    
    DB::table('intern_attendance')->insert($updateData);

    return redirect()->route('intern.attendance')->with('success', $message);
}
    public function checkOut(Request $request)
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $etiId = $intern->eti_id;
        $internshipType = $intern->internship_type ?? 'Remote';

        $attendanceRecord = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->whereDate('start_shift', Carbon::now('Asia/Karachi')->toDateString())
            ->whereNull('end_shift')
            ->first();

        if (!$attendanceRecord) {
            return back()->with('error', 'No active check-in found for today!');
        }

        $endTime = Carbon::now('Asia/Karachi');
        $startTime = Carbon::parse($attendanceRecord->start_shift);
        $durationHours = round($startTime->diffInMinutes($endTime) / 60, 2);

        $updateData = [
            'end_shift' => $endTime,
            'duration' => $durationHours,
            'updated_at' => $endTime,
        ];

        // ONSITE - GPS REQUIRED + Distance Check
        if ($internshipType == 'Onsite') {
            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                return back()->with('error', '📍 Location required for onsite check-out.');
            }
            
            $locationCheck = $this->isWithinOfficeRadius($request->latitude, $request->longitude);
            if (!$locationCheck['within']) {
                return back()->with('error', "📍 You must be at office to check out. Distance: {$locationCheck['distance']}m");
            }
            
            $updateData['checkout_latitude'] = $request->latitude;
            $updateData['checkout_longitude'] = $request->longitude;
            $updateData['checkout_method'] = 'gps';
            $message = "✅ Checked out at {$endTime->format('h:i A')} | Duration: {$durationHours} hours";
        } 
        // REMOTE - Time Checks + Min 4 Hours
        else {
            $currentHour = (int) $endTime->format('H');
            
            if ($currentHour < 9) {
                return back()->with('error', 'Cannot checkout before 9:00 AM.');
            }
            
            if ($currentHour >= 21) {
                return back()->with('error', 'Checkout closed after 9:00 PM.');
            }
            
            if ($durationHours < 4) {
                return back()->with('error', "❌ Minimum 4 hours required. You worked {$durationHours} hours.");
            }
            
            $updateData['checkout_method'] = 'remote_time';
            $message = "✅ Checked out at {$endTime->format('h:i A')} | Duration: {$durationHours} hours (Remote)";
        }

        DB::table('intern_attendance')
            ->where('id', $attendanceRecord->id)
            ->update($updateData);

        return redirect()->route('intern.attendance')->with('success', $message);
    }
}