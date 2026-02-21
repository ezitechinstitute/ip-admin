<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Models\InternAccount;
use App\Models\AdminSetting;
use App\Models\Technologies;

class AllManagerInternController extends Controller
{
    public function index(){
        return view('pages.manager.all-interns.allinterns');
    }
public function newInterns()
{
    return view('pages.manager.all-interns.newInterns');
}
public function contactWith()
{
    return view('pages.manager.all-interns.contactWith');
}
public function interview()
{
    return view('pages.manager.all-interns.interview');
}
     
    public function active(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');
    
    $managerId = $manager->manager_id; 

    // 1. Fetch Allowed Permissions
    $allowedPermissions = DB::table('manager_permissions')
        ->where('manager_id', $managerId)
        ->get();

    $allowedTechIds = $allowedPermissions->pluck('tech_id')->toArray();
    $allowedTechNames = DB::table('technologies')->whereIn('tech_id', $allowedTechIds)->pluck('technology')->toArray();
    $allowedInternTypes = $allowedPermissions->pluck('interview_type')->map(fn($i) => strtolower($i))->toArray();

    $query = InternAccount::query()
        ->join('intern_table', 'intern_accounts.email', '=', 'intern_table.email')
        ->select(
            'intern_accounts.*', 
            'intern_table.intern_type', 
            'intern_table.technology', 
            'intern_table.city', 
            'intern_accounts.start_date as joining_date',
            'intern_accounts.review'
        )
        ->where('intern_accounts.int_status', 'active');

    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('intern_table.technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_table.intern_type)'), $allowedInternTypes);
    });

  

if ($request->filled('date_range')) {
    $rawDate = str_replace('+', ' ', $request->date_range);
    
    if (str_contains($rawDate, ' to ')) {
        $dates = explode(' to ', $rawDate);
        $startDate = trim($dates[0]) . ' 00:00:00';
        $endDate   = trim($dates[1]) . ' 23:59:59';
        
        $query->whereBetween('intern_table.created_at', [$startDate, $endDate]);
    } else {
        $singleDate = trim($rawDate);
        $query->whereDate('intern_table.created_at', $singleDate);
    }
}

    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('intern_accounts.name', 'like', '%' . $request->search . '%')
              ->orWhere('intern_accounts.eti_id', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->filled('status')) {
        $techName = str_replace('-', ' ', $request->status);
        $query->where('intern_table.technology', $techName);
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_table.intern_type', $request->intern_type);
    }

    $pageLimitSet = AdminSetting::first();
$defaultLimit = $pageLimitSet->pagination_limit ?? 15;

$perPage = $request->input('per_page', $defaultLimit);

$internAccounts = $query->orderBy('intern_accounts.int_id', 'desc')
                        ->paginate($perPage)
                        ->withQueryString();

    $activeTechnologies = Technologies::whereIn('tech_id', $allowedTechIds)->where('status', 1)->get();
    
    $stats = [
        'active' => $internAccounts->total(), 
        'interview' => 0, 
        'contacted' => 0, 
        'test_attempt' => 0, 
        'test_completed' => 0
    ];

    return view('pages.manager.all-interns.managerActiveInterns', compact('internAccounts', 'stats', 'activeTechnologies'));
}


    public function exportActiveCSV(Request $request)
{
    $query =InternAccount::query()
        ->join('intern_table', 'intern_accounts.email', '=', 'intern_table.email')
        ->select('intern_accounts.*', 'intern_table.intern_type', 'intern_table.technology', 'intern_table.city', 'intern_accounts.start_date as joining_date')
        ->where('intern_accounts.int_status', 'active');

    // --- APPLY SAME FILTERS AS LISTING ---
    
    if ($request->filled('search')) {
        $searchTerm = '%' . $request->search . '%';
        $query->where(function($q) use ($searchTerm) {
            $q->where('intern_accounts.name', 'like', $searchTerm)
              ->orWhere('intern_accounts.eti_id', 'like', $searchTerm);
        });
    }

    if ($request->filled('date_range')) {
        $rawDate = str_replace('+', ' ', $request->date_range);
        if (str_contains($rawDate, ' to ')) {
            $dates = explode(' to ', $rawDate);
            $query->whereBetween('intern_table.created_at', [trim($dates[0]).' 00:00:00', trim($dates[1]).' 23:59:59']);
        } else {
            $query->whereDate('intern_table.created_at', trim($rawDate));
        }
    }

    if ($request->filled('status')) {
        $techName = str_replace('-', ' ', $request->status);
        $query->where('intern_table.technology', $techName);
    }

    $data = $query->get();

    // --- GENERATE CSV RESPONSE ---

    $fileName = 'manager_active_interns_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI ID', 'Name', 'Email', 'City', 'Phone', 'Technology', 'Type', 'Status'];

    $callback = function() use($data, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $row) {
            fputcsv($file, [
                $row->eti_id,
                $row->name,
                $row->email,
                $row->city,
                $row->phone,
                $row->technology,
                $row->intern_type,
                $row->int_status,
                // $row->joining_date
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function removeIntern($id)
{
    // English comments: Update the record directly using the correct primary key 'int_id'
    $affected = InternAccount::where('int_id', $id)->update([
        'int_status' => 'Freeze' 
    ]);

    if ($affected) {
        return redirect()->back()->with('success', 'Intern status updated to Freeze.');
    }

    return redirect()->back()->with('error', 'Intern not found or could not be updated.');
}


public function updateInternStatus(Request $request)
{
    $request->validate([
        'id' => 'required',
        'status' => 'required',
    ]);

    // English comments: Use int_id directly to avoid the 'id is null' issue
    $updated = \App\Models\InternAccount::where('int_id', $request->id)->update([
        'int_status' => $request->status,
        'review'     => $request->review, // English comments: Save the remarks/review
    ]);

    if ($updated) {
        return redirect()->back()->with('success', 'Intern updated successfully!');
    }

    return redirect()->back()->with('error', 'Update failed.');
}
}
