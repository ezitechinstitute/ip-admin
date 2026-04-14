<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorProjectController extends Controller
{
    public function index()
    {
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

        if (!$supervisorId) {
            return redirect()->route('login')->with('error', 'Authentication error: Supervisor ID not found. Please re-login.');
        }

        // 1. Fetch Projects
        $projects = DB::table('intern_projects')
            ->join('manager_accounts', 'intern_projects.assigned_by', '=', 'manager_accounts.manager_id')
            ->select(
                'intern_projects.project_id',
                'intern_projects.eti_id',
                'intern_projects.email',
                'intern_projects.title',
                'intern_projects.start_date',
                'intern_projects.end_date',
                'intern_projects.assigned_by',
                'intern_projects.pstatus',
                'intern_projects.tech_stack',
                'intern_projects.difficulty_level',
                'manager_accounts.name as supervisor_name'
            )
            ->where('intern_projects.assigned_by', $supervisorId)
            ->orderByDesc('intern_projects.project_id')
            ->limit(20)
            ->get();

        // 2. Dynamic Intern Filtering (Based on Supervisor Technology Permissions)
        $permissionTechIds = \App\Models\SupervisorPermission::where('manager_id', $supervisorId)
            ->pluck('tech_id')
            ->toArray();

        $allowedTechNames = DB::table('technologies')
            ->whereIn('tech_id', $permissionTechIds)
            ->pluck('technology')
            ->toArray();

        if (empty($allowedTechNames)) {
            $allowedTechNames = ['___NO_ACCESS___']; 
        }

        $interns = DB::table('intern_accounts')
            ->select('eti_id', 'name', 'email')
            ->where('int_status', 'active')
            ->whereIn('int_technology', $allowedTechNames)
            ->get();

        return view('content.supervisor.projects', compact('projects', 'interns'));
    }

    public function store(Request $request)
    {
        // Removed duration, days, project_marks, and obt_marks
        $request->validate([
            'eti_id' => 'required',
            'email' => 'required|email',
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'tech_stack' => 'nullable|string|max:255',
            'difficulty_level' => 'required|string|in:Beginner,Intermediate,Advanced',
            'pstatus' => 'required|string|in:Ongoing,Submitted,Approved,Rejected,Expired,Completed,Pending',
        ]);

        DB::table('intern_projects')->insert([
            'eti_id' => $request->eti_id,
            'email' => $request->email,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'tech_stack' => $request->tech_stack,
            'difficulty_level' => $request->difficulty_level,
            'assigned_by' => Auth::guard('manager')->id() ?? session('manager_id'),
            'pstatus' => $request->pstatus,
            'createdat' => now(),
            'updatedat' => now(),
        ]);

        // Wrap in method_exists just in case these are defined in a trait or base controller
        if(method_exists($this, 'logActivity')) {
            $this->logActivity('Created Project', 'Project title: ' . $request->title . ' for Intern: ' . $request->eti_id);
        }
        if(method_exists($this, 'notifyIntern')) {
            $this->notifyIntern($request->eti_id, 'New Project', 'You have been assigned a new project: ' . $request->title);
        }

        return redirect()->back()->with('success', 'Project created successfully');
    }

    public function edit($id)
    {
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');
        
        $project = DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if (!$project) {
            return redirect()->route('supervisor.projects')->with('error', 'Project not found.');
        }

        return view('content.supervisor.projects.edit', compact('project'));
    }

    public function update(Request $request, $id)
    {
        // Removed duration, days, project_marks, and obt_marks
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'description' => 'required|string',
            'tech_stack' => 'nullable|string|max:255',
            'difficulty_level' => 'required|string|in:Beginner,Intermediate,Advanced',
            'pstatus' => 'required|string|in:Ongoing,Submitted,Approved,Rejected,Expired,Completed,Pending',
        ]);

        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');

        DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->update([
                'title' => $request->title,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'tech_stack' => $request->tech_stack,
                'difficulty_level' => $request->difficulty_level,
                'pstatus' => $request->pstatus,
                'updatedat' => now(),
            ]);

        if(method_exists($this, 'logActivity')) {
            $this->logActivity('Updated Project', "Modified Project ID: {$id}");
        }

        return redirect()->route('supervisor.projects')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $supervisorId = Auth::guard('manager')->id() ?? session('manager_id');
        
        $project = DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('assigned_by', $supervisorId)
            ->first();

        if ($project) {
            // Delete associated tasks first
            DB::table('project_tasks')->where('project_id', $id)->delete();
            // Delete project
            DB::table('intern_projects')->where('project_id', $id)->delete();
            
            if(method_exists($this, 'logActivity')) {
                $this->logActivity('Deleted Project', "Removed Project: '{$project->title}'");
            }
            
            return redirect()->route('supervisor.projects')->with('success', 'Project and its tasks deleted successfully.');
        }

        return redirect()->route('supervisor.projects')->with('error', 'Project not found.');
    }

    // ==========================================
    // TASK MANAGEMENT METHODS
    // ==========================================

    public function tasks($project_id)
    {
        $project = DB::table('intern_projects')
            ->where('project_id', $project_id)
            ->first();

        $tasks = DB::table('project_tasks')
            ->where('project_id', $project_id)
            ->orderByDesc('task_id')
            ->get();

        return view('content.supervisor.project-tasks', compact('project', 'tasks'));
    }

    public function storeTask(Request $request, $project_id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'milestone_title' => 'nullable|string|max:255',
            't_start_date' => 'required',
            't_end_date' => 'required',
            'task_days' => 'required|numeric',
            'task_duration' => 'required|numeric',
            'task_obt_mark' => 'nullable|numeric',
            'task_mark' => 'required|numeric',
            'task_status' => 'required|string',
            'description' => 'required|string',
        ]);

        $project = DB::table('intern_projects')
            ->where('project_id', $project_id)
            ->first();

        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        DB::table('project_tasks')->insert([
            'project_id' => $project_id,
            'eti_id' => $project->eti_id,
            'task_title' => $request->task_title,
            'milestone_title' => $request->milestone_title,
            't_start_date' => $request->t_start_date,
            't_end_date' => $request->t_end_date,
            'task_days' => $request->task_days,
            'task_duration' => $request->task_duration,
            'task_obt_mark' => $request->task_obt_mark ?? 0,
            'task_mark' => $request->task_mark,
            'assigned_by' => Auth::guard('manager')->id() ?? session('manager_id'),
            'task_status' => $request->task_status,
            'approved' => null,
            'review' => '',
            'task_screenshot' => '',
            'task_live_url' => '',
            'task_git_url' => '',
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task created successfully.');
    }

    public function loadCurriculum(Request $request, $project_id)
    {
        $request->validate([
            'technology' => 'required|string',
            'base_start_date' => 'required|date'
        ]);

        $project = \Illuminate\Support\Facades\DB::table('intern_projects')->where('project_id', $project_id)->first();
        
        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        $supervisorId = \Illuminate\Support\Facades\Auth::guard('manager')->id() ?? session('manager_id');
        $baseDate = \Carbon\Carbon::parse($request->base_start_date);
        
        $curriculumTasks = [];

        // Define standard curriculums matching your modal options
        if ($request->technology === 'Laravel') {
            $curriculumTasks = [
                ['title' => 'Setup Laravel & Environment', 'milestone' => 'Week 1: Basics', 'days' => 2, 'marks' => 10, 'desc' => 'Install Laravel, configure .env, set up database connection.'],
                ['title' => 'Database Migrations & Models', 'milestone' => 'Week 1: Basics', 'days' => 3, 'marks' => 15, 'desc' => 'Create necessary migrations and Eloquent models with relationships.'],
                ['title' => 'Authentication & Routing', 'milestone' => 'Week 2: Core Features', 'days' => 2, 'marks' => 15, 'desc' => 'Implement user authentication and middleware.'],
                ['title' => 'CRUD Operations & Controllers', 'milestone' => 'Week 2: Core Features', 'days' => 4, 'marks' => 20, 'desc' => 'Build controllers for core features with form validation.'],
                ['title' => 'API Development & Testing', 'milestone' => 'Week 3: Advanced', 'days' => 4, 'marks' => 20, 'desc' => 'Create RESTful APIs and test them.'],
                ['title' => 'Deployment & Final Review', 'milestone' => 'Week 4: Deployment', 'days' => 3, 'marks' => 20, 'desc' => 'Deploy the application to a live server and optimize performance.']
            ];
        } elseif ($request->technology === 'React') {
            $curriculumTasks = [
                ['title' => 'React App Setup & UI Structure', 'milestone' => 'Week 1: Fundamentals', 'days' => 2, 'marks' => 10, 'desc' => 'Initialize React app, setup Tailwind/Bootstrap.'],
                ['title' => 'Components & Props', 'milestone' => 'Week 1: Fundamentals', 'days' => 3, 'marks' => 15, 'desc' => 'Build reusable UI components and pass data using props.'],
                ['title' => 'State Management (Hooks)', 'milestone' => 'Week 2: State & Routing', 'days' => 3, 'marks' => 20, 'desc' => 'Implement useState and useEffect hooks for dynamic data.'],
                ['title' => 'React Router Setup', 'milestone' => 'Week 2: State & Routing', 'days' => 2, 'marks' => 15, 'desc' => 'Set up multi-page navigation using react-router-dom.'],
                ['title' => 'API Integration (Axios/Fetch)', 'milestone' => 'Week 3: Data Fetching', 'days' => 4, 'marks' => 20, 'desc' => 'Fetch data from external APIs and display it in the UI.'],
                ['title' => 'Build & Hosting', 'milestone' => 'Week 4: Deployment', 'days' => 2, 'marks' => 20, 'desc' => 'Create production build and host on Vercel/Netlify.']
            ];
        } elseif ($request->technology === 'UI/UX') {
            $curriculumTasks = [
                ['title' => 'User Research & Personas', 'milestone' => 'Week 1: Research', 'days' => 4, 'marks' => 20, 'desc' => 'Conduct user research and create detailed buyer personas.'],
                ['title' => 'Wireframing (Low Fidelity)', 'milestone' => 'Week 2: Wireframes', 'days' => 4, 'marks' => 20, 'desc' => 'Create basic structural wireframes in Figma.'],
                ['title' => 'High Fidelity Prototyping', 'milestone' => 'Week 3: UI Design', 'days' => 5, 'marks' => 30, 'desc' => 'Design the final UI with color theory, typography, and assets.'],
                ['title' => 'Interactive Prototype & Handoff', 'milestone' => 'Week 4: Prototyping', 'days' => 3, 'marks' => 30, 'desc' => 'Link screens for interactivity and prepare dev handoff files.']
            ];
        } elseif ($request->technology === 'WordPress') {
            $curriculumTasks = [
                ['title' => 'Localhost Setup & WP Install', 'milestone' => 'Week 1: Setup', 'days' => 2, 'marks' => 15, 'desc' => 'Install XAMPP/LocalWP and set up a fresh WordPress instance.'],
                ['title' => 'Theme Customization', 'milestone' => 'Week 2: Design', 'days' => 4, 'marks' => 25, 'desc' => 'Install a theme and customize it using Elementor.'],
                ['title' => 'Plugins & Functionality', 'milestone' => 'Week 3: Functionality', 'days' => 5, 'marks' => 30, 'desc' => 'Setup essential plugins (security, SEO) and configure basic settings.'],
                ['title' => 'On-Page SEO & Migration', 'milestone' => 'Week 4: Launch', 'days' => 4, 'marks' => 30, 'desc' => 'Optimize pages with SEO plugins and migrate to live server.']
            ];
        } else {
            return back()->with('error', 'Invalid technology selected.');
        }

        $currentStartDate = $baseDate->copy();

        foreach ($curriculumTasks as $task) {
            $endDate = $currentStartDate->copy()->addDays($task['days'] - 1); 

            \Illuminate\Support\Facades\DB::table('project_tasks')->insert([
                'project_id' => $project_id,
                'eti_id' => $project->eti_id,
                'task_title' => $task['title'],
                'milestone_title' => $task['milestone'],
                't_start_date' => $currentStartDate->toDateString(),
                't_end_date' => $endDate->toDateString(),
                'task_days' => $task['days'],
                'task_duration' => $task['days'],
                'task_obt_mark' => 0,
                'task_mark' => $task['marks'],
                'assigned_by' => $supervisorId,
                'task_status' => 'Ongoing',
                'description' => $task['desc'],
                
                // 🔥 ADD THESE MISSING DEFAULT VALUES:
                'approved' => null,
                'review' => '',
                'task_screenshot' => '',
                'task_live_url' => '',
                'task_git_url' => '',
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $currentStartDate = $endDate->copy()->addDay();
        
        }

        if(method_exists($this, 'logActivity')) {
            $this->logActivity('Loaded Curriculum', "Loaded {$request->technology} curriculum for Project ID: {$project_id}");
        }

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', count($curriculumTasks) . " {$request->technology} tasks loaded successfully!");
    }

    public function editTask($project_id, $task_id)
    {
        $project = DB::table('intern_projects')->where('project_id', $project_id)->first();
        $task = DB::table('project_tasks')->where('task_id', $task_id)->first();

        if (!$project || !$task) {
            return back()->with('error', 'Task or Project not found.');
        }

        return view('content.supervisor.projects.edit-task', compact('project', 'task'));
    }

    public function updateTask(Request $request, $project_id, $task_id)
    {
        $request->validate([
            'task_title' => 'required|string|max:255',
            'milestone_title' => 'nullable|string|max:255',
            't_start_date' => 'required',
            't_end_date' => 'required',
            'task_days' => 'required|numeric',
            'task_duration' => 'required|numeric',
            'task_obt_mark' => 'nullable|numeric',
            'task_mark' => 'required|numeric',
            'task_status' => 'required|string',
            'description' => 'required|string',
        ]);

        DB::table('project_tasks')
            ->where('task_id', $task_id)
            ->where('project_id', $project_id)
            ->update([
                'task_title' => $request->task_title,
                'milestone_title' => $request->milestone_title,
                't_start_date' => $request->t_start_date,
                't_end_date' => $request->t_end_date,
                'task_days' => $request->task_days,
                'task_duration' => $request->task_duration,
                'task_obt_mark' => $request->task_obt_mark ?? 0,
                'task_mark' => $request->task_mark,
                'task_status' => $request->task_status,
                'description' => $request->description,
                'updated_at' => now(),
            ]);

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task updated successfully.');
    }

    public function deleteTask($project_id, $task_id)
    {
        DB::table('project_tasks')
            ->where('project_id', $project_id)
            ->where('task_id', $task_id)
            ->delete();

        return redirect()->route('supervisor.projects.tasks', $project_id)
            ->with('success', 'Task deleted successfully.');
    }
}