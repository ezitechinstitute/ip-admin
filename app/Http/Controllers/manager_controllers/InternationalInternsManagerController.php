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

        // 1. Fetch Manager Permissions
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

        // 2. Base Query (only international interns, i.e., country != Pakistan and not null)
        $query = DB::table('intern_table')
            ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
            ->where('country', '!=', 'Pakistan')
            ->whereNotNull('country');

        // 3. Security Filter: manager allowed techs & types
        $query->where(function ($q) use ($allowedTechNames, $allowedInternTypes) {
            $q->whereIn('technology', $allowedTechNames)
              ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
        });

        // 4. Filters: search, status, intern type, technology
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
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
            $query->where('technology', 'LIKE', $techFilter);
        }

        // 5. Clone query to get counts before pagination
        $countsQuery = clone $query;
        $statusCounts = $countsQuery
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Ensure all statuses exist even if 0
        $statuses = ['Interview', 'Contact', 'Test', 'Completed'];
        foreach ($statuses as $status) {
            if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
        }

        // 6. Pagination
        $pageLimitSet = AdminSetting::first();
        $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
        $perPage = $request->input('per_page', $defaultLimit);

        $interns = $query->orderBy('id', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

        return view('pages.manager.international-interns.internationalInterns', compact(
            'interns', 'allowedTechNames', 'perPage', 'statusCounts'
        ));
    }


public function exportInternationalInterns(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return redirect()->route('login');

    // 1. Allowed technologies & types
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

    // 2. Build query (exclude Pakistan)
    $query = DB::table('intern_table')
        ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
        ->where('country', '!=', 'Pakistan')
        ->whereIn('technology', $allowedTechNames)
        ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);

    // Optional filters
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'LIKE', "%{$request->search}%")
              ->orWhere('email', 'LIKE', "%{$request->search}%");
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
        $query->where('technology', 'LIKE', $techFilter);
    }

    // 3. Stream CSV
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="international_interns.csv"',
    ];

    $columns = [
        'name', 'email', 'phone', 'technology', 'status', 'country', 'intern_type', 'join_date'
    ];

    $callback = function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        // Add BOM for Excel UTF-8 support
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Column headers
        fputcsv($file, $columns);

        // Chunk query to handle large data
        $query->orderBy('id')->chunk(1000, function($interns) use ($file, $columns) {
            foreach ($interns as $intern) {
                $row = [];
                foreach ($columns as $col) {
                    $row[] = $intern->$col ?? '';
                }
                fputcsv($file, $row);
            }
        });

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
