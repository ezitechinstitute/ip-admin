<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Leave;
use Illuminate\Routing\Controller;

class LeaveController extends Controller
{
    public function index(Request $request)
{
    $perPage = (int) $request->get('per_page', 15);
    $search = $request->get('search');
    $leaveType = $request->get('leave_type');
    $page = $request->get('page', 1);

    // 1. Define sub-queries
    $employee = DB::table('employee_leaves')
        ->select('leave_id', 'name', 'email', 'from_date', 'to_date', 'reason', 'leave_status', 'created_at', DB::raw("'employee' as source"));

    $supervisor = DB::table('supervisor_leaves')
        ->select('leave_id', 'name', 'email', 'from_date', 'to_date', 'reason', 'leave_status', 'created_at', DB::raw("'supervisor' as source"));

    $intern = DB::table('intern_leaves')
        ->select('leave_id', 'name', 'email', 'from_date', 'to_date', 'reason', 'leave_status', 'created_at', DB::raw("'intern' as source"));

    // 2. Combine queries
    if ($leaveType == 'employee') {
        $combinedQuery = $employee;
    } elseif ($leaveType == 'supervisor') {
        $combinedQuery = $supervisor;
    } elseif ($leaveType == 'intern') {
        $combinedQuery = $intern;
    } else {
        $combinedQuery = $employee->unionAll($supervisor)->unionAll($intern);
    }

    // 3. Execution with Search
    // English comments: Wrap the union in a subquery to apply filters globally
    $query = DB::table(DB::raw("({$combinedQuery->toSql()}) as combined_leaves"))
        ->mergeBindings($combinedQuery)
        ->when($search, function($q) use ($search) {
            $q->where(function($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('leave_id', 'like', "%{$search}%");
            });
        })
        ->orderBy('created_at', 'desc');

    // 4. Manual Pagination to ensure firstItem() exists
    $total = $query->count();
    $results = $query->forPage($page, $perPage)->get();

    $leave = new LengthAwarePaginator(
        $results,
        $total,
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('pages.admin.leave.leave', compact('leave', 'perPage'));
}

    public function approve($id)
    {
        Leave::where('leave_id', $id)->update([
            'leave_status' => 1
        ]);

        return back();
    }

    public function reject($id)
    {
        Leave::where('leave_id', $id)->update([
            'leave_status' => 0
        ]);

        return back();
    }
}
