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
    public function interAccounts(Request $request)
{
    // English: Fetch pagination limit from admin settings
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Changed 'id' to 'int_id' to match your table schema
    $query = InternAccount::select('int_id', 'eti_id', 'name', 'email', 'int_status', 'int_technology');

    // 🔍 Optimized Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            // English: Prefix search (search%) for high performance with 3L+ records
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    $status = $request->status;
    if (!empty($status)) {
        $query->where('int_status', $status);
    }
    
    // English: Sorting by 'int_id' instead of 'id' to fix the 500 error
    $query->orderBy('int_id', 'desc');
    
    // 🔢 Pagination
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
    // English: Fetch intern using int_id
    $interneAccountDetails = InternAccount::where('int_id', $id)->firstOrFail();
    
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Always select only necessary columns to save memory (RAM)
    $query = ProjectTask::select('task_id', 'project_id', 'eti_id', 'task_title', 'task_status', 'created_at')
        ->where('eti_id', trim($interneAccountDetails->eti_id))
        ->with(['project' => function($q) {
            $q->select('project_id', 'title');
        }]);

    // 🔍 Optimized Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search 'search%' for better index performance
            $q->where('task_title', 'like', "{$search}%");
            
            // English: Only use whereHas if absolutely necessary as it slows down large tables
            $q->orWhereHas('project', function ($sub) use ($search) {
                $sub->where('title', 'like', "{$search}%");
            });
        });
    }

    // English: Ordering by Primary Key (task_id) is much faster than 'created_at'
    $tasks = $query->orderBy('task_id', 'desc')->paginate($perPage)->withQueryString();

    // ✅ COUNTS (English: These run fast if 'eti_id' and 'task_status' are indexed)
    $totalTasksDone = ProjectTask::where('eti_id', $interneAccountDetails->eti_id)
        ->whereIn('task_status', ['Approved', 'Completed'])
        ->count();

    $projectsDone = InternProject::where('eti_id', $interneAccountDetails->eti_id)
        ->whereIn('pstatus', ['Completed', 'Approved'])
        ->count();

    return view(
        'pages.admin.intern-accounts.internViewProfile',
        compact('interneAccountDetails', 'tasks', 'perPage', 'totalTasksDone', 'projectsDone')
    );
}

   public function exportInternAccountsCSV(Request $request)
{
    // English: Remove execution time limit and increase memory for massive export
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Permission Check
    
    $permissions = $settings->export_permissions ?? [];
    if (!isset($permissions['admin']) || $permissions['admin'] != 1) {
        return redirect()->back()->with('error', 'Export permission is disabled.');
    }

    // 2. Clear buffers to avoid extra whitespace/errors in CSV
    if (ob_get_level()) ob_end_clean();

    // 3. Optimized Query (English: Selecting only needed columns to reduce RAM usage)
    $query = \App\Models\InternAccount::select([
        'eti_id', 'name', 'email', 'phone', 'int_technology', 'int_status', 'start_date', 'review'
    ]);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search for index optimization
            $q->where('name', 'like', "{$search}%")
              ->orWhere('email', 'like', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        // English: Avoid strtolower() inside where clause to keep index active
        $query->where('int_status', $request->status);
    }

    // 4. File Setup & Streaming
    $fileName = 'Intern_Accounts_' . date('Y-m-d_His') . '.csv';
    
    // English: Passing the $query instead of $interns (data) to avoid memory overload
    return response()->stream(function() use($query) {
        $file = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($file, ['ETI-ID', 'Name', 'Email', 'Phone', 'Technology', 'Status', 'Start Date', 'Review']);

        // English: Using cursor() to fetch 1 row at a time from 3L+ records
        
        foreach ($query->orderBy('int_id', 'desc')->cursor() as $intern) {
            fputcsv($file, [
                $intern->eti_id,
                $intern->name,
                $intern->email,
                " " . $intern->phone, // English: Space prevents scientific notation in Excel
                $intern->int_technology,
                ucfirst($intern->int_status),
                $intern->start_date,
                $intern->review
            ]);

            // English: Periodically flush buffer to keep download active
            flush();
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
