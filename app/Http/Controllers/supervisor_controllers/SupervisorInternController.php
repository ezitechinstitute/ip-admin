<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorInternController extends Controller
{
    /**
     * Get supervisor technology safely
     */
    private function getSupervisorTechnology()
    {
        return strtolower(trim(session('manager_department')));
    }

    /**
     * Apply common filters
     */
    private function applyTechnologyFilter($query, $technology)
    {
         if ($technology === 'web development') {
            $query->whereIn('int_technology', ['Laravel', 'ReactJS', 'Flutter']);
        } else {
            $query->where('int_technology', 'LIKE', '%' . $technology . '%');
        }

        return $query;
        // if (!empty($technology)) {
        //     $query->where('int_technology', 'LIKE', '%' . $technology . '%');
        // }
        // return $query;
    }

    /**
     * My Interns (ALL)
     */
    public function myInterns(Request $request)
{
    $technology = $this->getSupervisorTechnology();

    // Start with your original select
    $query = DB::table('intern_accounts')
        ->select(
            'int_id',
            'eti_id',
            'name',
            'email',
            'phone',
            'int_technology',
            'internship_type',
            'start_date',
            'end_date', 
            'image',   
            'int_status'
        );

    // 1. Apply your base filter
    $query = $this->applyTechnologyFilter($query, $technology);

    // 2. Apply your Dynamic Filters (Keeping your original logic)
    if ($request->filled('tech')) {
        $query->where('int_technology', 'LIKE', '%' . $request->tech . '%');
    }

    if ($request->filled('type')) {
        $query->where('internship_type', $request->type);
    }

    if ($request->filled('status')) {
        $query->where('int_status', $request->status);
    }

    if ($request->filled('join_date')) {
        $query->where('start_date', 'LIKE', '%' . $request->join_date . '%');
    }

    // 🔥 CHANGE 1: Use paginate() instead of get() for 1000+ records
    $interns = $query->paginate(15)->withQueryString();

    // 🔥 CHANGE 2: Efficient Progress Calculation (N+1 Fix)
    // We only get task stats for the 15 interns on THIS page.
    $etiIds = $interns->pluck('eti_id')->toArray();

    $taskStats = DB::table('intern_tasks')
        ->whereIn('eti_id', $etiIds)
        ->select('eti_id', 
            DB::raw('count(*) as total'), 
            DB::raw('count(CASE WHEN LOWER(task_status) = "completed" THEN 1 END) as completed'))
        ->groupBy('eti_id')
        ->get()
        ->keyBy('eti_id');

    // Apply the stats to the interns collection
    foreach ($interns as $intern) {
        $stats = $taskStats->get($intern->eti_id);
        
        $total = $stats ? $stats->total : 0;
        $completed = $stats ? $stats->completed : 0;

        $intern->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    return view('content.supervisor.my-interns', compact('interns', 'technology'));
}

    /**
     * Active Interns
     */
    public function active()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'Active');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->get();

        foreach ($interns as $intern) {
            $totalTasks = DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->count();

            $completedTasks = DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->where('task_status', 'completed')
                ->count();

            $intern->progress = $totalTasks > 0
                ? round(($completedTasks / $totalTasks) * 100)
                : 0;

            $intern->total_tasks = $totalTasks;
            $intern->completed_tasks = $completedTasks;
        }

        return view('content.supervisor.active-interns', compact('interns', 'technology'));
    }

    /**
     * Contact Phase Interns
     */
    public function contactWith()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'contact');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->limit(20)->get();

        return view('content.supervisor.contact-interns', compact('interns', 'technology'));
    }

    /**
     * Test Phase Interns
     */
    public function test()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'test');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->limit(20)->get();

        return view('content.supervisor.test-interns', compact('interns', 'technology'));
    }

    /**
     * Completed Interns
     */
    public function completed()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'completed');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->limit(20)->get();

        return view('content.supervisor.completed-interns', compact('interns', 'technology'));
    }

    /**
     * New Interns
     */
    public function newInterns()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'new');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->limit(20)->get();

        return view('content.supervisor.new-interns', compact('interns', 'technology'));
    }

    /**
     * View Single Intern
     */
    public function show($id)
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_id', $id);

        $query = $this->applyTechnologyFilter($query, $technology);

        $intern = $query->first();

        if (!$intern) {
            return redirect()
                ->route('supervisor.myInterns')
                ->with('error', 'Intern not found or not assigned to your technology.');
        }

        $tasks = DB::table('intern_tasks')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('updated_at')
            ->get();

        $projects = DB::table('intern_projects')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('updatedat')
            ->get();

        $evaluations = DB::table('intern_evaluations')
            ->where('eti_id', $intern->eti_id)
            ->orderByDesc('month')
            ->get();

        return view('content.supervisor.view-intern', compact('intern', 'tasks', 'projects', 'evaluations'));
    }

    /**
     * Progress Monitoring
     */
    /**
     * Progress Monitoring (Optimized for Speed - N+1 Fixed)
     */
    public function progressMonitoring()
{
    $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');
    $technology = $this->getSupervisorTechnology();

    // 1. Fetch the Interns assigned to this supervisor's technology
    $query = DB::table('intern_accounts')
        ->where('int_status', 'active');
    $query = $this->applyTechnologyFilter($query, $technology);
    $interns = $query->get();

    if ($interns->isEmpty()) {
        return view('content.supervisor.progress-monitoring', compact('interns', 'technology'));
    }

    $etiIds = $interns->pluck('eti_id')->toArray();

    // Pre-load all data to prevent N+1
    $allTasks = DB::table('intern_tasks')->whereIn('eti_id', $etiIds)->get()->groupBy('eti_id');
    $allProjects = DB::table('intern_projects')->whereIn('eti_id', $etiIds)->get()->groupBy('eti_id');
    $allEvaluations = DB::table('intern_evaluations')->whereIn('eti_id', $etiIds)->get()->groupBy('eti_id');

    foreach ($interns as $intern) {
        $tasks = $allTasks->get($intern->eti_id, collect());
        $projects = $allProjects->get($intern->eti_id, collect());
        $evaluations = $allEvaluations->get($intern->eti_id, collect());

        // Task Math
        $total = $tasks->count();
        $completed = $tasks->where('task_status', 'Completed')->count();
        $overdue = $tasks->whereIn('task_status', ['Ongoing', 'In Progress', 'Pending'])
            ->where('task_end', '<', now()->toDateString())
            ->count();

        $intern->total_tasks = $total;
        $intern->completed_tasks = $completed;
        $intern->overdue_tasks = $overdue;
        $intern->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        // Compliance Math
        $intern->compliance = $total > 0 ? round((($total - $overdue) / $total) * 100) : 100;

        // Project Math
        $totalP = $projects->count();
        $completedP = $projects->whereIn('pstatus', ['Completed', 'Approved'])->count();
        $intern->project_completion = $totalP > 0 ? round(($completedP / $totalP) * 100) : 0;
        $intern->total_projects = $totalP;

        // Code Quality (Scale of 1-100 for the Chart)
        // If your evaluations are out of 10, multiply by 10
        $avgEval = $evaluations->avg('overall_score');
        $intern->code_quality = $avgEval ? round($avgEval * 10) : 0; 
    }

    return view('content.supervisor.progress-monitoring', compact('interns', 'technology'));
}
}