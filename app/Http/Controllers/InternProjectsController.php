<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\InternProject;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InternProjectsController extends Controller
{

public function interProjects(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Changed 'id' to 'project_id' and 'intern_id' to 'eti_id' based on your schema
    $query = InternProject::select('project_id', 'eti_id', 'email', 'title', 'pstatus', 'createdat');

    // English: Eager load intern using eti_id (Ensure relationship is defined in Model)
    $query->with(['intern' => function($q) {
        $q->select('eti_id', 'name'); // English: Matching eti_id from your accounts table
    }])->withCount('tasks');

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "{$search}%")
              ->orWhere('eti_id', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('pstatus', $request->status);
    }

    // English: Fixed sorting column name to 'project_id'
    $query->orderBy('project_id', 'desc');

    // 🔢 Pagination
    $internProjects = $query->paginate($perPage)->withQueryString();

    return view(
        'pages.admin.intern-projects.internProjects',
        compact('internProjects', 'perPage')
    );
}
    public function updateInternProject(Request $request){
        $request->validate([
            'project_title' => 'required',
            'pstatus' => 'required'
        ]);
        InternProject::where('project_id', $request->int_id)->update([
            'title' => $request->project_title,
            'pstatus' => $request->pstatus
        ]);
        return redirect()->back()->with('success', 'Intern Project Successfully updated!');
    }


   public function exportInternProjectsCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for 3L+ records
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Permission Check
    $adminSettings = AdminSetting::first();
    $permissions = $adminSettings->export_permissions ?? [];
    if (!isset($permissions['admin']) || $permissions['admin'] != 1) {
        return redirect()->back()->with('error', 'Export permission is disabled.');
    }

    // 2. Clear output buffer
    if (ob_get_level()) ob_end_clean();

    // 3. Optimized Query (English: Selecting only necessary columns)
    // English: We use eti_id to join with interns efficiently
    $query = InternProject::select([
        'project_id', 'eti_id', 'title', 'start_date', 'end_date', 
        'duration', 'days', 'obt_marks', 'project_marks', 'pstatus'
    ])
    ->with(['intern' => function($q) {
        $q->select('eti_id', 'name'); // English: Only fetch intern name
    }])
    ->withCount('tasks');

    // 🔍 Search Optimization
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "{$search}%")
              ->orWhereHas('intern', function ($sub) use ($search) {
                  $sub->where('name', 'like', "{$search}%");
              });
        });
    }

    if ($request->filled('status')) {
        $query->where('pstatus', $request->status);
    }

    // 4. CSV Streaming Setup
    $fileName = 'Intern_Projects_' . date('Y-m-d_His') . '.csv';
    
    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    return response()->stream(function() use($query) {
        $file = fopen('php://output', 'w');
        
        // Column Headers
        fputcsv($file, ['Intern Name', 'Project Title', 'Start Date', 'End Date', 'Duration', 'Days', 'Points', 'Status', 'Tasks']);

        // English: cursor() processes one record at a time, saving your server from crashing
        
        foreach ($query->orderBy('project_id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->intern->name ?? 'N/A',
                $row->title,
                $row->start_date,
                $row->end_date,
                $row->duration,
                $row->days,
                $row->obt_marks . '/' . $row->project_marks,
                ucfirst($row->pstatus),
                $row->tasks_count
            ]);

            // English: Periodically clear buffer to keep memory usage flat
            flush();
        }
        fclose($file);
    }, 200, $headers);
}


}
