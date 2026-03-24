<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\CurriculumProject;
use App\Models\TechnologyCurriculum;
use Illuminate\Http\Request;
use App\Models\CurriculumSupervisorMapping;


class ManagerCurriculumProjectController extends Controller
{
    public function store(Request $request)
    {
    $manager = auth('manager')->user();
    if (!$manager) return redirect()->route('login');
    $managerId = $manager->manager_id;

        $data = $request->validate([
            'curriculum_id' => 'required|integer|exists:technology_curriculum,curriculum_id',
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'sequence_order' => 'required|integer|min:1',
            'duration_weeks' => 'required|integer|min:0',
            'assigned_supervisor' => 'nullable|integer|exists:manager_accounts,manager_id',
            'learning_objectives' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $project = CurriculumProject::create($data);

        // Update curriculum total projects
        $curriculum = TechnologyCurriculum::find($data['curriculum_id']);
        if ($curriculum) {
            $curriculum->total_projects = $curriculum->projects()->count();
            $curriculum->save();
        }

        // Add supervisor mapping if supervisor assigned
        if (!empty($data['assigned_supervisor'])) {
            CurriculumSupervisorMapping::create([
                'cp_id' => $project->cp_id,
                'supervisor_id' => $data['assigned_supervisor'],
                'assigned_by' => $managerId, // manager who assigned
                'assigned_date' => now(),
                'is_primary' => 1,
                'status' => 1,
            ]);
        }

        return redirect()->route('manager.curriculum.show', $data['curriculum_id'])
            ->with('success', 'Project added successfully.');
    }

    public function update(Request $request, $id)
    {
        $project = CurriculumProject::findOrFail($id);

        $data = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'sequence_order' => 'required|integer|min:1',
            'duration_weeks' => 'required|integer|min:0',
            'assigned_supervisor' => 'nullable|integer|exists:manager_accounts,manager_id',
            'learning_objectives' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $project->update($data);

        return redirect()->route('manager.curriculum.show', $project->curriculum_id)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = CurriculumProject::findOrFail($id);
        $curriculumId = $project->curriculum_id;
        $project->delete();

        $curriculum = TechnologyCurriculum::find($curriculumId);
        if ($curriculum) {
            $curriculum->total_projects = $curriculum->projects()->count();
            $curriculum->save();
        }

        return redirect()->route('manager.curriculum.show', $curriculumId)
            ->with('success', 'Project deleted successfully.');
    }
}
