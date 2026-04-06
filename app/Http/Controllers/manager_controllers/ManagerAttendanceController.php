<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AdminSetting;

class ManagerAttendanceController extends Controller
{
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

public function internAttendance(Request $request)
{
    $manager = auth()->guard('manager')->user();
    $managerId = $manager->manager_id;

    // Check permission
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_intern_attendance')) {
        return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Access Denied.']);
    }

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

    // Get pagination limit from settings
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // Build intern attendance query with filters
    $query = DB::table('intern_attendance as ia')
        ->join('intern_table as it', 'ia.eti_id', '=', 'it.id')
        ->whereIn('it.technology', $managerTechs)
        ->whereIn('it.interview_type', $managerInterviewTypes)
        ->select('ia.id', 'ia.eti_id', 'ia.email', 'ia.start_shift', 'ia.end_shift', 'ia.duration', 'ia.status', 'it.name', 'it.technology');

    // Apply date filter
    if ($request->filled('date')) {
        $query->whereDate('ia.start_shift', $request->date);
    }

    // Apply intern filter
    if ($request->filled('intern_id')) {
        $query->where('ia.eti_id', $request->intern_id);
    }

    // Apply status filter
    if ($request->filled('status')) {
        $query->where('ia.status', $request->status);
    }

    // Get paginated results
    $attendanceRecords = $query->orderBy('ia.start_shift', 'desc')
                              ->paginate($perPage)
                              ->withQueryString();

    // Get interns list for filter dropdown
    $internsList = DB::table('intern_table')
        ->whereIn('technology', $managerTechs)
        ->whereIn('interview_type', $managerInterviewTypes)
        ->select('id as eti_id', 'name', 'email', 'technology')
        ->get();

    return view('pages.manager.attendance.internAttendance', compact('attendanceRecords', 'internsList'));
}

public function attendanceManagement(Request $request)
{
    $manager = auth()->guard('manager')->user();
    $managerId = $manager->manager_id;
    $tab = $request->get('tab', 'supervisor'); // Default to supervisor attendance tab

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

    // Get pagination limit from settings
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // ==================== INTERN ATTENDANCE ====================
    $internAttendanceQuery = DB::table('intern_attendance as ia')
        ->join('intern_table as it', 'ia.eti_id', '=', 'it.id')
        ->whereIn('it.technology', $managerTechs)
        ->whereIn('it.interview_type', $managerInterviewTypes)
        ->select('ia.id', 'ia.eti_id', 'ia.email', 'ia.start_shift', 'ia.end_shift', 'ia.duration', 'ia.status', 'it.name', 'it.technology');

    if ($request->filled('intern_date')) {
        $internAttendanceQuery->whereDate('ia.start_shift', $request->intern_date);
    }

    if ($request->filled('intern_id')) {
        $internAttendanceQuery->where('ia.eti_id', $request->intern_id);
    }

    if ($request->filled('intern_status')) {
        $internAttendanceQuery->where('ia.status', $request->intern_status);
    }

    $internAttendance = $internAttendanceQuery->orderBy('ia.start_shift', 'desc')
                                            ->paginate($perPage, ['*'], 'intern_page')
                                            ->withQueryString();

    $internsList = DB::table('intern_table')
        ->whereIn('technology', $managerTechs)
        ->whereIn('interview_type', $managerInterviewTypes)
        ->select('id as eti_id', 'name', 'email', 'technology')
        ->get();

    // ==================== SUPERVISOR ATTENDANCE ====================
    $supervisorAttendanceQuery = DB::table('supervisor_attendance as sa')
        ->whereIn('sa.supervisor_id', $assignedSupervisors)
        ->select('sa.*');

    if ($request->filled('supervisor_date')) {
        $supervisorAttendanceQuery->whereDate('sa.date', $request->supervisor_date);
    }

    if ($request->filled('supervisor_id')) {
        $supervisorAttendanceQuery->where('sa.supervisor_id', $request->supervisor_id);
    }

    $supervisorAttendance = $supervisorAttendanceQuery->orderBy('sa.date', 'desc')
                                                    ->orderBy('sa.check_in', 'desc')
                                                    ->paginate($perPage, ['*'], 'supervisor_page')
                                                    ->withQueryString();

    $supervisorsList = DB::table('manager_accounts')
        ->whereIn('manager_id', $assignedSupervisors)
        ->select('manager_id as id', 'name', 'email')
        ->get();

    // ==================== SUPERVISOR LEAVE REQUESTS ====================
    $supervisorLeaveQuery = DB::table('supervisor_leaves as sl')
        ->whereIn('sl.supervisor_id', $assignedSupervisors)
        ->select('sl.leave_id', 'sl.supervisor_id', 'sl.name', 'sl.email', 'sl.from_date', 'sl.to_date', 'sl.reason', 'sl.leave_status', 'sl.days');

    if ($request->filled('leave_status')) {
        $supervisorLeaveQuery->where('sl.leave_status', $request->leave_status);
    }

    if ($request->filled('leave_supervisor_id')) {
        $supervisorLeaveQuery->where('sl.supervisor_id', $request->leave_supervisor_id);
    }

    $supervisorLeaves = $supervisorLeaveQuery->orderBy('sl.created_at', 'desc')
                                             ->paginate($perPage, ['*'], 'leave_page')
                                             ->withQueryString();

    return view('pages.manager.attendance.attendanceManagement', compact(
        'manager',
        'tab',
        'internAttendance',
        'internsList',
        'supervisorAttendance',
        'supervisorsList',
        'supervisorLeaves'
    ));
}
}
