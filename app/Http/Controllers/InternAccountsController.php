<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\InternAccount;
use App\Models\InternProject;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InternAccountsController extends Controller
{
    public function interAccounts(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = InternAccount::query();

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request

   
    if(!empty($status)){
        $query->where('int_status', strtolower($status));
    }
    
    //get latest record
    // $query->latest();
    
    $internAccounts = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.intern-accounts.internAccounts', compact('internAccounts', 'perPage', 'status'));
    }

    public function updateInternAccount(Request $request){
$request->validate([
        'int_id' => 'required',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'int_technology' => 'required|string',
        'int_status' => 'required|in:active,test,freeze'
    ]);

    InternAccount::where('int_id', $request->int_id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'int_technology' => $request->int_technology,
            'int_status' => $request->int_status
        ]);
        
        return redirect()->back()->with('success', 'Intern account updated successfully!');
    }

    public function InternViewProfileAccount(Request $request, $id)
{
    // Intern info
    $interneAccountDetails = InternAccount::where('int_id', $id)->firstOrFail();

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // Tasks list (with search + pagination)
    $tasks = ProjectTask::with('project')
        ->where('eti_id', $interneAccountDetails->eti_id)
        ->when($request->filled('search'), function ($query) use ($request) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('task_title', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($sub) use ($search) {
                      $sub->where('title', 'like', "%{$search}%");
                  });
            });
        })
        ->orderByDesc('created_at')
        ->paginate($perPage)
        ->withQueryString();

    // ✅ TOTAL TASKS DONE
    $totalTasksDone = ProjectTask::where('eti_id', $interneAccountDetails->eti_id)
        ->where('task_status', 'Approved')
        ->count();

    // ✅ PROJECTS DONE (unique projects with completed tasks)
    $projectsDone = InternProject::where('eti_id', $interneAccountDetails->eti_id)
        ->where('pstatus', 'Completed')
        ->distinct('project_id')
        ->count('project_id');
    // return $tasks;
    return view(
        'pages.admin.intern-accounts.internViewProfile',
        compact(
            'interneAccountDetails',
            'tasks',
            'perPage',
            'totalTasksDone',
            'projectsDone'
        )
    );
}

    public function exportInternAccountsCSV(Request $request)
{
    // 1. Permission Check
    $settings = \App\Models\AdminSetting::first();
    $permissions = $settings->export_permissions ?? [];
    if (!isset($permissions['admin']) || $permissions['admin'] != 1) {
        return redirect()->back()->with('error', 'Export permission is disabled.');
    }

    // 2. Clear buffers to avoid extra whitespace/errors in CSV
    if (ob_get_level()) ob_end_clean();

    // 3. Query
    $query = \App\Models\InternAccount::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('int_status', strtolower($request->status));
    }

    $interns = $query->get();

    // 4. File Setup
    $fileName = 'Intern_Accounts_' . date('Y-m-d') . '.csv';
    
    return response()->stream(function() use($interns) {
        $file = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($file, ['ETI-ID', 'Name', 'Email', 'Phone', 'Technology', 'Status', 'Start Date', 'Review']);

        foreach ($interns as $intern) {
            fputcsv($file, [
                $intern->eti_id,
                $intern->name,
                $intern->email,
                $intern->phone,
                $intern->int_technology,
                ucfirst($intern->int_status),
                $intern->start_date,
                $intern->review
            ]);
        }
        fclose($file);
    }, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=\"$fileName\"",
        "Pragma" => "no-cache",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Expires" => "0"
    ]);
}


}
