<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;

use App\Models\ManagersAccount;
use App\Models\SupervisorPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\SupervisorRole;
// dd('Controller hit');

class SupervisorsController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = ManagersAccount::query();

    // 🔍 Search (Prefix search: 10x faster than double wildcard %search%)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Using prefix search to utilize database B-Tree indexes
            $q->where('name', 'like', "{$search}%")
              ->orWhere('eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 👤 Role Filter
    $query->where('loginas', 'Supervisor');

    // 📄 Efficient Sorting & Pagination
    // English: Sorting by manager_id (Primary Key) is much faster than created_at on huge tables
    $allSupervisors = $query->latest('manager_id')->paginate($perPage)->withQueryString();
    // $jsonPath = base_path('resources/menu/supervisorRolePrivileges.json');
    // $privilegeFile = resource_path('menu/supervisorRolePrivileges.json');
    // if (!file_exists($privilegeFile)) {
    //     abort(500, 'Supervisor privileges file not found');
    // }

    // $privilegeGroups = json_decode(file_get_contents($privilegeFile), true)['privileges'];
    $jsonPath = base_path('resources/menu/supervisorRolePrivileges.json');
    $privilegeGroups = [];
    if (file_exists($jsonPath)) {
        $jsonData = json_decode(file_get_contents($jsonPath), true);
        $privilegeGroups = $jsonData['privileges'] ?? [];
    }

        // $privilegeGroups = [];

        // if (file_exists($jsonPath)) {
        //     $jsonData = json_decode(file_get_contents($jsonPath), true);
        //     $privilegeGroups = $jsonData['privileges'] ?? [];
        // }
        return view('pages.admin.supervisor.supervisor', compact('allSupervisors', 'perPage', 'privilegeGroups'));
        

    // return view('pages.admin.supervisor.supervisor', compact('allSupervisors', 'perPage'));
}

    public function addSupervisor(Request $request)
{
    // dd($request->permissions);
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email',
        'password'  => 'required|min:5',
        'join_date' => 'required|date',
        'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'department'      => 'required|string|max:255',
    ]);

    //  Generate ETI ID
    do {
        $number = rand(100, 999);
        $etiId = 'ETI-SUPERVISOR-' . $number;
    } while (ManagersAccount::where('eti_id', $etiId)->exists());

    

    ManagersAccount::create([
        'eti_id'    => $etiId,
        'image'     => '',
        'name'      => $request->name,
        'email'     => $request->email,
        'password'  => $request->password,
        'contact'   => $request->contact ?? '',
        'join_date' => $request->join_date,
        'loginas'   => $request->manager == 'on' ? 'Supervisor' : '',
        'status'    => $request->status,
        'department'=> $request->department ?? '',
        'comission' => $request->comission,
        'emergency_contact'=> $request->emergency_contact ?? 0,
    ]);
    // saving permission
    if ($request->has('permissions')) {

        $data = [];

        foreach ($request->permissions as $permission) {
            $data[] = [
                'supervisor_id' => $supervisor->manager_id,
                'permission_key' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        SupervisorRole::insert($data);
    }

    return back()->with('success', 'Supervisor added successfully!');
}

public function update(Request $request, $id)
{
    // Validation
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email,' . $id . ',manager_id',
        'join_date' => 'required|date',
        'comission' => 'required|numeric',
        'status'    => 'required|in:0,1',
        'department'=> 'required|string|max:255',
        'password'  => 'nullable|min:5',
    ]);

    // Find Supervisor
    $supervisor = ManagersAccount::where('manager_id', $id)->firstOrFail();

    // Update Data
    $supervisor->name      = $request->name;
    $supervisor->email     = $request->email;
    $supervisor->join_date = $request->join_date;
    $supervisor->comission = $request->comission;
    $supervisor->status    = $request->status;
    $supervisor->department = $request->department; 
    
    // Password Update logic
    if ($request->filled('password')) {
        $supervisor->password = $request->password; 
    }

    $supervisor->save();

    // ==========================================
    // 🔥 STEP 2: THIS IS WHERE YOU ADD THE LOGIC
    // ==========================================
    
    // 1. Delete old role privileges to avoid duplicates
    \App\Models\SupervisorRole::where('supervisor_id', $id)->delete();

    // 2. Insert the newly checked boxes
    if ($request->has('permissions')) {
        $data = [];
        foreach ($request->permissions as $key) {
            $data[] = [
                'supervisor_id'  => $id,
                'permission_key' => $key,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        \App\Models\SupervisorRole::insert($data);
    }

    return redirect()->back()->with('success', 'Supervisor updated successfully!');
}


//     public function storePermissions(Request $request)
// {
//     $request->validate([
//         'manager_id' => 'required|integer',
//         'permissions' => 'nullable|array',
//     ]);

//     $supervisorId = $request->manager_id;

//     try {
//         DB::transaction(function () use ($supervisorId, $request) {
//             SupervisorPermission::where('manager_id', $supervisorId)->delete();

//             if ($request->has('permissions')) {
//                 $insertData = [];
//                 foreach ($request->permissions as $techId => $interviewTypes) {
//                     if ($techId === 'undefined' || empty($techId)) continue;

//                     foreach ($interviewTypes as $type) {
//                         $insertData[] = [
//                             'manager_id'     => (int) $supervisorId,
//                             'tech_id'        => (int) $techId,
//                             'internship_type' => $type
//                         ];
//                     }
//                 }

//                 if (!empty($insertData)) {
//                     SupervisorPermission::insert($insertData);
//                 }
//             }
//         });

//         //  CHANGE THIS: Redirect instead of JSON
//         return redirect()->back()->with('success', 'Permissions updated successfully!');
        
//     } catch (\Exception $e) {
//         // For errors, go back with the error message
//         return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
//     }
// }
public function storePermissions(Request $request)
{
    // dd($request->all());
    $request->validate([
        'manager_id' => 'required|integer',
        'permissions' => 'nullable|array',
    ]);

    $supervisorId = $request->manager_id;
    
    // dd($request->manager_id, $request->permissions);
    // $supervisorId = $request->id;
    // dd($supervisorId);

    // DELETE OLD
    // SupervisorRole::where('supervisor_id', $supervisorId)->delete();
    SupervisorRole::where('supervisor_id', $supervisorId)->delete();

    // INSERT NEW
    if ($request->has('permissions')) {

        $data = [];

        foreach ($request->permissions as $permission) {
            $data[] = [
                'supervisor_id' => $supervisorId,
                'permission_key' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        SupervisorRole::insert($data);
    }

    return redirect()->back()->with('success', 'Permissions updated successfully!');
}


// public function getSupervisorPermissions($id)
// {
//     $permissions = SupervisorPermission::where('manager_id', $id)
//         ->get()
//         ->groupBy('tech_id')
//         ->map(function ($rows) {
//             return $rows->pluck('internship_type')->toArray();
//         });

//     return response()->json([
//         'success' => true,
//         'data' => $permissions,
//     ]);
// }
        public function getSupervisorPermissions($id)
        {
            // 1. Fetch Tech Permissions and format them so the JS understands
            $permissions = SupervisorPermission::where('manager_id', $id) // Note: check if this should be manager_id or supervisor_id based on your table
                ->get()
                ->groupBy('tech_id')
                ->map(function ($rows) {
                    return $rows->pluck('internship_type')->toArray();
                });

            // 2. Fetch Role Privileges (The JSON Checkboxes)
            $rolePrivileges = \App\Models\SupervisorRole::where('supervisor_id', $id)
                ->pluck('permission_key')
                ->toArray();

            // 3. Return BOTH arrays
            return response()->json([
                'success' => true,
                'data' => $permissions,          // Fix: Uses the correctly mapped tech permissions
                'role_privileges' => $rolePrivileges 
            ]);
        }



        // Add to SupervisorsController.php

public function downloadSupervisorCSV()
{
    // English: Prevent timeouts and memory exhaustion for large datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Base Query with sorting on primary key
    // English: Filtering only supervisors and ordering by primary key for speed
    $query = \App\Models\ManagersAccount::where('loginas', 'Supervisor')
                                        ->latest('manager_id');

    $fileName = 'supervisors_list_' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI-ID', 'Name', 'Email', 'Phone', 'Join Date', 'Commission', 'Department', 'Emergency Contact', 'Status'];

    // English: Using streamDownload to handle 300,000+ records without crashing the server
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear any output buffer to prevent "Invalid Response" errors
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility (Fixes special characters)
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Header Row
        fputcsv($file, $columns);

        /* 🚀 English: cursor() fetches records one-by-one. 
           This keeps RAM usage at a minimum (approx 2MB) regardless of data size.
        */
        foreach ($query->cursor() as $user) {
            fputcsv($file, [
                $user->eti_id,
                $user->name,
                $user->email,
                $user->contact,
                $user->join_date,
                $user->comission,
                $user->department,
                $user->emergency_contact,
                $user->status == 1 ? 'Active' : 'Freeze',
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}
}
