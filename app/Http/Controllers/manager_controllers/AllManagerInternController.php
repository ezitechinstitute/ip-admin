<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\InternAccount;
use App\Models\Technologies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Response;

class AllManagerInternController extends Controller
{
    public function myInterns(Request $request)
{
    // English: Fast Auth Check
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // English: Authorization check using Gate
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_my_interns')) {
        return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Access Denied.']);
    }

    // 1. English: Optimized Permission Fetching (Using pluck directly for speed)
    $cacheKey = 'mgr_perms_' . $manager->manager_id;
    $permissions = cache()->remember($cacheKey, 3600, function() use ($manager) {
        return DB::table('manager_permissions')
            ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
            ->where('manager_permissions.manager_id', $manager->manager_id)
            ->where('technologies.status', 1)
            ->select('technologies.technology', 'manager_permissions.interview_type')
            ->get();
    });

    $allowedTechNames = $permissions->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $permissions->pluck('interview_type')->map(fn($t) => trim($t))->unique()->toArray();

    // 2. English: Base Query setup (Keeping only essential columns)
    $query = DB::table('intern_table')
        ->select('id', 'name', 'email', 'technology', 'intern_type', 'status', 'created_at', 'image', 'phone', 'city', 'join_date')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. English: Optimized Filtering
    if ($request->filled('search')) {
        $search = $request->search;
        // English: Grouped Where is crucial so it doesn't break the whereIn logic above
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', $search . '%')
              ->orWhere('email', 'LIKE', $search . '%');
        });
    }

    if ($request->filled('intern_type')) $query->where('intern_type', $request->intern_type);
    if ($request->filled('status')) $query->where('status', $request->status);
    if ($request->filled('tech')) $query->where('technology', str_replace('-', ' ', $request->tech));

    // 4. English: Super Fast Statistics (Executing only 1 optimized query)
    $statusCounts = cache()->remember('counts_' . $manager->manager_id, 300, function() use ($query) {
        // English: Clone to avoid affecting the main pagination query
        return (clone $query)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
    });

    // English: Ensure all keys exist to avoid Blade errors
    $defaultStatuses = ['Interview', 'Contact', 'Test', 'Completed', 'Active'];
    foreach ($defaultStatuses as $s) {
        if (!isset($statusCounts[$s])) $statusCounts[$s] = 0;
    }

    // 5. English: Pagination (Optimized for performance)
    $perPage = (int) $request->input('per_page', 15);
    
    // English: Order by ID desc is fast on indexed primary keys
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.allInterns', compact('interns', 'allowedTechNames', 'perPage', 'statusCounts'));
}



