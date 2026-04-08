<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupervisorEvaluationController extends Controller
{
    public function index()
    {
        $supervisorId = session('manager_id');

        $evaluations = DB::table('intern_evaluations as ie')
            ->leftJoin('intern_accounts as acc', function ($join) {
                $join->on(
                    DB::raw('LOWER(TRIM(ie.eti_id)) COLLATE utf8mb4_unicode_ci'),
                    '=',
                    DB::raw('LOWER(TRIM(acc.eti_id)) COLLATE utf8mb4_unicode_ci')
                );
            })
            ->select('ie.*', 'acc.name as intern_name')
            ->where('ie.supervisor_id', $supervisorId)
            ->orderByDesc('ie.id')
            ->paginate(15);

        return view('content.supervisor.evaluations.index', compact('evaluations'));
    }

    public function create($eti_id)
    {
        $intern = DB::table('intern_accounts')->where('eti_id', $eti_id)->first();

        if (!$intern) {
            return back()->with('error', 'Intern not found.');
        }

        return view('content.supervisor.evaluations.create', compact('intern'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'eti_id' => 'required|string',
            'month' => 'required|string',
            'technical_skills' => 'required|integer|min:0|max:10',
            'problem_solving' => 'required|integer|min:0|max:10',
            'communication' => 'required|integer|min:0|max:10',
            'remarks' => 'nullable|string',
        ]);

        $supervisorId = session('manager_id');

        // ✅ Prevent duplicate
        $exists = DB::table('intern_evaluations')
            ->where('eti_id', $request->eti_id)
            ->where('month', $request->month)
            ->where('supervisor_id', $supervisorId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Evaluation for this month already exists.');
        }

        // ✅ AUTO TASK COMPLETION
        $totalTasks = DB::table('intern_tasks')
            ->where('eti_id', $request->eti_id)
            ->count();

        $completedTasks = DB::table('intern_tasks')
            ->where('eti_id', $request->eti_id)
            // ->where('status', 'approved') // adjust if needed
            ->where('task_status', 'approved')
            ->count();

        $taskCompletion = $totalTasks > 0 
            ? ($completedTasks / $totalTasks) * 10 
            : 0;

        // ✅ OVERALL
        $overall = (
            $request->technical_skills +
            $request->problem_solving +
            $request->communication +
            $taskCompletion
        ) / 4;

        DB::table('intern_evaluations')->insert([
            'eti_id' => $request->eti_id,
            'supervisor_id' => $supervisorId,
            'month' => $request->month,
            'technical_skills' => $request->technical_skills,
            'problem_solving' => $request->problem_solving,
            'communication' => $request->communication,
            'task_completion' => round($taskCompletion, 1),
            'overall_score' => round($overall, 1),
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('supervisor.evaluations.index')
            ->with('success', 'Evaluation submitted successfully.');
    }

    public function edit($id)
    {
        $supervisorId = session('manager_id');

        $evaluation = DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->first();

        if (!$evaluation) {
            return redirect()->route('supervisor.evaluations.index')
                ->with('error', 'Evaluation not found.');
        }

        $intern = DB::table('intern_accounts')
            ->where('eti_id', $evaluation->eti_id)
            ->first();

        return view('content.supervisor.evaluations.edit', compact('evaluation', 'intern'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'month' => 'required|string',
            'technical_skills' => 'required|integer|min:0|max:10',
            'problem_solving' => 'required|integer|min:0|max:10',
            'communication' => 'required|integer|min:0|max:10',
            'remarks' => 'nullable|string',
        ]);

        $supervisorId = session('manager_id');

        // ✅ RE-CALCULATE TASK COMPLETION
        $totalTasks = DB::table('intern_tasks')
            ->where('eti_id', DB::table('intern_evaluations')->where('id', $id)->value('eti_id'))
            ->count();

        $completedTasks = DB::table('intern_tasks')
            ->where('eti_id', DB::table('intern_evaluations')->where('id', $id)->value('eti_id'))
            // ->where('status', 'approved')
            ->where('task_status', 'approved')
            ->count();

        $taskCompletion = $totalTasks > 0 
            ? ($completedTasks / $totalTasks) * 10 
            : 0;

        $overall = (
            $request->technical_skills +
            $request->problem_solving +
            $request->communication +
            $taskCompletion
        ) / 4;

        DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->update([
                'month' => $request->month,
                'technical_skills' => $request->technical_skills,
                'problem_solving' => $request->problem_solving,
                'communication' => $request->communication,
                'task_completion' => round($taskCompletion, 1),
                'overall_score' => round($overall, 1),
                'remarks' => $request->remarks,
                'updated_at' => now(),
            ]);

        return redirect()->route('supervisor.evaluations.index')
            ->with('success', 'Evaluation updated successfully.');
    }

    public function destroy($id)
    {
        $supervisorId = session('manager_id');

        DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->delete();

        return redirect()->route('supervisor.evaluations.index')
            ->with('success', 'Evaluation deleted successfully.');
    }
}