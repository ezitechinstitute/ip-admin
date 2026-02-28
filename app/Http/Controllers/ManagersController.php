<?php

namespace App\Http\Controllers;

use App\Mail\ManagerSetPasswordMail;
use App\Models\AdminSetting;
use App\Models\ManagerPermission;
use App\Models\ManagerRole;
use App\Models\ManagersAccount;
use App\Models\Technologies;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

    $jsonPath = base_path('resources/menu/managerRolePrivileges.json');
    $privilegeGroups = [];

    if (file_exists($jsonPath)) {
        $jsonData = json_decode(file_get_contents($jsonPath), true);
        $privilegeGroups = $jsonData['privileges'] ?? [];
    }
    // ----------------------------------------
        return view('pages.admin.manager.manager', compact('allManagers', 'perPage', 'privilegeGroups'));
    }

   public function addManager(Request $request)
{
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email',
        // 'password'  => 'required|min:6', 
        // 'join_date' => 'required|date',
    ]);

    // Generate ETI ID
    do {
        $number = rand(100, 999);
        $etiId = 'ETI-MANAGER-' . $number;
    } while (ManagersAccount::where('eti_id', $etiId)->exists());

 
    $manager = ManagersAccount::create([
        'eti_id'     => $etiId,
        'image'      => '',
        'name'       => $request->name,
        'email'      => $request->email,
        'password'   => $request->password ?? '', 
        'contact'    => $request->contact ?? '',
        'join_date'  => $request->join_date ?? '',
        'loginas'    => $request->manager == 'on' ? 'Manager' : '',
        'status'     => $request->status,
        'department' => $request->department ?? '',
        'comission'  => $request->comission,
        'emergency_contact' => $request->emergency_contact ?? 0,
    ]);

    
    if ($request->has('permissions') && is_array($request->permissions)) {
        $permissionsData = [];
        foreach ($request->permissions as $key) {
            $permissionsData[] = [
                'manager_id'     => $manager->manager_id,
                'permission_key' => $key,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        
        ManagerRole::insert($permissionsData);
    }

    Mail::to($manager->email)->send(new ManagerSetPasswordMail([
    'name' => $manager->name,
    'email' => $manager->email,
    // 'passwordSetUrl' => route('manager.password.set')
]));
    return back()->with('success', 'Manager and added successfully!');
}


public function getManagerRoles($id)
{
    $permissions = ManagerRole::where('manager_id', $id)
                    ->pluck('permission_key'); 
                    
    return response()->json($permissions);
}
    public function update(Request $request, $id)
{
    // 1. Validation
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email,' . $id . ',manager_id',
        // 'join_date' => 'required|date',
        'comission' => 'required|numeric',
        'status'    => 'required|in:0,1',
        // 'contact' => 'required', 
    ]);

    // 2. Find Manager
    $manager = ManagersAccount::where('manager_id', $id)->firstOrFail();

    // 3. Update Basic Info
    $manager->name      = $request->name;
    $manager->email     = $request->email;
    // $manager->join_date = $request->join_date;
    $manager->comission = $request->comission;
    $manager->status    = $request->status;
    
    // Password update logic commented as per request
    /*
    if ($request->filled('password')) {
        $manager->password = $request->password; 
    }
    */

    $manager->save();

    // 4. Update Permissions (Privileges)
    ManagerRole::where('manager_id', $id)->delete();

    if ($request->has('permissions')) {
        $newPermissions = [];
        foreach ($request->permissions as $key) {
            $newPermissions[] = [
                'manager_id'     => $id,
                'permission_key' => $key,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        // Bulk insert for better performance
        ManagerRole::insert($newPermissions);
    }

    return redirect()->back()->with('success', 'Manager and privileges updated successfully!');
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
