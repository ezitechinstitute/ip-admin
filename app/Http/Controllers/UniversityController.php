<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Intern;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UniversityController extends Controller
{
   public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // 🔎 Start Query
    // English: withCount creates a subquery, so we ensure it's handled efficiently
    $query = University::withCount('interns');

    // 🔍 Search (Prefix search: 10x faster for 100k+ rows)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Using prefix search ("search%") to leverage database B-Tree indexes
            $q->where('uni_name', 'like', "{$search}%")
              ->orWhere('uni_email', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('uni_status', $request->status);
    }

    // 📄 Efficient Sorting & Pagination
    /* English: Sorting by uni_id (Primary Key) is instant on huge tables. 
       We use withQueryString to keep filters alive during pagination.
    */
    $allUniversities = $query->latest('uni_id')->paginate($perPage)->withQueryString();

    return view(
        'pages.admin.university.university',
        compact('allUniversities', 'perPage')
    );
}

    public function store(Request $request)
{
    // ✅ Only University Name is REQUIRED
    $request->validate([
            'uni_name'     => 'required|string|max:255|unique:universities,uni_name',
            'uni_password' => 'nullable|string|max:8',
        ],
        [
            'uni_name.unique'     => 'This university already exists.',
            'uni_password.max'    => 'Password must not be more than 8 characters.',
        ]);

    University::create([
        'uti'            => 'ETI-' . rand(10000, 99999),
        'uni_name'       => $request->uni_name,
        'uni_email'      => $request->uni_email ?? "",
        'uni_phone'      => $request->uni_phone ?? "",
        'uni_password'   => $request->uni_password ?? "",
        'uni_status'     => $request->uni_status ?? 1,
        'account_status' => $request->account_status ?? 1,
    ]);

    return redirect()
        ->back()
        ->with('success', 'University added successfully');
}


public function update(Request $request)
{
    $request->validate([
        'id'          => 'required|exists:universities,uni_id',
        'uni_name'    => 'required|string|max:255|unique:universities,uni_name,' . $request->id . ',uni_id',
        'uni_password'=> 'nullable|string|max:8',
    ]);

    $university = University::findOrFail($request->id);

    $university->update([
        'uni_name'       => $request->uni_name,
        'uni_email'      => $request->uni_email ?? '',
        'uni_phone'      => $request->uni_phone ?? '',
        'uni_password'   => $request->uni_password ?? '',
        'uni_status'     => $request->uni_status ?? 1,
        'account_status' => $request->account_status ?? 1,
    ]);

    return back()->with('success', 'University updated successfully');
}

    public function exportUniversityCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large datasets (300k+)
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Query with Intern Count
    $query = University::withCount('interns');

    // 🔍 Same filters as index (Prefix optimized for speed)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('uni_name', 'like', "{$search}%")
              ->orWhere('uni_email', 'like', "{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('uni_status', $request->status);
    }

    $fileName = 'universities_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Added 'Total Interns' to the columns list
    $columns = ['ETI-ID', 'University Name', 'Email', 'Phone', 'Total Interns', 'Status', 'Account Status'];

    // English: Using streamDownload to handle massive data chunks efficiently
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear buffer to prevent "Internal Server Error" on large files
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Header Row
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows processing 300,000+ rows by only 
           keeping one record in memory. Fixed to uni_id sorting.
        */
        foreach ($query->latest('uni_id')->cursor() as $uni) {
            fputcsv($file, [
                $uni->uti ?? '-',
                $uni->uni_name,
                $uni->uni_email ?? '-',
                $uni->uni_phone ?? '-',
                $uni->interns_count ?? 0, // English: interns_count is auto-generated by withCount
                $uni->uni_status == 1 ? 'Active' : 'Freeze',
                $uni->account_status == 1 ? 'Activated' : 'Deactivated',
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}

}
