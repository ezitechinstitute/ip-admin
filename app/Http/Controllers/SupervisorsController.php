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
    public function index(Request $request){
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
    $query->where('loginas', 'Supervisor');
    //get latest record
    $query->latest();
    
    $allSupervisors = $query->paginate($perPage)->withQueryString();

        return view('pages.admin.supervisor.supervisor', compact('allSupervisors', 'perPage'));
    }

    public function addSupervisor(Request $request)
{
    $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|unique:manager_accounts,email',
        'password'  => 'required|min:6',
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
        'password'   => 'nullable|min:6',
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
    // Fetch only supervisors
    $supervisors = \App\Models\ManagersAccount::where('loginas', 'Supervisor')
        ->latest()
        ->get();

    $fileName = 'supervisors_list_' . now()->format('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI-ID', 'Name', 'Email', 'Phone', 'Join Date', 'Commission', 'Department', 'Emergency Contact', 'Status'];

    $callback = function() use($supervisors, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($supervisors as $user) {
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
    };

    return response()->stream($callback, 200, $headers);
}
}
