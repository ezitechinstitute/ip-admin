<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManagersAccount;
use App\Models\InternCurriculumAssignment;
use App\Models\CurriculumProject;
use App\Models\CurriculumSupervisorMapping;
use App\Models\InternProjectProgress;
use Illuminate\Support\Facades\Auth;

class Supervisorcontroller extends Controller
{
    public function index()
    {
        // Get logged in manager
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect('/login');
        }

        $managerId = $manager->manager_id;

        // Only supervisors assigned to this manager
        $supervisors = ManagersAccount::where('loginas', 'Supervisor')
                        ->where('assigned_manager', $managerId)
                        ->latest('manager_id')
                        ->paginate();

        return view('pages.manager.supervisor.Supervisor', compact('supervisors'));
    }

    /**
     * Show supervisor metrics and action panel.
     */
    public function show($id)
    {
        $supervisor = ManagersAccount::where('loginas', 'Supervisor')
            ->findOrFail($id);

        // Curriculums covered by this supervisor via project mapping
        $cpIds = CurriculumSupervisorMapping::where('supervisor_id', $id)->pluck('cp_id');
        $curriculumIds = CurriculumProject::whereIn('cp_id', $cpIds)->pluck('curriculum_id')->unique();

        $totalInterns = InternCurriculumAssignment::whereIn('curriculum_id', $curriculumIds)->count();
        $completedInterns = InternCurriculumAssignment::whereIn('curriculum_id', $curriculumIds)
            ->where('status', 'completed')
            ->count();

        $completionRate = $totalInterns > 0 ? round(100 * $completedInterns / $totalInterns, 2) : 0;

        $progresses = InternProjectProgress::where('supervisor_id', $id);

        $averageReviewTimeSeconds = $progresses->whereNotNull('start_date')->whereNotNull('end_date')->whereColumn('end_date', '>', 'start_date')
            ->get()
            ->map(function ($progress) {
                return $progress->end_date->diffInSeconds($progress->start_date);
            })->avg() ?? 0;

        $averageReviewTimeHours = $averageReviewTimeSeconds ? round($averageReviewTimeSeconds / 3600, 2) : 0;

        $pendingReviews = InternProjectProgress::where('supervisor_id', $id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();

        $activityLog = CurriculumSupervisorMapping::with(['project', 'supervisor'])
            ->where('supervisor_id', $id)
            ->orderBy('assigned_date', 'desc')
            ->limit(50)
            ->get();

        return view('pages.manager.supervisor.show', compact(
            'supervisor',
            'totalInterns',
            'averageReviewTimeHours',
            'pendingReviews',
            'completionRate',
            'activityLog'
        ));
    }

    /**
     * Reassign intern to a different supervisor.
     */
    public function reassignIntern(Request $request, $supervisorId)
    {
        $request->validate([
            'assignment_id' => 'required|integer|exists:intern_curriculum_assignment,assignment_id',
            'new_supervisor_id' => 'required|integer|exists:manager_accounts,manager_id',
        ]);

        $assignment = InternCurriculumAssignment::findOrFail($request->assignment_id);
        $newSupervisor = ManagersAccount::where('loginas', 'Supervisor')->findOrFail($request->new_supervisor_id);

        // reassign related curriculum supervisor mappings to new supervisor for this curriculum
        $projectIds = CurriculumProject::where('curriculum_id', $assignment->curriculum_id)->pluck('cp_id');

        CurriculumSupervisorMapping::whereIn('cp_id', $projectIds)
            ->update(['supervisor_id' => $newSupervisor->manager_id]);

        // add a log entry for tracking (if using mapping table as activity log)
        foreach ($projectIds as $cpId) {
            CurriculumSupervisorMapping::create([
                'cp_id' => $cpId,
                'supervisor_id' => $newSupervisor->manager_id,
                'assigned_by' => Auth::guard('manager')->id(),
                'is_primary' => 1,
                'status' => 1,
            ]);
        }

        return redirect()->route('manager.supervisor.show', $supervisorId)
            ->with('success', 'Intern reassigned successfully.');
    }

    /**
     * Freeze or unfreeze supervisor account.
     */
    public function toggleFreeze($id)
    {
        $supervisor = ManagersAccount::where('loginas', 'Supervisor')->findOrFail($id);
        $supervisor->status = !$supervisor->status;
        $supervisor->save();

        return redirect()->route('manager.supervisor.show', $id)
            ->with('success', $supervisor->status ? 'Supervisor unfrozen.' : 'Supervisor frozen.');
    }

    /**
     * Supervisor activity log page.
     */
    public function activityLog($id)
    {
        $supervisor = ManagersAccount::where('loginas', 'Supervisor')->findOrFail($id);
        $activityLog = CurriculumSupervisorMapping::with(['project'])
            ->where('supervisor_id', $id)
            ->orderBy('assigned_date', 'desc')
            ->get();

        return view('pages.manager.supervisor.activity_log', compact('supervisor', 'activityLog'));
    }
}
