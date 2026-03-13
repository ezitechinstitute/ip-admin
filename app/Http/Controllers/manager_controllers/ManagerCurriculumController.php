<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\ManagersAccount;
use App\Models\Technology;
use App\Models\TechnologyCurriculum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerCurriculumController extends Controller
{
    public function index()
    {
        $curriculums = TechnologyCurriculum::with('technology')
            ->orderBy('curriculum_id', 'desc')
            ->paginate(15);

        return view('pages.manager.curriculum.index', compact('curriculums'));
    }

    public function create()
    {
        $technologies = Technology::where('status', 1)->orderBy('technology')->get();
        return view('pages.manager.curriculum.create', compact('technologies'));
    }

    public function store(Request $request)
    {
        $validator = $request->validate([
            'tech_id' => 'required|integer|exists:technologies,tech_id',
            'curriculum_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_projects' => 'required|integer|min:0',
            'total_duration_weeks' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $validator['created_by'] = Auth::guard('manager')->check()
            ? Auth::guard('manager')->user()->manager_id
            : null;

        TechnologyCurriculum::create($validator);

        return redirect()->route('manager.curriculum.index')
            ->with('success', 'Curriculum created successfully.');
    }

    public function show($id)
    {
        $curriculum = TechnologyCurriculum::with(['technology', 'projects.supervisor'])
            ->findOrFail($id);

        $supervisors = ManagersAccount::where('loginas', 'Supervisor')
            ->orderBy('name')
            ->get();

        return view('pages.manager.curriculum.show', compact('curriculum', 'supervisors'));
    }

    public function edit($id)
    {
        $curriculum = TechnologyCurriculum::findOrFail($id);
        $technologies = Technology::where('status', 1)->orderBy('technology')->get();
        return view('pages.manager.curriculum.edit', compact('curriculum', 'technologies'));
    }

    public function update(Request $request, $id)
    {
        $curriculum = TechnologyCurriculum::findOrFail($id);

        $data = $request->validate([
            'tech_id' => 'required|integer|exists:technologies,tech_id',
            'curriculum_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_projects' => 'required|integer|min:0',
            'total_duration_weeks' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $curriculum->update($data);

        return redirect()->route('manager.curriculum.index')
            ->with('success', 'Curriculum updated successfully.');
    }

    public function destroy($id)
    {
        $curriculum = TechnologyCurriculum::findOrFail($id);
        $curriculum->delete();

        return redirect()->route('manager.curriculum.index')
            ->with('success', 'Curriculum deleted successfully.');
    }
}
