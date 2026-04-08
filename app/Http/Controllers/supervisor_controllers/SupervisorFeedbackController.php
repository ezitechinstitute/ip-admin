<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternFeedback;
use Illuminate\Support\Facades\DB;
use App\Models\SupervisorFeedback;

class SupervisorFeedbackController extends Controller
{
   public function index()
{
    $supervisorId = session('manager_id');

    // ✅ Dropdown data
    $interns = DB::table('intern_accounts')
        ->select('eti_id', 'name')
        ->orderBy('name')
        ->get();

    // ✅ ONLY this supervisor's feedback
    $feedbacks = SupervisorFeedback::where('supervisor_feedback.supervisor_id', $supervisorId)
        ->leftJoin('intern_accounts', function ($join) {
            $join->on(
                DB::raw('CONVERT(supervisor_feedback.eti_id USING utf8mb4)'),
                '=',
                DB::raw('CONVERT(intern_accounts.eti_id USING utf8mb4)')
            );
        })
        ->select(
            'supervisor_feedback.*',
            'intern_accounts.name as intern_name'
        )
        ->orderByDesc('supervisor_feedback.created_at')
        ->get();

    return view('content.supervisor.feedback', compact('interns', 'feedbacks'));
}

public function store(Request $request)
{
    $request->validate([
        'eti_id' => 'required',
        'score' => 'required|integer|min:1|max:100',
        'remarks' => 'required',
        'improvement_suggestions' => 'nullable'
    ]);

    SupervisorFeedback::create([
        'eti_id' => $request->eti_id,
        'supervisor_id' => session('manager_id'),
        'score' => $request->score,
        'remarks' => $request->remarks,
        'improvement_suggestions' => $request->improvement_suggestions
    ]);

    return back()->with('success', 'Feedback added successfully');
}
}