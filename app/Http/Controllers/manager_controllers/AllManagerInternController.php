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
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Fetching Manager Permissions
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

    // 2. Base Query for all statuses
    $query = DB::table('intern_table')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active']);

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

    if ($request->filled('tech')) {
        $techFilter = str_replace('-', ' ', $request->tech);
        $query->where('technology', 'LIKE', $techFilter);
    }

    // 5. Clone query to get counts before pagination
    $countsQuery = clone $query;

    $statusCounts = $countsQuery
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status') // returns: ['Interview' => 10, 'Contact' => 5, ...]
        ->toArray();

    // Ensure all keys exist even if 0
    $statuses = ['Interview', 'Contact', 'Test', 'Completed'];
    foreach ($statuses as $status) {
        if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
    }

    // 6. Pagination Logic
    $pageLimitSet = AdminSetting::first();
    $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
    $perPage = $request->input('per_page', $defaultLimit);

    $interns = $query->orderBy('id', 'desc')
                     ->paginate($perPage)
                     ->withQueryString();

    return view('pages.manager.all-interns.allInterns', compact('interns', 'allowedTechNames', 'perPage', 'statusCounts'));
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
    $manager = auth('manager')->user();
    if (!$manager) return redirect()->route('login');

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
