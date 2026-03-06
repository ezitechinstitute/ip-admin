<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\EmployeeLeave;
use App\Models\Leave;
use App\Models\SupervisorLeave;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
         $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

        $leaveType = $request->leave_type;
        $status = $request->status; // approved / rejected
        $filterDate = $request->filter_date;

        // ---------- SINGLE TABLE CASES ----------
        if ($leaveType === 'employee') {

            $leaves = EmployeeLeave::latest()->get()->map(function ($l) {
                $l->source = 'employee';
                return $l;
            });

        } elseif ($leaveType === 'supervisor') {

            $leaves = SupervisorLeave::latest()->get()->map(function ($l) {
                $l->source = 'supervisor';
                return $l;
            });

        } elseif ($leaveType === 'intern') {

            $leaves = Leave::latest()->get()->map(function ($l) {
                $l->source = 'intern';
                return $l;
            });

        } else {

            // ---------- MERGE ALL THREE TABLES ----------
            $leaves = collect()
                ->merge(
                    EmployeeLeave::all()->map(function ($l) {
                        $l->source = 'employee';
                        return $l;
                    })
                )
                ->merge(
                    SupervisorLeave::all()->map(function ($l) {
                        $l->source = 'supervisor';
                        return $l;
                    })
                )
                ->merge(
                    Leave::all()->map(function ($l) {
                        $l->source = 'intern';
                        return $l;
                    })
                );
        }

        // ---------- STATUS FILTER (works for all cases) ----------
        if ($status) {
            $value = $status === 'approved' ? 1 : 0;
            $leaves = $leaves->where('leave_status', $value);
        }

        // ---------- DATE FILTER (YOUR CALENDAR FILTER) ----------
        if ($filterDate) {
            $leaves = $leaves->filter(function ($leave) use ($filterDate) {
                return
                    $leave->from_date <= $filterDate &&
                    $leave->to_date >= $filterDate;
            });
        }
        $search = $request->search;

if ($search) {
    $leaves = $leaves->filter(function ($leave) use ($search) {

        $s = strtolower($search);

        return
            str_contains(strtolower($leave->name ?? ''), $s) ||
            str_contains(strtolower($leave->email ?? ''), $s) ||
            str_contains((string)($leave->leave_id ?? ''), $s);
    });
}
$page = request()->get('page', 1);
$perPage = $perPage ?? 15;

// collection ko array slice karo
$items = $leaves->values();
$currentPageItems = $items->slice(($page - 1) * $perPage, $perPage)->values();

$leave = new LengthAwarePaginator(
    $currentPageItems,
    $items->count(),
    $perPage,
    $page,
    ['path' => request()->url(), 'query' => request()->query()]
);

        return view('pages.admin.leave.leave', compact('leave','perPage'));

    }

    // -------- ACTION METHODS --------

    public function approveEmployee($id)
    {
        EmployeeLeave::where('leave_id', $id)->update(['leave_status' => 1]);
        return back()->with('success', 'Employee leave approved');
    }

    public function rejectEmployee($id)
    {
        EmployeeLeave::where('leave_id', $id)->update(['leave_status' => 0]);
        return back()->with('error', 'Employee leave rejected');
    }

    public function approveSupervisor($id)
    {
        SupervisorLeave::where('leave_id', $id)->update(['leave_status' => 1]);
        return back()->with('success', 'Supervisor leave approved');
    }

    public function rejectSupervisor($id)
    {
        SupervisorLeave::where('leave_id', $id)->update(['leave_status' => 0]);
        return back()->with('error', 'Supervisor leave rejected');
    }

    public function approveIntern($id)
    {
        Leave::where('leave_id', $id)->update(['leave_status' => 1]);
        return back()->with('success', 'Intern leave approved');
    }

    public function rejectIntern($id)
    {
        Leave::where('leave_id', $id)->update(['leave_status' => 0]);
        return back()->with('error', 'Intern leave rejected');
    }





    public function exportLeavesCSV(Request $request)
{
    // English: Setting time limit to 0 for large datasets (1 Lakh+)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $fileName = 'leaves_report_' . date('Y-m-d') . '.csv';

    // 1. Base Queries
    $internQuery = DB::table('intern_leaves')
        ->select('leave_id', 'name', 'email', 'reason', 'from_date', 'to_date', 'leave_status', DB::raw("'intern' as source"));

    $employeeQuery = DB::table('employee_leaves')
        ->select('leave_id', 'name', 'email', 'reason', 'from_date', 'to_date', 'leave_status', DB::raw("'employee' as source"));

    $supervisorQuery = DB::table('supervisor_leaves')
        ->select('leave_id', 'name', 'email', 'reason', 'from_date', 'to_date', 'leave_status', DB::raw("'supervisor' as source"));

    // 2. Filter Logic
    if ($request->leave_type === 'employee') { $query = $employeeQuery; }
    elseif ($request->leave_type === 'supervisor') { $query = $supervisorQuery; }
    elseif ($request->leave_type === 'intern') { $query = $internQuery; }
    else { $query = $internQuery->unionAll($employeeQuery)->unionAll($supervisorQuery); }

    $finalQuery = DB::table(DB::raw("({$query->toSql()}) as combined_leaves"))->mergeBindings($query);

    // Filters
    if ($request->status) {
        $finalQuery->where('leave_status', ($request->status === 'approved' ? 1 : 0));
    }
    if ($request->filter_date) {
        $finalQuery->where('from_date', '<=', $request->filter_date)->where('to_date', '>=', $request->filter_date);
    }
    if ($request->search) {
        $search = $request->search;
        $finalQuery->where('name', 'like', "{$search}%");
    }

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    return response()->stream(function() use ($finalQuery) {
        // English: Critical fix - Clear output buffer to avoid ERR_INVALID_RESPONSE
        if (ob_get_level() > 0) {
            ob_end_clean();
        }

        $file = fopen('php://output', 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
        
        fputcsv($file, ['ID', 'Type', 'Name', 'Email', 'From', 'To', 'Reason', 'Status']);

        // English: Using cursor to iterate over 100,000+ records without memory crash
        $finalQuery->orderBy('from_date', 'desc')->cursor()->each(function ($row) use ($file) {
            fputcsv($file, [
                $row->leave_id, 
                ucfirst($row->source), 
                $row->name, 
                $row->email, 
                $row->from_date, 
                $row->to_date, 
                $row->reason, 
                ($row->leave_status == 1) ? 'Approved' : 'Pending'
            ]);
        });

        fclose($file);
    }, 200, $headers);
}





}
