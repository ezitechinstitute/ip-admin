<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternationalInternsManagerController extends Controller
{
    public function index(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_international_interns')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access International Interns records.']);
    }

    // 1. Fetch Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1)
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keeping original case for DB index optimization
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query (English: Optimized international filter)
    $query = DB::table('intern_table')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        ->where('country', '!=', 'Pakistan')
        ->whereNotNull('country');

    // 3. Security Filter (English: Direct whereIn is faster than DB::raw LOWER)
    $query->whereIn('technology', $allowedTechNames)
          ->whereIn('intern_type', $allowedInternTypes);

    // 4. Filters (English: Prefix optimized search)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    if ($request->filled('tech')) {
        $techFilter = str_replace('-', ' ', $request->tech);
        $query->where('technology', $techFilter);
    }

    // 5. Counts Logic (English: Optimized status counts)
    // English: Cloning the query ensures filters are applied to the counts too
    $statusCounts = (clone $query)
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

    $statuses = ['Interview', 'Contact', 'Test', 'Completed', 'Active'];
    foreach ($statuses as $status) {
        if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
    }

    // 6. Pagination
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Ordering by ID (Primary Key) ensures instant results
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.international-interns.internationalInterns', compact(
        'interns', 'allowedTechNames', 'perPage', 'statusCounts'
    ));
}


public function exportInternationalInterns(Request $request)
{
    // English: Essential for large scale exports (300k+ rows)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetch Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1)
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keep original case to maintain database index speed
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query (English: Optimized for international filtering)
    $query = DB::table('intern_table')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        ->where('country', '!=', 'Pakistan')
        ->whereNotNull('country')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // Optional Filters (English: Prefix search optimization)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    if ($request->filled('tech')) {
        $techFilter = str_replace('-', ' ', $request->tech);
        $query->where('technology', $techFilter);
    }

    $fileName = 'international_interns_report_' . date('Y-m-d_His') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // 3. Optimized Streaming using cursor()
    return response()->streamDownload(function() use ($query) {
        // English: Clean buffer for clean CSV output
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Column Headers
        fputcsv($file, ['Name', 'Email', 'Phone', 'Technology', 'Status', 'Country', 'Intern Type', 'Join Date']);

        /* 🚀 English: cursor() is faster than chunk() for 300k+ records 
           as it uses a single database query with a cursor.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $intern) {
            fputcsv($file, [
                $intern->name,
                $intern->email,
                $intern->phone ?? 'N/A',
                $intern->technology,
                $intern->status,
                $intern->country,
                $intern->intern_type,
                $intern->join_date ?? 'N/A'
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}
}
