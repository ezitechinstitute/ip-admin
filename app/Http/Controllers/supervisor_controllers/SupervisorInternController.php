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
    public function progressMonitoring()
    {
        $technology = $this->getSupervisorTechnology();

        $query = DB::table('intern_accounts')
            ->where('int_status', 'active');

        $query = $this->applyTechnologyFilter($query, $technology);

        $interns = $query->get();

        foreach ($interns as $intern) {
            // ==========================================
            // 1. TASKS LOGIC (Task Completion & Compliance)
            // ==========================================
            $tasks = DB::table('intern_tasks')
                ->where('eti_id', $intern->eti_id)
                ->get();

            $total = $tasks->count();
            $completed = $tasks->where('task_status', 'completed')->count();
            $expired = $tasks->where('task_status', 'expired')->count();

            $overdue = $tasks->where('task_status', 'Assigned')
                ->where('task_end', '<', now()->toDateString())
                ->count();

            $intern->total_tasks = $total;
            $intern->completed_tasks = $completed;
            $intern->expired_tasks = $expired;
            $intern->overdue_tasks = $overdue;
            $intern->progress = $total > 0 ? round(($completed / $total) * 100) : 0;
            $intern->compliance = $total > 0 ? round(($completed / $total) * 100) : 100;

            // ==========================================
            // 🔥 2. NEW: PROJECT COMPLETION LOGIC
            // ==========================================
            $projects = DB::table('intern_projects')
                ->where('eti_id', $intern->eti_id)
                ->get();

            $totalProjects = $projects->count();
            // Assuming your projects table uses 'Completed' for finished projects
            $completedProjects = $projects->where('pstatus', 'Completed')->count(); 
            
            $intern->project_completion = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100) : 0;
            $intern->total_projects = $totalProjects;

            // ==========================================
            // 🔥 3. CODE QUALITY SCORE LOGIC
            // ==========================================
            // Fetching the overall score from the intern_evaluations table
            $avgQualityScore = DB::table('intern_evaluations')
                ->where('eti_id', $intern->eti_id)
                ->avg('overall_score'); 

            // Default to 0 if they haven't been evaluated yet
            $intern->code_quality = $avgQualityScore ? round($avgQualityScore, 1) : 0;
        }

        return view('content.supervisor.progress-monitoring', compact('interns', 'technology'));
    }
}