public function exportMyInternsCSV(Request $request)
{
    // English: Setting high limits for processing large data (300k+ records)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetching Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')->map(fn($t) => trim($t))->unique()->toArray();

    // 2. Base Query (English: Using Query Builder for maximum speed)
    $query = DB::table('intern_table')
        ->select('name', 'email', 'phone', 'city', 'intern_type', 'technology', 'join_date', 'status')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. Filters (English: Prefix optimized search for indexing)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'LIKE', $search . '%')
              ->orWhere('email', 'LIKE', $search . '%');
        });
    }

    if ($request->filled('status')) {
        $val = str_replace('-', ' ', $request->status);
        $actualStatuses = ['Active', 'Interview', 'Contact', 'Test', 'Completed'];
        if (in_array($val, $actualStatuses)) {
            $query->where('status', $val);
        } else {
            $query->where('technology', $val);
        }
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $fileName = 'manager_interns_report_' . date('Y-m-d_His') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // 4. Optimized Streaming
    return response()->streamDownload(function() use ($query) {
        // English: Avoid buffer interference on Live servers
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 

        // CSV Header
        fputcsv($file, ['Name', 'Email', 'Phone', 'City', 'Type', 'Technology', 'Join Date', 'Status']);

        /* 🚀 English: cursor() is essential here. It doesn't load 300k records at once.
           It keeps only 1 row in memory, then moves to next.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city ?? 'N/A',
                $row->intern_type,
                $row->technology,
                $row->join_date ?? 'N/A',
                $row->status
            ]);
        }
        
        fclose($file);
    }, $fileName, $headers);
}


public function newInterns(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_new_interns')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access New Interns records.']);
    }

    // 1. Fetching Manager Permissions (English: Optimized permissions fetch)
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keep original case for DB comparison to ensure index usage
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query (English: Start with primary filter)
    $query = DB::table('intern_table')->where('status', 'Interview');

    // 3. Security Filter (English: Optimized to avoid LOWER() on column to keep indexes active)
    $query->whereIn('technology', $allowedTechNames)
          ->whereIn('intern_type', $allowedInternTypes);

    // 4. Search & Dropdown Filters (English: Prefix search for massive performance gain)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Using prefix search ("search%") instead of double wildcard ("%search%") 
            // to allow MySQL to use the index effectively on 300k+ rows.
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    // Technology Filter
    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    // Intern Type Filter
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Sorting by ID (Primary Key) is instant on indexed tables
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.newInterns', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportNewInternsCSV(Request $request)
{
    // English: Setting high limits to prevent timeouts on large datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetching Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Avoiding LOWER() to keep indexes optimized
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($t) => trim($t))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Interview)
    $query = DB::table('intern_table')
        ->where('status', 'Interview')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. Apply Current Filters (Search, Tech, Intern Type)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Using prefix search for faster index retrieval
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $fileName = 'new_interns_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Internship Type', 'Technology', 'Join Date', 'Status'];

    // 4. Optimized CSV Generation using streamDownload
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear any output buffer to ensure clean CSV file
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility (Important for non-English names)
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Header
        fputcsv($file, $columns);

        /* 🚀 English: cursor() is critical for 300,000+ records. 
           It fetches one record at a time to keep memory usage low.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city ?? 'N/A',
                $row->intern_type,
                $row->technology,
                $row->join_date ?? 'N/A',
                $row->status
            ]);
        }
        
        fclose($file);
    }, $fileName, $headers);
}













public function contactWith(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_contact_with')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Contact With records.']);
    }

    // 1. Fetching Allowed Technologies
    // English comments: Get only active technologies and permission types linked to this manager
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keeping original case to utilize database B-Tree indexes effectively
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query
    // English comments: We start with interns having 'Contact' status
    $query = DB::table('intern_table')->where('status', 'Contact');

    // 3. Applying Security Filters (Mandatory)
    // English: Avoid LOWER() or other functions on columns to keep the index active
    $query->whereIn('technology', $allowedTechNames)
          ->whereIn('intern_type', $allowedInternTypes);

    // 4. Dynamic Filters (User input from Frontend)
    
    // Search Logic: Name or Email (Prefix Optimized)
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            // English: Prefix search ("term%") is significantly faster than "%term%" on large tables
            $q->where('name', 'LIKE', "{$searchTerm}%")
              ->orWhere('email', 'LIKE', "{$searchTerm}%");
        });
    }

    // Technology Filter
    if ($request->filled('status')) {
        // English comments: direct match is better than LIKE for exact technology filtering
        $techFilter = str_replace('-', ' ', $request->status);
        $query->where('technology', $techFilter);
    }

    // Intern Type Filter (Onsite/Remote)
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Sorting by primary key (id) ensures the fastest possible sorting performance
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.contactWith', compact('interns', 'allowedTechNames', 'perPage'));
}

public function exportContactWith(Request $request)
{
    // English: Prevent timeouts and memory crashes for large datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetching Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keeping original case to maintain database index efficiency
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($t) => trim($t))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Contact)
    $query = DB::table('intern_table')
        ->where('status', 'Contact')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. Apply Current Filters (Search, Tech, Intern Type)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Prefix search for significantly faster performance on huge tables
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $fileName = 'contact_interns_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Internship Type', 'Technology', 'Join Date', 'Status'];

    // 4. Optimized Streaming via streamDownload
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear any output buffer to ensure a clean file download
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper rendering of special characters in Excel
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write CSV Header
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to process 300,000+ rows 
           by only keeping ONE row in memory at any given time.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city ?? 'N/A',
                $row->intern_type,
                $row->technology,
                $row->join_date ?? 'N/A',
                $row->status
            ]);
        }
        
        fclose($file);
    }, $fileName, $headers);
}





public function test(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_interview_test')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Interview Test records.']);
    }

    // 1. Fetching Manager Permissions
    // English comments: Get allowed technologies and matching interview types
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keep original case for efficient indexing. Avoid LOWER() in DB query.
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Test)
    $query = DB::table('intern_table')->where('status', 'Test');

    // 3. Security Filter (Optimized)
    // English: Direct whereIn is 100x faster than using DB::raw('LOWER(...)') on 300k records
    $query->whereIn('technology', $allowedTechNames)
          ->whereIn('intern_type', $allowedInternTypes);

    // 4. Search & Dropdown Filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Using prefix search to utilize B-Tree indexes on Name and Email
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    // Technology Filter
    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    // Intern Type Filter
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Ordering by primary key (id) is the most efficient way to sort large datasets
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.test', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportTestCSV(Request $request)
{
    // English: Prevent timeouts and memory crashes for large datasets (300k+)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetching Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keep original case for efficient indexing. Avoid DB::raw on 300k records.
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($t) => trim($t))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Test)
    $query = DB::table('intern_table')
        ->where('status', 'Test')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. Apply Active Filters (Search, Tech, Type)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Prefix search is significantly faster on large tables
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $fileName = 'test_interns_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Internship Type', 'Technology', 'Join Date', 'Status'];

    // 4. Optimized Streaming via streamDownload
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear any output buffer to ensure a clean file download
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper rendering of special characters in Excel
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write CSV Header
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to process 300,000+ rows 
           by only keeping ONE row in memory at any given time.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city ?? 'N/A',
                $row->intern_type,
                $row->technology,
                $row->join_date ?? 'N/A',
                $row->status
            ]);
        }
        
        fclose($file);
    }, $fileName, $headers);
}
















public function completed(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_test_completed')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Completed Interns records.']);
    }

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Keep original case for DB comparison to ensure index usage
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => trim($type))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Completed)
    $query = DB::table('intern_table')->where('status', 'Completed');

    // 3. Security Filter (Optimized)
    // English: Direct whereIn is much faster than DB::raw('LOWER(...)') as it uses the index.
    $query->whereIn('technology', $allowedTechNames)
          ->whereIn('intern_type', $allowedInternTypes);

    // 4. Search & Dropdown Filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Using prefix search to utilize B-Tree indexing on 'name' and 'email'
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    // Technology Filter
    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    // Intern Type Filter
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Sorting by primary key (id) is nearly instantaneous even on large tables.
    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    $internIds = $interns->pluck('id')->toArray();
    $certificateRequests = DB::table('certificate_requests')
        ->whereIn('intern_id', $internIds)
        ->orderBy('created_at', 'desc')
        ->get()
        ->keyBy('intern_id');

    return view('pages.manager.all-interns.completed', compact('interns', 'allowedTechNames', 'perPage', 'certificateRequests'));
}



public function exportCompletedCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large datasets (300k+)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetching Manager Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    // English: Removing DB functions from query to allow index usage
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($t) => trim($t))
        ->unique()
        ->toArray();

    // 2. Base Query (Status: Completed)
    $query = DB::table('intern_table')
        ->where('status', 'Completed')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn('intern_type', $allowedInternTypes);

    // 3. Apply Current Filters (Search, Tech, Intern Type)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // English: Prefix search is significantly faster for large tables
            $q->where('name', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }

    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $fileName = 'completed_interns_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Internship Type', 'Technology', 'Join Date', 'Status'];

    // 4. Optimized Streaming via streamDownload
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Ensure a clean buffer for live servers
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper Excel character rendering
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write CSV Header
        fputcsv($file, $columns);

        /* 🚀 English: cursor() processes 300,000+ rows line-by-line.
           It keeps memory usage constant and very low.
        */
        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city ?? 'N/A',
                $row->intern_type,
                $row->technology,
                $row->join_date ?? 'N/A',
                $row->status
            ]);
        }
        
        fclose($file);
    }, $fileName, $headers);
}









