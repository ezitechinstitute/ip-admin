<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- Add this line
use App\Models\InternAccount;
use App\Models\AdminSetting;

class AllManagerInternController extends Controller
{
    public function index(){
        return view('pages.manager.all-interns.allinterns');
    }
     public function active(Request $request)
    {
        // 1. Authentication & Guard Check
        $manager = auth('manager')->user();
        if (!$manager) {
            return redirect()->route('login')->withErrors(['email' => 'Session expired.']);
        }

        $managerId = $manager->id;

        // 2. Fetch Manager Permissions
        // English comments: Get assigned technology IDs/Names for this manager
        $allowedTechs = DB::table('manager_permissions')
            ->where('manager_id', $managerId)
            ->pluck('tech_id')
            ->toArray();

        // 3. Dynamic Statistics (Filtered by Permissions)
        // English comments: Using whereIn to ensure stats only reflect manager's scope
        $stats = [
            'interview'      => InternAccount::whereIn('int_technology', $allowedTechs)->where('int_status', 'interview')->count(),
            'contacted'      => InternAccount::whereIn('int_technology', $allowedTechs)->where('int_status', 'contact')->count(),
            'test_attempt'   => InternAccount::whereIn('int_technology', $allowedTechs)->where('int_status', 'test')->count(),
            'test_completed' => InternAccount::whereIn('int_technology', $allowedTechs)->where('int_status', 'completed')->count(),
        ];

        // 4. Pagination Settings
        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

        // 5. Build Main Query
        $query = InternAccount::query()
            ->select('intern_accounts.*', 'intern_table.image as profile_image')
            ->leftJoin('intern_table', 'intern_accounts.email', '=', 'intern_table.email')
            ->where('intern_accounts.int_status', 'active');

        // 6. Apply Technology Filter (Only if permissions exist)
        // English comments: Only filter if the manager has specific assigned technologies
        if (!empty($allowedTechs)) {
            $query->whereIn('intern_accounts.int_technology', $allowedTechs);
        } else {
            // Optional: If no permissions, you might want to return 0 results
            // $query->whereRaw('1 = 0'); 
        }

        // 7. Search Functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('intern_accounts.name', 'like', "%{$search}%")
                  ->orWhere('intern_accounts.email', 'like', "%{$search}%")
                  ->orWhere('intern_accounts.eti_id', 'like', "%{$search}%");
            });
        }

        // 8. Execute Pagination
        $internAccounts = $query->orderBy('intern_accounts.int_id', 'desc')
                                ->paginate($perPage)
                                ->withQueryString();

        // 9. Debugging (Optional - Uncomment if you still get 0 results)
        // dd($allowedTechs, $internAccounts->toArray());

        return view('pages.manager.all-interns.managerActiveInterns', compact('internAccounts', 'stats', 'perPage'));
    }
}
