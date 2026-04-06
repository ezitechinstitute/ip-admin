<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorLeaveController extends Controller
{
    public function index()
{
    $supervisorId = session('manager_id');

    $leaves = \Illuminate\Support\Facades\DB::table('supervisor_leaves')
        ->select(
            'leave_id',
            'supervisor_id',
            'reason',
            'from_date',
            'to_date',
            'leave_status'
        )
        ->where('supervisor_id', $supervisorId)
        ->orderByDesc('leave_id')
        ->limit(20)
        ->get();

    return view('content.supervisor.leaves', compact('leaves'));
}
}