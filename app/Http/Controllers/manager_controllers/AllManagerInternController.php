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
    public function myInterns(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query for 'Interview' Status
    $query = DB::table('intern_table')->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active']);

    // 3. Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Search & Dropdown Filters
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'LIKE', "%{$request->search}%")
              ->orWhere('email', 'LIKE', "%{$request->search}%");
        });
    }

    

    if ($request->filled('intern_type')) {
    $query->where('intern_type', $request->intern_type);
}
    if ($request->filled('status')) {
    $query->where('status', $request->status);
}

// English comments: Filtering by Technology (Renamed to 'tech' to avoid conflict)
if ($request->filled('tech')) {
    $techFilter = str_replace('-', ' ', $request->tech);
    $query->where('technology', 'LIKE', $techFilter);
}
    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.allInterns', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportMyInternsCSV(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1. Manager Permissions (Security)
    // English comments: Fetching allowed tech and intern types for this specific manager
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')->map(fn($t) => strtolower(trim($t)))->unique()->toArray();

    // 2. Base Query
    $query = DB::table('intern_table')->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active']);

    // Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 3. Smart Filters (Solving the "Zero Data" issue)
    
    // Search Filter
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'LIKE', "%{$request->search}%")
              ->orWhere('email', 'LIKE', "%{$request->search}%");
        });
    }

    // Technology or Status Filter
    if ($request->filled('status')) {
        $val = str_replace('-', ' ', $request->status);
        $actualStatuses = ['Active', 'Interview', 'Contact', 'Test', 'Completed'];

        // English comments: If the dropdown value is a status, filter by status column. 
        // Otherwise, assume it's a technology filter.
        if (in_array($val, $actualStatuses)) {
            $query->where('status', $val);
        } else {
            $query->where('technology', 'LIKE', "%{$val}%");
        }
    }

    // Intern Type Filter (Onsite/Remote)
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    $data = $query->orderBy('id', 'desc')->get();

    // 4. CSV Header & Generation
    $fileName = 'manager_interns_report_' . now()->format('Y-m-d_His') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Name', 'Email', 'Phone', 'City', 'Type', 'Technology', 'Join Date', 'Status'];

    $callback = function() use($data, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $row) {
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
    };

    return response()->stream($callback, 200, $headers);
}


















public function newInterns(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query for 'Interview' Status
    $query = DB::table('intern_table')->where('status', 'Interview');

    // 3. Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Search & Dropdown Filters
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

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.newInterns', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportNewInternsCSV(Request $request)
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
    $query = DB::table('intern_table')->where('status', 'Interview');

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
    $fileName = 'new_interns_export_' . date('Y-m-d') . '.csv';
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













public function contactWith(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Allowed Technologies
    // English comments: Get only active technologies linked to this manager
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query
    // English comments: We start with interns having 'Contact' status
    $query = DB::table('intern_table')->where('status', 'Contact');

    // 3. Applying Security Filters (Mandatory)
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Dynamic Filters (User input from Frontend)
    
    // Search Logic: Name or Email
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('email', 'LIKE', "%{$searchTerm}%");
        });
    }

    // Technology Filter (status mapping in your HTML)
    if ($request->filled('status')) {
        // English comments: Replace hyphens back to spaces if slugs are used in frontend
        $techFilter = str_replace('-', ' ', $request->status);
        $query->where('technology', 'LIKE', $techFilter);
    }

    // Intern Type Filter (Onsite/Remote)
    if ($request->filled('intern_type')) {
        $query->where('intern_type', $request->intern_type);
    }

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString(); // English comments: Keeps filter parameters in pagination links

    return view('pages.manager.all-interns.contactWith', compact('interns', 'allowedTechNames', 'perPage'));
}

public function exportContactWith(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // English comments: Reusing the same logic to fetch allowed techs for security
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')->map(fn($t) => strtolower(trim($t)))->unique()->toArray();

    // English comments: Start the query with 'Contact' status
    $query = DB::table('intern_table')->where('status', 'Contact');

    // Security constraints
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // Apply Active Filters (Search, Tech, Type)
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

    // English comments: Generating the CSV content
    $fileName = 'contact_interns_' . date('Y-m-d') . '.csv';
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





public function test(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query for 'Test' Status
    $query = DB::table('intern_table')->where('status', 'Test');

    // 3. Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Search & Dropdown Filters
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

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.test', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportTestCSV(Request $request)
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

    // 2. Base Query (Status: Test)
    $query = DB::table('intern_table')->where('status', 'Test');

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
    $fileName = 'test_interns_export_' . date('Y-m-d') . '.csv';
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
















public function completed(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query for 'Completed' Status
    $query = DB::table('intern_table')->where('status', 'Completed');

    // 3. Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Search & Dropdown Filters
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

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.completed', compact('interns', 'allowedTechNames', 'perPage'));
}



public function exportCompletedCSV(Request $request)
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
    $query = DB::table('intern_table')->where('status', 'Completed');

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
    $fileName = 'completed_test_interns_export_' . date('Y-m-d') . '.csv';
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
















     
    public function active(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
    // English comments: Get technologies and types this manager is allowed to oversee
    $allowedTechs = DB::table('manager_permissions')
        ->join('technologies', 'manager_permissions.tech_id', '=', 'technologies.tech_id')
        ->where('manager_permissions.manager_id', $manager->manager_id)
        ->where('technologies.status', 1) 
        ->select('technologies.technology', 'manager_permissions.interview_type')
        ->get();

    $allowedTechNames = $allowedTechs->pluck('technology')->unique()->toArray();
    $allowedInternTypes = $allowedTechs->pluck('interview_type')
        ->map(fn($type) => strtolower(trim($type)))
        ->unique()
        ->toArray();

    // 2. Base Query for 'Active' Status
    $query = DB::table('intern_table')->where('status', 'Active');

    // 3. Security Filter
    $query->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
        $q->whereIn('technology', $allowedTechNames)
          ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
    });

    // 4. Search & Dropdown Filters
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

    // 5. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.managerActiveInterns', compact('interns', 'allowedTechNames', 'perPage'));
}



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