public function active(Request $request)
{
    $manager = auth('manager')->user();
    if (!$manager) return redirect()->route('login');
    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_active_interns')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to access Active Interns records.']);
    }
    $managerId = $manager->manager_id;

    // Allowed techs for this manager
    $allowedTechsData = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $managerId)
        ->where('technologies.status', 1)
        ->select('technologies.technology')
        ->get();

    $allowedTechNames = $allowedTechsData->pluck('technology')->unique()->toArray();

    // Pagination limit
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // Base query: join intern_accounts with intern_table
    $query = DB::table('intern_accounts')
        ->leftJoin('intern_table', 'intern_accounts.email', '=', 'intern_table.email')
        ->select(
            'intern_accounts.int_id',
            'intern_accounts.eti_id',
            'intern_accounts.name',
            'intern_accounts.email',
            'intern_accounts.phone',
            'intern_accounts.review',
            'intern_accounts.int_status as status', // aliased to avoid errors
            'intern_accounts.int_technology',
            'intern_table.image as profile_image',
            'intern_table.city',
            'intern_table.intern_type',
            'intern_accounts.start_date as join_date'
        )
        ->where('intern_accounts.int_status', 'Active');

    // Filter only allowed technologies
    if (!empty($allowedTechNames)) {
        $query->whereIn('intern_accounts.int_technology', $allowedTechNames);
    } else {
        $query->whereRaw('1 = 0'); // no allowed techs
    }

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('intern_accounts.name', 'like', "%{$search}%")
              ->orWhere('intern_accounts.email', 'like', "%{$search}%")
              ->orWhere('intern_accounts.eti_id', 'like', "%{$search}%");
        });
    }

    // Technology filter
    if ($request->filled('status')) {
        $tech = str_replace('-', ' ', $request->status);
        $query->where('intern_accounts.int_technology', $tech);
    }

    // Internship type filter (Onsite/Remote)
    if ($request->filled('intern_type')) {
        $query->where('intern_table.intern_type', $request->intern_type);
    }

    // Pagination
    $internAccounts = $query->orderBy('intern_accounts.int_id', 'desc')
        ->paginate($perPage)
        ->withQueryString();

    return view('pages.manager.all-interns.managerActiveInterns', compact(
        'internAccounts', 'allowedTechNames', 'perPage'
    ));
}






