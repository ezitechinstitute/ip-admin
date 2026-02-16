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

    $internProjects = InternProject::with('intern')
        ->withCount('tasks') // ✅ TASK COUNT
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->whereHas('intern', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%");
                })
                ->orWhere('title', 'like', "%{$search}%");
            });
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('pstatus', $request->status);
        })
        ->orderByDesc('createdat')
        ->paginate($perPage)
        ->withQueryString();

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
    // 1. Permission Check (As per your view logic)
    $adminSettings = AdminSetting::first();
    $permissions = $adminSettings->export_permissions ?? [];
    if (!isset($permissions['admin']) || $permissions['admin'] != 1) {
        return redirect()->back()->with('error', 'Export permission is disabled.');
    }

    // 2. Clear output buffer to prevent ERR_INVALID_RESPONSE
    if (ob_get_level()) ob_end_clean();

    // 3. Query (Same filters as your listing page)
    $query = InternProject::with('intern')->withCount('tasks');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->whereHas('intern', function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%");
            })->orWhere('title', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('pstatus', $request->status);
    }

    $projects = $query->orderByDesc('createdat')->get();

    // 4. CSV Generation
    $fileName = 'Intern_Projects_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ];

    return response()->stream(function() use($projects) {
        $file = fopen('php://output', 'w');
        
        // Column Headers
        fputcsv($file, ['Intern Name', 'Project Title', 'Start Date', 'End Date', 'Duration', 'Days', 'Points', 'Status', 'Tasks']);

        foreach ($projects as $row) {
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
        }
        fclose($file);
    }, 200, $headers);
}


}
