<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\EmployeeLeave;
use App\Models\Leave;
use App\Models\SupervisorLeave;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

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
            str_contains(strtolower($leave->reason ?? ''), $s) ||
            str_contains((string)($leave->leave_id ?? ''), $s);
    });
}


        return view('pages.admin.leave.leave', ['leave' => $leaves],compact('perPage'));
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
}
