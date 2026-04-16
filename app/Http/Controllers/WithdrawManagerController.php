<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        DB::table('withdraw_requests')->insert([
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

        return back()->with('success', 'Request Submitted!');
    }
}
}