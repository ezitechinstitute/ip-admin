<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorAttendanceController extends Controller
{
    public function index()
{
    $supervisorTechnology = session('manager_department');

    $attendance = \Illuminate\Support\Facades\DB::table('intern_attendance')
        ->join('intern_accounts', 'intern_attendance.eti_id', '=', 'intern_accounts.eti_id')
        ->select(
            'intern_attendance.id',
            'intern_attendance.eti_id',
            'intern_attendance.start_shift',
            'intern_attendance.end_shift',
            'intern_attendance.duration',
            'intern_attendance.status'
        )
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->where('intern_accounts.int_technology', $supervisorTechnology);
        })
        ->orderByDesc('intern_attendance.id')
        ->limit(20)
        ->get();

    return view('content.supervisor.attendance', compact('attendance'));
}
}