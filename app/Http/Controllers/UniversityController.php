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

    $query = University::withCount('interns');

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('uni_name', 'like', "%{$search}%")
              ->orWhere('uni_email', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter
    if ($request->filled('status')) {
        $query->where('uni_status', $request->status);
    }

    $query->latest('uni_id');

    $allUniversities = $query->paginate($perPage)->withQueryString();

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
    // Reuse your filtering logic to ensure the user exports what they actually see
    $query = University::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('uni_name', 'like', "%{$search}%")
              ->orWhere('uni_email', 'like', "%{$search}%");
    }

    if ($request->filled('status')) {
        $query->where('uni_status', $request->status);
    }

    $universities = $query->latest('uni_id')->get();

    $fileName = 'universities_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ETI-ID', 'University Name', 'Email', 'Phone', 'Status', 'Account Status'];

    $callback = function() use($universities, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($universities as $uni) {
            fputcsv($file, [
                $uni->uti ?? '-',
                $uni->uni_name,
                $uni->uni_email ?? '-',
                $uni->uni_phone ?? '-',
                $uni->uni_status == 1 ? 'Active' : 'Freeze',
                $uni->account_status == 1 ? 'Activated' : 'Deactivated',
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
