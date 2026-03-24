<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SupervisorAttendance;
use App\Models\AdminSetting;

class ManagerAttendanceController extends Controller
{
public function supervisorAttendance(Request $request)
{
    $manager = auth()->guard('manager')->user();
    $managerId = $manager->manager_id;
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_supervisor_attendance')) {
        return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Access Denied.']);
    }
    // Get supervisors assigned to this manager
    $assignedSupervisors = DB::table('manager_accounts')
        ->where('assigned_manager', $managerId)
        ->pluck('manager_id');

    // Get supervisors list for filter dropdown
    $supervisorsList = DB::table('manager_accounts')
        ->whereIn('manager_id', $assignedSupervisors)
        ->select('manager_id as id', 'name', 'email')
        ->get();
    // Get pagination limit from settings
    // dd($supervisorsList, $assignedSupervisors);
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);
    // Build attendance query with filters
    $query = SupervisorAttendance::whereIn('supervisor_id', $assignedSupervisors);

    // Apply date filter
    // if ($request->filled('date')) {
    //     $query->whereDate('date', $request->date);
    // } else {
    //     $query->whereDate('date', date('Y-m-d'));
    // }

    // Apply supervisor filter
    if ($request->filled('supervisor_id')) {
        $query->where('supervisor_id', $request->supervisor_id);
    }

    // Get paginated results
   $attendanceRecords = $query->orderBy('date', 'desc')
                           ->orderBy('check_in', 'desc')
                           ->paginate($perPage)
                           ->withQueryString();
    return view('pages.manager.attendance.supervisor', compact('attendanceRecords', 'supervisorsList'));
}
public function attendanceCalendar(Request $request)
{
    $manager = auth()->guard('manager')->user();
    $managerId = $manager->manager_id;

    // Get technologies assigned to this manager
    $managerTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $managerId)
        ->where('technologies.status', 1)
        ->pluck('technologies.technology')
        ->toArray();

    // Get interview types assigned to this manager
    $managerInterviewTypes = DB::table('manager_permissions')
        ->where('manager_id', $managerId)
        ->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // Get supervisors assigned to this manager
    $assignedSupervisors = DB::table('manager_accounts')
        ->where('assigned_manager', $managerId)
        ->pluck('manager_id');

    // Get supervisors list
    $supervisors = DB::table('manager_accounts')
        ->whereIn('manager_id', $assignedSupervisors)
        ->select('manager_id as id', 'name', 'email')
        ->get();

    // Get interns based on technologies and interview types
    $interns = DB::table('intern_table')
        ->whereIn('technology', $managerTechs)
        ->whereIn('interview_type', $managerInterviewTypes)
        ->select('id', 'name', 'email', 'technology')
        ->get();

    // Date range filter logic
    $dateFilter = $request->get('date_filter', 'today');
    $startDate = null;
    $endDate = null;
    $dateLabel = 'Today';

    switch($dateFilter) {
        case 'today':
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $dateLabel = 'Today';
            break;
        case 'yesterday':
            $startDate = date('Y-m-d', strtotime('-1 day'));
            $endDate = date('Y-m-d', strtotime('-1 day'));
            $dateLabel = 'Yesterday';
            break;
        case 'this_week':
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate = date('Y-m-d');
            $dateLabel = 'This Week';
            break;
        case 'last_week':
            $startDate = date('Y-m-d', strtotime('monday last week'));
            $endDate = date('Y-m-d', strtotime('sunday last week'));
            $dateLabel = 'Last Week';
            break;
        case 'this_month':
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-d');
            $dateLabel = 'This Month';
            break;
        case 'last_month':
            $startDate = date('Y-m-01', strtotime('first day of last month'));
            $endDate = date('Y-m-t', strtotime('last day of last month'));
            $dateLabel = 'Last Month';
            break;
        case 'custom':
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $dateLabel = 'Custom Range';
            break;
    }

    // Supervisor attendance based on date range
    $supervisorAttendanceQuery = DB::table('supervisor_attendance as sa')
        ->join('manager_accounts as ma', 'sa.supervisor_id', '=', 'ma.manager_id')
        ->whereIn('sa.supervisor_id', $assignedSupervisors);

    if ($startDate && $endDate) {
        $supervisorAttendanceQuery->whereBetween('sa.date', [$startDate, $endDate]);
    }

    $supervisorAttendance = $supervisorAttendanceQuery
        ->select('sa.*', 'ma.name as supervisor_name', 'ma.email as supervisor_email')
        ->orderBy('sa.date', 'desc')
        ->get();

    // Intern attendance based on date range
    $internAttendanceQuery = DB::table('intern_attendance as ia')
        ->join('intern_table as it', 'ia.eti_id', '=', 'it.id')
        ->whereIn('it.technology', $managerTechs)
        ->whereIn('it.interview_type', $managerInterviewTypes);

    if ($startDate && $endDate) {
        $internAttendanceQuery->whereBetween(DB::raw('DATE(ia.start_shift)'), [$startDate, $endDate]);
    }

    $internAttendance = $internAttendanceQuery
        ->select('ia.*', 'it.name', 'it.email', 'it.id as intern_id', 'it.technology')
        ->orderBy('ia.start_shift', 'desc')
        ->get();

    // Supervisor calendar events
    $supervisorCalendarEvents = [];
    foreach ($supervisorAttendance as $record) {
        $checkIn = $record->check_in ? \Carbon\Carbon::parse($record->check_in)->format('h:i A') : '—';
        $checkOut = $record->check_out ? \Carbon\Carbon::parse($record->check_out)->format('h:i A') : '—';
        
        $hours = 0;
        $minutes = 0;
        if ($record->check_in && $record->check_out) {
            $checkInTime = \Carbon\Carbon::parse($record->check_in);
            $checkOutTime = \Carbon\Carbon::parse($record->check_out);
            $hours = $checkOutTime->diffInHours($checkInTime);
            $minutes = $checkOutTime->diffInMinutes($checkInTime) % 60;
        }

        $supervisorCalendarEvents[] = [
            'title' => $record->supervisor_name ?? 'Unknown',
            'start' => $record->date,
            'color' => $record->check_in && $record->check_out ? '#28a745' : ($record->check_in ? '#ffc107' : '#6c757d'),
            'textColor' => '#ffffff',
            'extendedProps' => [
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'hours' => $hours,
                'minutes' => $minutes
            ]
        ];
    }

    // Intern calendar events
    $internCalendarEvents = [];
    foreach ($internAttendance as $record) {
        $date = \Carbon\Carbon::parse($record->start_shift)->format('Y-m-d');
        $startShift = $record->start_shift ? \Carbon\Carbon::parse($record->start_shift)->format('h:i A') : '—';
        $endShift = $record->end_shift ? \Carbon\Carbon::parse($record->end_shift)->format('h:i A') : '—';

        $internCalendarEvents[] = [
            'title' => $record->name . ' (' . $record->technology . ')',
            'start' => $date,
            'color' => $record->status == 1 ? '#28a745' : '#dc3545',
            'textColor' => '#ffffff',
            'extendedProps' => [
                'intern_id' => $record->intern_id,
                'eti_id' => $record->eti_id,
                'technology' => $record->technology,
                'start_shift' => $startShift,
                'end_shift' => $endShift,
                'duration' => $record->duration ?? 0
            ]
        ];
    }

    return view('pages.manager.attendance.attendanceCalendar', compact(
        'supervisors',
        'interns',
        'supervisorAttendance',
        'internAttendance',
        'supervisorCalendarEvents',
        'internCalendarEvents',
        'managerTechs',
        'dateFilter',
        'startDate',
        'endDate',
        'dateLabel'
    ));
}
}
