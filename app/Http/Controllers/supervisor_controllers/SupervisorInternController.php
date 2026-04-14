<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // 1. Always apply the Supervisor's base technology filter first
        $query = $this->applyTechnologyFilter($query, $technology);

        // ==========================================
        // 🔥 NEW: DYNAMIC FORM FILTERS
        // ==========================================
        
        // Filter by typed Technology
        if ($request->filled('tech')) {
            $query->where('int_technology', 'LIKE', '%' . $request->tech . '%');
        }

        // Filter by Internship Type (Remote/Onsite/Hybrid)
        if ($request->filled('type')) {
            $query->where('internship_type', $request->type);
        }

        // Filter by Status (Active/Pending)
        if ($request->filled('status')) {
            $query->where('int_status', $request->status);
        }

        // 🔥 ADDED: Filter by Join Date (Maps to start_date in DB)
        if ($request->filled('join_date')) {
            // Using LIKE in case your start_date contains times as well
            $query->where('start_date', 'LIKE', '%' . $request->join_date . '%');
        }

        // Execute the query
        $interns = $query->limit(20)->get();

        // Calculate Project Progress
        foreach ($interns as $intern) {
            $totalTasks = DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->count();

            $completedTasks = DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->where('task_status', 'completed')
                ->count();

            $intern->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
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
        $technology = $this->getSupervisorTechnology();

        // 1. Fetch the Interns
        $query = DB::table('intern_accounts')
            ->where('int_status', 'active');
        $query = $this->applyTechnologyFilter($query, $technology);
        $interns = $query->get();

        // If there are no interns, just return the empty view
        if ($interns->isEmpty()) {
            return view('content.supervisor.progress-monitoring', compact('interns', 'technology'));
        }

        // ==========================================
        // 🔥 THE FIX: Get ALL IDs and fetch data in just 3 queries total
        // ==========================================
        $etiIds = $interns->pluck('eti_id')->toArray();

        // Query 1: Get ALL tasks for these specific interns, grouped by eti_id
        $allTasks = DB::table('intern_tasks')
            ->whereIn('eti_id', $etiIds)
            ->get()
            ->groupBy('eti_id');

        // Query 2: Get ALL projects for these specific interns, grouped by eti_id
        $allProjects = DB::table('intern_projects')
            ->whereIn('eti_id', $etiIds)
            ->get()
            ->groupBy('eti_id');

        // Query 3: Get ALL evaluations for these specific interns, grouped by eti_id
        $allEvaluations = DB::table('intern_evaluations')
            ->whereIn('eti_id', $etiIds)
            ->get()
            ->groupBy('eti_id');

        // ==========================================
        // Now, we loop through interns and do the math purely in PHP memory!
        // ==========================================
        foreach ($interns as $intern) {
            
            // Grab the specific data for this intern from our pre-loaded Collections
            // If they have no tasks/projects, it defaults to an empty collection (collect())
            $tasks = $allTasks->get($intern->eti_id, collect());
            $projects = $allProjects->get($intern->eti_id, collect());
            $evaluations = $allEvaluations->get($intern->eti_id, collect());

            // 1. TASKS LOGIC (Completion & Compliance)
            $total = $tasks->count();
            $completed = $tasks->where('task_status', 'Completed')->count();
            $expired = $tasks->where('task_status', 'Expired')->count();

            $overdue = $tasks->whereIn('task_status', ['Assigned', 'Ongoing', 'In Progress', 'Pending'])
                ->where('task_end', '<', now()->toDateString())
                ->count();

            $intern->total_tasks = $total;
            $intern->completed_tasks = $completed;
            $intern->expired_tasks = $expired;
            $intern->overdue_tasks = $overdue;
            
            $intern->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
            
            $compliantTasks = $total - ($expired + $overdue);
            $intern->compliance = $total > 0 ? round(($compliantTasks / $total) * 100) : 100;

            // 2. PROJECT COMPLETION LOGIC
            $totalProjects = $projects->count();
            $completedProjects = $projects->where('pstatus', 'Completed')->count(); 
            
            $intern->project_completion = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;
            $intern->total_projects = $totalProjects;

            // 3. CODE QUALITY SCORE LOGIC
            $avgQualityScore = $evaluations->avg('overall_score'); 
            $intern->code_quality = $avgQualityScore ? round($avgQualityScore, 1) : 0;
        }

        return view('content.supervisor.progress-monitoring', compact('interns', 'technology'));
    }
}