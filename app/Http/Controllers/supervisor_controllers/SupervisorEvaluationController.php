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
        $evaluations = DB::table('intern_evaluations')
            ->join('intern_accounts', function($join) {
                $join->on(DB::raw('intern_evaluations.eti_id COLLATE utf8mb4_unicode_ci'), '=', DB::raw('intern_accounts.eti_id COLLATE utf8mb4_unicode_ci'));
            })
            ->select('intern_evaluations.*', 'intern_accounts.name as intern_name')
            ->where('supervisor_id', $supervisorId)
            ->orderByDesc('id')
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
            'professionalism' => 'required|integer|min:0|max:10',
            'overall_score' => 'required|integer|min:0|max:10',
            'remarks' => 'nullable|string',
        ]);

        $supervisorId = session('manager_id');

        DB::table('intern_evaluations')->insert([
            'eti_id' => $request->eti_id,
            'supervisor_id' => $supervisorId,
            'month' => $request->month,
            'technical_skills' => $request->technical_skills,
            'problem_solving' => $request->problem_solving,
            'communication' => $request->communication,
            'professionalism' => $request->professionalism,
            'overall_score' => $request->overall_score,
            'remarks' => $request->remarks,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('supervisor.viewIntern', $request->eti_id)->with('success', 'Evaluation submitted successfully.');
    }
    public function edit($id)
    {
        $supervisorId = session('manager_id');
        $evaluation = DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->first();

        if (!$evaluation) {
            return redirect()->route('supervisor.evaluations.index')->with('error', 'Evaluation not found.');
        }

        $intern = DB::table('intern_accounts')->where('eti_id', $evaluation->eti_id)->first();

        return view('content.supervisor.evaluations.edit', compact('evaluation', 'intern'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'month' => 'required|string',
            'technical_skills' => 'required|integer|min:0|max:10',
            'problem_solving' => 'required|integer|min:0|max:10',
            'communication' => 'required|integer|min:0|max:10',
            'professionalism' => 'required|integer|min:0|max:10',
            'overall_score' => 'required|integer|min:0|max:10',
            'remarks' => 'nullable|string',
        ]);

        $supervisorId = session('manager_id');

        DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->update([
                'month' => $request->month,
                'technical_skills' => $request->technical_skills,
                'problem_solving' => $request->problem_solving,
                'communication' => $request->communication,
                'professionalism' => $request->professionalism,
                'overall_score' => $request->overall_score,
                'remarks' => $request->remarks,
                'updated_at' => now(),
            ]);

        $this->logActivity('Updated Evaluation', "Modified Evaluation ID: {$id}");

        return redirect()->route('supervisor.evaluations.index')->with('success', 'Evaluation updated successfully.');
    }

    public function destroy($id)
    {
        $supervisorId = session('manager_id');
        $evaluation = DB::table('intern_evaluations')
            ->where('id', $id)
            ->where('supervisor_id', $supervisorId)
            ->first();

        if ($evaluation) {
            DB::table('intern_evaluations')->where('id', $id)->delete();
            $this->logActivity('Deleted Evaluation', "Removed Evaluation for Intern: {$evaluation->eti_id}");
            return redirect()->route('supervisor.evaluations.index')->with('success', 'Evaluation deleted successfully.');
        }

        return redirect()->route('supervisor.evaluations.index')->with('error', 'Evaluation not found.');
    }
}

