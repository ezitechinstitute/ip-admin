<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternLeaveController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get leave requests for this intern
        $leaves = DB::table('intern_leaves')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.intern.leave.index', compact('leaves'));
    }
    
    public function requestLeave(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $validated = $request->validate([
            'leave_type' => 'required|string|max:50',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string|max:500',
        ]);
        
        // Calculate days
        $fromDate = Carbon::parse($validated['from_date']);
        $toDate = Carbon::parse($validated['to_date']);
        $days = $fromDate->diffInDays($toDate) + 1;
        
        // Check for existing pending leave
        $pendingLeave = DB::table('intern_leaves')
            ->where('eti_id', $intern->eti_id)
            ->where('leave_status', 0)
            ->first();
        
        if ($pendingLeave) {
            return redirect()->back()->with('error', 'You already have a pending leave request.');
        }
        
        // Create leave request - NO leave_type column
        DB::table('intern_leaves')->insert([
            'eti_id' => $intern->eti_id,
            'name' => $intern->name,
            'email' => $intern->email,
            // 'leave_type' => $validated['leave_type'], // ← REMOVE THIS - column doesn't exist
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'reason' => $validated['reason'],
            'technology' => $intern->int_technology,
            'intern_type' => $intern->internship_type ?? 'Remote',
            'days' => $days,
            'leave_status' => 0, // 0 = pending
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Create notification
        $this->createLeaveNotification($intern);
        
        return redirect()->route('intern.leave')
            ->with('success', 'Leave request submitted successfully. Waiting for approval.');
    }
    
    private function createLeaveNotification($intern)
    {
        if (!Schema::hasTable('intern_notifications')) {
            return;
        }
        
        DB::table('intern_notifications')->insert([
            'intern_id' => $intern->int_id,
            'title' => 'Leave Request Submitted',
            'message' => 'Your leave request has been submitted and is pending approval.',
            'type' => 'leave',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}