public function removeInternAccActive($id)
{
    DB::table('intern_accounts')
        ->where('int_id', $id)
        ->update([
            'int_status' => 'Removed'
        ]);

    return back()->with('success', 'Intern removed successfully!');
}


public function updateInternActive(Request $request)
{
    // Validate request
    $request->validate([
        'id' => 'required|exists:intern_accounts,int_id',
        'status' => 'required|string',
        'review' => 'nullable|string'
    ]);

    // Update intern
    DB::table('intern_accounts')
        ->where('int_id', $request->id)
        ->update([
            'int_status' => $request->status,
            'review' => $request->review,
        ]);

    return redirect()->back()->with('success', 'Intern updated successfully!');
}



public function exportActiveInterns(Request $request)
{
    $columns = ['ID', 'ETI ID', 'Name', 'Email', 'Phone', 'Technology', 'Status', 'Review'];
    $filename = "active_interns.csv";

    $callback = function() use ($columns) {
        $handle = fopen('php://output', 'w');
        // Add CSV header
        fputcsv($handle, $columns);

        // Use cursor() to stream data row by row
        InternAccount::where('int_status', 'Active')
            ->select('int_id', 'eti_id', 'name', 'email', 'phone', 'int_technology', 'int_status', 'review')
            ->orderBy('int_id', 'asc')
            ->cursor() // ⚡ streams instead of loading all at once
            ->each(function($intern) use ($handle) {
                fputcsv($handle, [
                    $intern->int_id,
                    $intern->eti_id,
                    $intern->name,
                    $intern->email,
                    $intern->phone,
                    $intern->int_technology,
                    $intern->int_status,
                    $intern->review,
                ]);
            });

        fclose($handle);
    };

    return Response::stream($callback, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename={$filename}",
    ]);
}


     
//     public function active(Request $request)
// {
//     $manager = auth()->guard('manager')->user();
//     if (!$manager) return redirect()->route('login');

//     // 1. Fetching Manager Permissions
//     // English comments: Get technologies and types this manager is allowed to oversee
//     $allowedTechs = DB::table('manager_permissions')
//         ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
//         ->where('manager_permissions.manager_id', $manager->manager_id)
//         ->where('technologies.status', 1) 
//         ->select('technologies.technology', 'manager_permissions.interview_type')
//         ->get();

