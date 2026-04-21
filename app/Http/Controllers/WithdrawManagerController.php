<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\WithdrawSubmittedMail;
use Illuminate\Support\Facades\Mail;

class WithdrawManagerController extends Controller
{
    public function index()
    {
        $userId = Auth::id() ?? 1;

        // Get all requests for this manager
        $withdrawRequests = DB::table('withdraw_requests')
            ->where('req_by', $userId)
            ->orderByDesc('req_id')
            ->get();

        return view('pages.manager.withdraw.withdraw', compact('withdrawRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank' => 'required',
            'ac_no' => 'required',
            'ac_name' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        $withdraw = DB::table('withdraw_requests')->insertGetId([
            'eti_id' => 'ETI-' . time(),
            'req_by' => Auth::id() ?? 1,
            'bank' => $request->bank,
            'ac_no' => $request->ac_no,
            'ac_name' => $request->ac_name,
            'description' => $request->description,
            'date' => now(),
            'amount' => $request->amount,
            'req_status' => 0,
            'period' => $request->period,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send notification to admin
        try {
            $adminEmail = 'admin@ezitech.org'; // Configure this in env
            Mail::to($adminEmail)->send(new WithdrawSubmittedMail($request->all(), Auth::user()));
        } catch (\Exception $e) {
            // Log error but don't fail the submission
        }

        // Activity log (only if table exists)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
                DB::table('activity_logs')->insert([
                    'user_id' => Auth::id(),
                    'action' => 'Submitted withdrawal request',
                    'details' => "Amount: {$request->amount}, Bank: {$request->bank}",
                    'created_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            // Silently fail if activity logging has issues
        }

        return back()->with('success', 'Request Submitted!');
    }
}