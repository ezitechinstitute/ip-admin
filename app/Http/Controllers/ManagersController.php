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
    public function managersData(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = ManagersAccount::query();

    // 🔍 Search (Prefix search for high performance on 100k+ records)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Use prefix search to allow database indexing to work
            $q->where('name', 'like', "{$search}%")
              ->orWhere('eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // English: Filtering by Manager role
    $query->where('loginas', 'Manager');

    // 📄 Efficient Pagination
    // English: Fixed column name to manager_id as per your database schema
    $allManagers = $query->latest('manager_id')->paginate($perPage)->withQueryString();

    // 📂 JSON Privilege Loading (Optimized)
    $jsonPath = base_path('resources/menu/managerRolePrivileges.json');
    $privilegeGroups = [];

    if (file_exists($jsonPath)) {
        // English: Loading privileges once outside the loop for efficiency
        $jsonData = json_decode(file_get_contents($jsonPath), true);
        $privilegeGroups = $jsonData['privileges'] ?? [];
    }

    return view('pages.admin.manager.manager', compact('allManagers', 'perPage', 'privilegeGroups'));
}

   public function addManager(Request $request)
{
    // 1. Fetch SMTP Settings from DB
    $settings = DB::table('admin_settings')->first(); 

    // 2. Comprehensive SMTP Validation (Active check + Data Presence)
    // English: Checking if settings exist, if it's active, and if critical fields are not empty
    if (
        !$settings || 
        $settings->smtp_active_check == 0 || 
        empty($settings->smtp_host) || 
        empty($settings->smtp_email) || 
        empty($settings->smtp_password)
    ) {
        return back()
            ->withErrors(['smtp_error' => 'SMTP is either inactive or configuration is missing in database. Manager cannot be added without email capability.'])
            ->withInput();
    }

    // 3. Standard Validation
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:manager_accounts,email',
    ]);

    // 4. Generate ETI ID (Original Logic)
    do {
        $number = rand(100, 999);
        $etiId = 'ETI-MANAGER-' . $number;
    } while (ManagersAccount::where('eti_id', $etiId)->exists());

    // 5. Create Manager
    $manager = ManagersAccount::create([
        'eti_id'            => $etiId,
        'image'             => '',
        'name'              => $request->name,
        'email'             => $request->email,
        'password'          => $request->password ?? '', 
        'contact'           => $request->contact ?? '',
        'join_date'         => $request->join_date ?? '',
        'loginas'           => $request->manager == 'on' ? 'Manager' : '',
        'status'            => $request->status,
        'department'        => $request->department ?? '',
        'comission'         => $request->comission,
        'emergency_contact' => $request->emergency_contact ?? 0,
    ]);

    // 6. Permissions Logic (Original Logic)
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

    // 7. Send Email with Error Catching
    try {
        Mail::to($manager->email)->send(new ManagerSetPasswordMail([
            'name'  => $manager->name,
            'email' => $manager->email,
        ]));
    } catch (\Exception $e) {
        // English: Log the system error for debugging
        \Log::error("SMTP Error: " . $e->getMessage());
        return back()->with('success', 'Manager added, but welcome email failed. Check SMTP logs.');
    }

    return back()->with('success', 'Manager added successfully!');
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
    // English: Setting high limits for processing large datasets (100k+)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Base Query with sorting on primary key
    $query = ManagersAccount::where('loginas', 'Manager')->latest('manager_id');

    $csvFileName = 'managers_export_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI-ID', 'Name', 'Email', 'Phone', 'Join Date', 'Commission', 'Department', 'Emergency Contact', 'Status'];

    // English: Using modern streamDownload to prevent memory exhaustion
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Crucial - Clear output buffer to avoid ERR_INVALID_RESPONSE or HTML injection
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper Excel character rendering
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() fetches records one by one from DB. 
           It keeps memory usage low even for 300,000+ records.
        */
        foreach ($query->cursor() as $manager) {
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
    }, $csvFileName, $headers);
}


    public function resendEmail($id)
{
    // 1. Fetch SMTP Settings from DB (As per your addManager logic)
    $settings = DB::table('admin_settings')->first(); 

    // 2. Comprehensive SMTP Validation
    // English: Checking if settings exist and if critical fields are active/not empty
    if (
        !$settings || 
        $settings->smtp_active_check == 0 || 
        empty($settings->smtp_host) || 
        empty($settings->smtp_email) || 
        empty($settings->smtp_password)
    ) {
        return back()->with('error', 'SMTP is either inactive or configuration is missing in database.');
    }

    // 3. Find the Manager
    // English: Ensure the manager exists or return 404
    $manager = \App\Models\ManagersAccount::findOrFail($id);

    // 4. Security Check
    // English: Only resend if password is still empty
    if (!empty($manager->password)) {
        return back()->with('error', 'This manager has already set their password.');
    }

    // 5. Send Email with Error Catching
    try {
        Mail::to($manager->email)->send(new \App\Mail\ManagerSetPasswordMail([
            'name'  => $manager->name,
            'email' => $manager->email,
        ]));

        return back()->with('success', 'Invitation email has been resent to ' . $manager->email);
        
    } catch (\Exception $e) {
        // English: Log the system error for debugging
        \Log::error("SMTP Resend Error: " . $e->getMessage());
        return back()->with('error', 'Failed to send email. Please check your SMTP settings.');
    }
}

}
