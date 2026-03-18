<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardManagerController extends Controller
{
    public function index()
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        // 1️⃣ Fetch manager permissions (tech + interview type)
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

        // 2️⃣ Base query (all allowed interns for this manager)
        $query = DB::table('intern_table')
            ->whereIn('status', ['Interview', 'Test', 'Contact', 'Completed', 'Active'])
            ->where(function($q) use ($allowedTechNames, $allowedInternTypes) {
                $q->whereIn('technology', $allowedTechNames)
                  ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes);
            });

        // 3️⃣ Get counts per status
        $statusCounts = $query->select('status', DB::raw('count(*) as total'))
                              ->groupBy('status')
                              ->pluck('total', 'status')
                              ->toArray();

        // Make sure all statuses exist
        $allStatuses = ['Interview', 'Contact', 'Test', 'Completed'];
        foreach ($allStatuses as $status) {
            if (!isset($statusCounts[$status])) $statusCounts[$status] = 0;
        }

        return view('pages.manager.dashboard.dashboard', compact('manager', 'statusCounts'));
    }
}
