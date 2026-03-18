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
            'id',
            'supervisor_id',
            'leave_type',
            'from_date',
            'to_date',
            'status'
        )
        ->where('supervisor_id', $supervisorId)
        ->orderByDesc('id')
        ->limit(20)
        ->get();

    return view('content.supervisor.leaves', compact('leaves'));
}
}