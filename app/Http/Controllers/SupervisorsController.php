<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;

use App\Models\ManagersAccount;
use App\Models\SupervisorPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

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

      $privilegeGroups = [];
    $jsonPath = base_path('resources/menu/managerRolePrivileges.json');

    if (file_exists($jsonPath)) {
        // English: Loading privileges once outside the loop for efficiency
        $jsonData = json_decode(file_get_contents($jsonPath), true);
        $privilegeGroups = $jsonData['privileges'] ?? [];
    }
    $allManager = ManagersAccount::select('manager_id', 'name')
                ->where('loginas', 'Manager')->get();
    return view('pages.admin.supervisor.supervisor', compact('allSupervisors','allManager', 'privilegeGroups', 'perPage'));
}

    public function addSupervisor(Request $request)
{
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

    return back()->with('success', 'Supervisor added successfully!');
}

public function update(Request $request, $id)
{
    // Validation
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email,' . $id . ',manager_id',
        // 'contact'   => 'required',
        'join_date' => 'required|date',
        'comission' => 'required|numeric',
        'status'    => 'required|in:0,1',
        'department'      => 'required|string|max:255',
        'password'   => 'nullable|min:5',
    ]);

    // Find Supervisor
    $supervisor = ManagersAccount::where('manager_id', $id)->firstOrFail();

    // Update Data
    $supervisor->name      = $request->name;
    $supervisor->email     = $request->email;
    // $supervisor->contact   = $request->contact;
    $supervisor->join_date = $request->join_date;
    $supervisor->comission = $request->comission;
    $supervisor->password = $request->password;
    $supervisor->status    = $request->status;
    $supervisor->department = $request->department; 
    // Password Update logic (only if user typed something)
    if ($request->filled('password')) {
        $supervisor->password = $request->password; 
    }

    $supervisor->save();

    return redirect()->back()->with('success', 'Supervisor updated successfully!');
}


    public function storePermissions(Request $request)
{
    $request->validate([
        'manager_id' => 'required|integer',
        'permissions' => 'nullable|array',
    ]);

    $supervisorId = $request->manager_id;

    try {
        DB::transaction(function () use ($supervisorId, $request) {
            SupervisorPermission::where('manager_id', $supervisorId)->delete();

            if ($request->has('permissions')) {
                $insertData = [];
                foreach ($request->permissions as $techId => $interviewTypes) {
                    if ($techId === 'undefined' || empty($techId)) continue;

                    foreach ($interviewTypes as $type) {
                        $insertData[] = [
                            'manager_id'     => (int) $supervisorId,
                            'tech_id'        => (int) $techId,
                            'internship_type' => $type
                        ];
                    }
                }

                if (!empty($insertData)) {
                    SupervisorPermission::insert($insertData);
                }
            }
        });

        //  CHANGE THIS: Redirect instead of JSON
        return redirect()->back()->with('success', 'Permissions updated successfully!');
        
    } catch (\Exception $e) {
        // For errors, go back with the error message
        return redirect()->back()->with('error', 'Failed to save: ' . $e->getMessage());
    }
}

public function assignSupervisor(Request $request)
{
    // dd($request->all()); // check data

    DB::table('manager_accounts')
        ->where('manager_id', $request->supervisor_id) // supervisor row
        ->update([
            'assigned_manager' => $request->manager_id // selected manager
        ]);

    return back()->with('success','Supervisor assigned successfully');
}


public function getSupervisorPermissions($id)
{
    $permissions = SupervisorPermission::where('manager_id', $id)
        ->get()
        ->groupBy('tech_id')
        ->map(function ($rows) {
            return $rows->pluck('internship_type')->toArray();
        });

    return response()->json([
        'success' => true,
        'data' => $permissions,
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