//     $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
//     $allowedInternTypes = $allowedTechs->pluck('interview_type')
//         ->map(fn($type) => strtolower(trim($type)))
//         ->unique()
//         ->toArray();

//     // 2. Base Query for 'Active' Status
//     $query = DB::table('intern_table')->where('status', 'Active');

//     // 3. Security Filter
//     $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
//         $q->whereIn('technology', $allowedTechNames)
//           ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
//     });

//     // 4. Search & Dropdown Filters
//     if ($request->filled('search')) {
//         $query->where(function($q) use ($request) {
//             $q->where('name', 'LIKE', "%{$request->search}%")
//               ->orWhere('email', 'LIKE', "%{$request->search}%");
//         });
//     }

//     if ($request->filled('status')) {
//         $query->where('technology', str_replace('-', ' ', $request->status));
//     }

//     if ($request->filled('intern_type')) {
//     $query->where('intern_type', $request->intern_type);
// }

//     // 5. Pagination Logic
//     $pageLimitSet = AdminSetting::first();
//     $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
//     $perPage = $request->input('per_page', $defaultLimit);

//     $interns = $query->orderBy('id', 'desc')
//                      ->paginate($perPage)
//                      ->withQueryString();

//     return view('pages.manager.all-interns.managerActiveInterns', compact('interns', 'allowedTechNames', 'perPage'));
// }



public function exportActiveInternsCSV(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Re-using Security Permissions
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')->map(fn($t) => strtolower(trim($t)))->unique()->toArray();

    // 2. Base Query (Status: Completed)
    $query = DB::table('intern_table')->where('status', 'Active');

    // Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 3. Apply Current Filters (Search, Tech, Intern Type)
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'LIKE', "%{$request->search}%")
              ->orWhere('email', 'LIKE', "%{$request->search}%");
        });
    }
    if ($request->filled('status')) {
        $query->where('technology', str_replace('-', ' ', $request->status));
    }
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $data = $query->orderBy('id', 'desc')->get();

    // 4. CSV Generation
    $fileName = 'active_interns_export_' . date('Y-m-d') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Internship Type', 'Technology', 'Join Date',  'Status'];

    $callback = function() use($data, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $row) {
            fputcsv($file, [
                $row->name,
                $row->email,
                $row->phone ?? 'N/A',
                $row->city,
                $row->intern_type,
                $row->technology,
                $row->join_date,
                $row->status
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
public function remove($id)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Fetch Intern
    $intern = DB::table('intern_table')->where('id', $id)->first();

    if (!$intern) {
        return redirect()->back()->with('error', 'Intern not found.');
    }


    // 3. Update Status to 'Removed' (or any status you use to hide them)
    DB::table('intern_table')->where('id', $id)->update([
        'status' => 'Removed',
    ]);

    return redirect()->back()->with('success', 'Intern removed successfully.');
}


public function updateStatus(Request $request)
{
    $request->validate([
        'id' => 'required|exists:intern_table,id',
        'status' => 'required|in:Active,Interview,Contact,Test,Completed,Removed'
    ]);

    try {
        // English comments: Use the Model directly
        $intern = \App\Models\Intern::findOrFail($request->id);
        
        // ✅ VALIDATION - Check if changing from Test to Active
        if ($intern->status == 'Test' && $request->status == 'Active') {
            // Check if invoice exists and is approved
            $invoice = \App\Models\invoice::where('intern_email', $intern->email)
                ->where('approval_status', 'approved')
                ->first();
            
            if (!$invoice) {
                return redirect()->back()->withErrors([
                    'error' => 'Cannot activate intern. Please create and approve invoice first.'
                ]);
            }
        }
        
        // Existing code - Update status
        $intern->status = $request->status;
        $intern->save();

        return redirect()->back()->with('success', 'Status updated successfully!');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
    }
}

    // English comments: Remove or Soft Delete the intern
    public function removeIntern($id)
    {
        try {
            $intern = \App\Models\Intern::findOrFail($id);
            
            // English comments: You can either delete or change status to 'Removed'
            $intern->status = 'Removed';
            $intern->save();

            return redirect()->back()->with('success', 'Intern has been removed.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to remove intern.']);
        }
    }
}
