<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternAttendanceController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $attendance = DB::table('intern_attendance')
            ->where('intern_id', $intern->int_id)
            ->orderBy('date', 'desc')
            ->paginate(30);
        
        return view('pages.intern.attendance.index', compact('attendance'));
    }
}