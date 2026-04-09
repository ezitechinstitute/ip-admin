<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorAttendanceController extends Controller
{
    

public function index()
{
    $attendance = DB::table('intern_attendance as ia')
        ->leftJoin('intern_accounts as acc', function ($join) {
            $join->on(
                DB::raw('TRIM(LOWER(ia.eti_id))'),
                '=',
                DB::raw('TRIM(LOWER(acc.eti_id))')
            );
        })
        ->select(
            'ia.id',
            'ia.eti_id',
            'acc.name as intern_name',
            'ia.start_shift',
            'ia.end_shift',
            'ia.duration',
            'ia.status'
        )
        ->orderByDesc('ia.id')
        ->get();

    return view('content.supervisor.attendance', compact('attendance'));
}
}