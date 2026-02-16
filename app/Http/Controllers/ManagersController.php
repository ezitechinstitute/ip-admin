<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;

use App\Models\ManagerPermission;
use App\Models\ManagersAccount;
use App\Models\Technologies;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ManagersController extends Controller
{
    public function managersData(Request $request){

        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = ManagersAccount::query();

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('eti_id', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request

   
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    $query->where('loginas', 'Manager');
    //get latest record
    $query->latest();
    
    $allManagers = $query->paginate($perPage)->withQueryString();

        return view('pages.admin.manager.manager', compact('allManagers', 'perPage'));
    }

   public function addManager(Request $request)
{
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email',
        'password'  => 'required|min:6',
        'join_date' => 'required|date',
        'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    //  Generate ETI ID
    do {
        $number = rand(100, 999);
        $etiId = 'ETI-MANAGER-' . $number;
    } while (ManagersAccount::where('eti_id', $etiId)->exists());

    

    ManagersAccount::create([
        'eti_id'    => $etiId,
        'image'     => '',
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => $request->password,
        'contact'   => $request->contact ?? '',
        'join_date' => $request->join_date,
        'loginas'   => $request->manager == 'on' ? 'Manager' : '',
        'status'    => $request->status,
        'department'=> $request->department ?? '',
        'comission' => $request->comission,
    'emergency_contact'=> $request->emergency_contact ?? 0,
    ]);

    return back()->with('success', 'Manager added successfully!');
}

    public function update(Request $request, $id)
{
    // Validation
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email,' . $id . ',manager_id',
        'contact'   => 'required',
        'join_date' => 'required|date',
        'comission' => 'required|numeric',
        'status'    => 'required|in:0,1',
    ]);

    // Find Manager
    $manager = ManagersAccount::where('manager_id', $id)->firstOrFail();

    // Update Data
    $manager->name      = $request->name;
    $manager->email     = $request->email;
    $manager->contact   = $request->contact;
    $manager->join_date = $request->join_date;
    $manager->comission = $request->comission;
    $manager->password = $request->password;
    $manager->status    = $request->status;

    // Password Update logic (only if user typed something)
    if ($request->filled('password')) {
        $manager->password = $request->password; 
    }

    $manager->save();

    return redirect()->back()->with('success', 'Manager updated successfully!');
}

public function activeTechnologies(Request $request)
    {
        // Fetch only active technologies
        $technologiesActive = Technologies::where('status', 1)->latest()->get();
        return response()->json([
            'success' => true,
            'data' => $technologiesActive
        ]);
    }

   public function storePermissions(Request $request)
{
    $request->validate([
        'manager_id' => 'required|integer',
        'permissions' => 'nullable|array',
    ]);

    $managerId = $request->manager_id;

    try {
        DB::transaction(function () use ($managerId, $request) {
            ManagerPermission::where('manager_id', $managerId)->delete();

            if ($request->has('permissions')) {
                $insertData = [];
                foreach ($request->permissions as $techId => $interviewTypes) {
                    if ($techId === 'undefined' || empty($techId)) continue;

                    foreach ($interviewTypes as $type) {
                        $insertData[] = [
                            'manager_id'     => (int) $managerId,
                            'tech_id'        => (int) $techId,
                            'interview_type' => $type
                        ];
                    }
                }

                if (!empty($insertData)) {
                    ManagerPermission::insert($insertData);
                }
            }
        });

        // ✅ CHANGE THIS: Redirect instead of JSON
        return redirect()->back()->with('success', 'Permissions updated successfully!');
        
    } catch (\Exception $e) {
        // For errors, go back with the error message
        return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
    }
}


public function getManagerPermissions($id)
{
    $permissions = ManagerPermission::where('manager_id', $id)
        ->get()
        ->groupBy('tech_id')
        ->map(function ($rows) {
            return $rows->pluck('interview_type')->toArray();
        });

    return response()->json([
        'success' => true,
        'data' => $permissions
    ]);
}

    public function downloadManagerCSV()
{
    $managers = ManagersAccount::where('loginas', 'Manager')->latest()->get();

    $csvFileName = 'managers_export_' . date('Y-m-d') . '.csv';
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$csvFileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI-ID', 'Name', 'Email', 'Phone', 'Join Date', 'Commission', 'Department', 'Emergency Contact', 'Status'];

    $callback = function() use($managers, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($managers as $manager) {
            fputcsv($file, [
                $manager->eti_id,
                $manager->name,
                $manager->email,
                $manager->contact,
                $manager->join_date,
                $manager->comission,
                $manager->department,
                $manager->emergency_contact,
                $manager->status == 1 ? 'Active' : 'Freeze',
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
