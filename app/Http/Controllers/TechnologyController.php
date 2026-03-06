<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Technologies;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TechnologyController extends Controller
{
    public function technologyData(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('perPage', $pageLimitSet->pagination_limit ?? 15);

    $query = Technologies::query();

    // 🔍 Search (Prefix search for better index usage)
    if ($request->filled('search')) {
        $search = $request->search;
        // English: Using prefix search to utilize B-Tree indexing on 'technology' column
        $query->where('technology', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 📄 Efficient Sorting & Pagination
    /* English: Fixed column name to tech_id to match your database schema.
       Sorting by primary key is the fastest way to handle 100k+ records.
    */
    $allTechnologies = $query->latest('tech_id')->paginate($perPage)->withQueryString();

    return view('pages.admin.technology.technology', compact('allTechnologies', 'perPage'));
}

    public function addTechnology(Request $request){
        $request->validate(['technology'=> 'required|unique:technologies,technology', 'status'=>'required|in:0,1']);

        Technologies::create([
            'technology' => $request->technology,
            'status' => $request->status
        ]);


        return back()->with('success', 'Technology added successfully!');
    }
    public function editTechnology(Request $request)
{
    $request->validate([
        'id' => 'required|exists:technologies,tech_id',
        'technology' => 'required|unique:technologies,technology,' . $request->id . ',tech_id',
        'status' => 'required|in:0,1',
    ]);

    Technologies::where('tech_id', $request->id)->update([
        'technology' => $request->technology,
        'status' => $request->status,
    ]);

    return back()->with('success', 'Technology updated successfully!');
}

    public function activeTechnologies(Request $request){
        $technologiesActive = Technologies::where('status', 1)->get();
        return response()->json([
        'success' => true,
        'data' => $technologiesActive
    ]);
    }


public function downloadTechnologiesCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $query = Technologies::query();

    // 🔍 Apply the same filters (Prefix optimized)
    if ($request->filled('search')) {
        $search = $request->search;
        // English: Prefix search is faster for 100k+ rows
        $query->where('technology', 'like', "{$search}%");
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $fileName = 'technologies_export_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['#ID', 'Technology Name', 'Status'];

    // English: Using streamDownload to push data directly to the browser
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear output buffer to avoid ERR_INVALID_RESPONSE or HTML injection
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper Excel character rendering
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to process 300,000+ rows by only 
           keeping ONE record in memory at a time. Fixed to tech_id.
        */
        foreach ($query->latest('tech_id')->cursor() as $tech) {
            fputcsv($file, [
                $tech->tech_id,
                $tech->technology,
                $tech->status == 1 ? 'Active' : 'Freeze'
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}
}
