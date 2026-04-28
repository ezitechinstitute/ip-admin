<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternAttendanceController extends Controller
{
   private $officeLatitude = 33.6145;   // Eziline Software House
private $officeLongitude = 73.0589;  // Amna Plaza, Rawalpindi
private $officeRadius = 100;          // 100 meters

    public function index(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }

        $etiId = $intern->eti_id;
        $internshipType = $intern->internship_type ?? 'Remote';
        
        // Get all attendance records
        $allAttendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->orderBy('start_shift', 'desc')
            ->get();
        
        $attendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->orderBy('start_shift', 'desc')
            ->paginate(10);

        // Calculate stats
        $totalDays = $allAttendance->count();
        $presentDays = $allAttendance->where('status', 1)->count();
        $absentDays = $allAttendance->where('status', 0)->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;

        $currentMonth = Carbon::now()->format('Y-m');
        $monthlyAttendance = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->where('start_shift', 'LIKE', $currentMonth . '%')
            ->get();
        
        $monthlyPresent = $monthlyAttendance->where('status', 1)->count();
        $monthlyTotal = $monthlyAttendance->count();
        $monthlyPercentage = $monthlyTotal > 0 ? round(($monthlyPresent / $monthlyTotal) * 100) : 0;

        $stats = [
            'total_days' => $totalDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'attendance_percentage' => $attendancePercentage,
            'monthly_percentage' => $monthlyPercentage,
            'monthly_present' => $monthlyPresent,
            'monthly_total' => $monthlyTotal,
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

    // Calculate distance between two coordinates
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        
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

    // Check if location is within office radius
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
            'distance' => round($distance, 2),
            'max_radius' => $this->officeRadius
        ];
    }

    public function checkIn(Request $request)
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        $etiId = $intern->eti_id;
        $internshipType = $intern->internship_type ?? 'Remote';

        // Check if already checked in today
        $existing = DB::table('intern_attendance')
            ->where('eti_id', $etiId)
            ->whereDate('start_shift', Carbon::today())
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already checked in today!');
        }

        $now = Carbon::now();
        $updateData = [
            'eti_id' => $etiId,
            'email' => $intern->email,
            'start_shift' => $now,
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // For Remote interns - Time based check-in
        if ($internshipType == 'Remote') {
            $currentHour = (int) $now->format('H');
            
            // Remote check-in allowed between 9 AM and 6 PM
            if ($currentHour < 9) {
                return back()->with('error', '⏰ Working hours start at 9:00 AM. Please wait.');
            }
            
            if ($currentHour >= 18) {
                return back()->with('error', '⏰ Working hours ended at 6:00 PM. You cannot check in now.');
            }
            
            $updateData['checkin_method'] = 'remote_time';
            $message = "✅ Checked in successfully at {$now->format('h:i A')} (Remote)";
        }
        
        // For Onsite interns - GPS Location required
        else {
            // Validate location data
            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                return back()->with('error', '📍 Location access is required for check-in. Please enable GPS.');
            }
            
            $locationCheck = $this->isWithinOfficeRadius($request->latitude, $request->longitude);
            
            if (!$locationCheck['within']) {
                return back()->with('error', "📍 You are not within office range. Your distance: {$locationCheck['distance']}m. Required: within {$locationCheck['max_radius']}m.");
            }
            
            $updateData['checkin_latitude'] = $request->latitude;
            $updateData['checkin_longitude'] = $request->longitude;
            $updateData['checkin_accuracy'] = $locationCheck['distance'];
            $updateData['checkin_method'] = 'gps';
            $message = "✅ Checked in successfully at {$now->format('h:i A')} (Location verified - {$locationCheck['distance']}m from office)";
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
            ->whereDate('start_shift', Carbon::today())
            ->whereNull('end_shift')
            ->first();

        if (!$attendanceRecord) {
            return back()->with('error', 'No active check-in found for today!');
        }

        $endTime = Carbon::now();
        $startTime = Carbon::parse($attendanceRecord->start_shift);
        $durationHours = round($startTime->diffInMinutes($endTime) / 60, 2);

        $updateData = [
            'end_shift' => $endTime,
            'duration' => $durationHours,
            'updated_at' => $endTime,
        ];

        // For Onsite, optionally verify location on checkout
        if ($internshipType == 'Onsite') {
            $updateData['checkout_method'] = 'gps';
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $locationCheck = $this->isWithinOfficeRadius($request->latitude, $request->longitude);
                $updateData['checkout_latitude'] = $request->latitude;
                $updateData['checkout_longitude'] = $request->longitude;
            }
            $message = "✅ Checked out at {$endTime->format('h:i A')} | Total hours: {$durationHours}";
        } else {
            // Remote check-out - always allowed
            $currentHour = (int) $endTime->format('H');
            if ($currentHour < 9) {
                return back()->with('error', 'You cannot check out before 9:00 AM.');
            }
            $updateData['checkout_method'] = 'remote_time';
            $message = "✅ Checked out at {$endTime->format('h:i A')} | Total hours: {$durationHours} (Remote)";
        }

        DB::table('intern_attendance')
            ->where('id', $attendanceRecord->id)
            ->update($updateData);

        return redirect()->route('intern.attendance')->with('success', $message);
    }
}