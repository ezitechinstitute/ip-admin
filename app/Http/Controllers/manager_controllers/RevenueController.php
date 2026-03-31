<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) {
            return redirect()->route('manager.login');
        }

        if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_revenue')) {
            return redirect()->route('manager.dashboard')
                             ->withErrors(['access_denied' => 'You do not have permission to access Revenue & Commission.']);
        }

        // 1) Determine which interns this manager can see (based on permissions)
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

        // 2) Total interns converted (Active + Completed)
        $totalInternsConverted = DB::table('intern_table')
            ->whereIn('status', ['Active', 'Completed'])
            ->whereIn('technology', $allowedTechNames)
            ->whereIn(DB::raw('LOWER(intern_type)'), $allowedInternTypes)
            ->count();

        // 3) Revenue generated (all time for this manager)
        $totalRevenue = (float) DB::table('transactions')
            ->where('manager_email', $manager->email)
            ->sum('amount');

        // 4) Commission info
        // Note: some setups store commission in basis points (e.g. 1000 = 10%).
        $commissionPercentage = (float) ($manager->comission ?? 0);
        if ($commissionPercentage > 100) {
            $commissionPercentage = $commissionPercentage / 100;
        }

        $commissionEarned = ($totalRevenue * $commissionPercentage) / 100;

        return view('pages.manager.revenue.index', compact(
            'totalInternsConverted',
            'totalRevenue',
            'commissionPercentage',
            'commissionEarned'
        ));
    }
}
