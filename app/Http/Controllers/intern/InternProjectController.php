<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternProjectController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get all projects for this intern using eti_id
        $projects = DB::table('intern_projects')
            ->where('eti_id', $intern->eti_id)
            ->orderBy('createdat', 'desc')
            ->paginate(10);
        
        // Calculate progress for each project based on tasks - FIXED: Use 'tasks' table
        foreach ($projects as $project) {
            $totalTasks = DB::table('tasks')
                ->where('intern_id', $intern->int_id)
                ->where('title', 'LIKE', '%' . $project->title . '%')
                ->count();
            
            $completedTasks = DB::table('tasks')
                ->where('intern_id', $intern->int_id)
                ->where('title', 'LIKE', '%' . $project->title . '%')
                ->where('status', 'approved')
                ->count();
            
            $project->progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        }
        
        // Get statistics
        $stats = [
            'total' => DB::table('intern_projects')->where('eti_id', $intern->eti_id)->count(),
            'ongoing' => DB::table('intern_projects')->where('eti_id', $intern->eti_id)->where('pstatus', 'Ongoing')->count(),
            'submitted' => DB::table('intern_projects')->where('eti_id', $intern->eti_id)->where('pstatus', 'Submitted')->count(),
            'approved' => DB::table('intern_projects')->where('eti_id', $intern->eti_id)->where('pstatus', 'Approved')->count(),
        ];
        
        return view('pages.intern.projects.index', compact('projects', 'stats'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $project = DB::table('intern_projects')
            ->where('project_id', $id)
            ->where('eti_id', $intern->eti_id)
            ->first();
        
        if (!$project) {
            abort(404, 'Project not found');
        }
        
        // Get tasks for this project - FIXED: Use 'tasks' table
        $tasks = DB::table('tasks')
            ->where('intern_id', $intern->int_id)
            ->where('title', 'LIKE', '%' . $project->title . '%')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get supervisor name
        $supervisorName = 'Not Assigned';
        if ($project->assigned_by) {
            $supervisor = DB::table('manager_accounts')->where('manager_id', $project->assigned_by)->first();
            $supervisorName = $supervisor ? $supervisor->name : 'Not Assigned';
        }
        
        return view('pages.intern.projects.show', compact('project', 'tasks', 'supervisorName'));
    }
}