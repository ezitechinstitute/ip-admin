<?php

namespace App\Http\Controllers;
use App\Models\AdminSetting;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KnowledgeBaseController extends Controller
{

public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // 🔎 Start Query
    $query = KnowledgeBase::query();

    // 🔍 Search by Title (Optimized for Large Datasets)
    if ($request->filled('search')) {
        $search = $request->search;
        // English: Using prefix search ("search%") is significantly faster than double wildcards ("%search%")
        $query->where('title', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    $status = $request->status;
    if ($request->filled('status')) {
        $query->where('status', strtolower($status));
    }

    // 📅 Latest Records (Optimized Sorting)
    /* English: latest() defaults to created_at. Sorting by primary key (id) 
       is much faster on 100k+ rows because it's always indexed.
    */
    $knowledge = $query->latest('id')->paginate($perPage)->withQueryString();

    return view(
        'pages.admin.knowledgeBase.knowledgeBase',
        compact('knowledge', 'perPage', 'status')
    );
}

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required',
        'category' => 'required',
        'content' => 'required',
        'visibility' => 'required|array',
    ]);

    KnowledgeBase::create([
        'title' => $request->title,
        'category' => $request->category,
        'content' => $request->content,
        'visibility' => $request->visibility,
        'status' => $request->status,
    ]);

    return back()->with('success', 'Knowledge created successfully.');
}
public function update(Request $request, $id)
{
    $request->validate([
        'title' => 'required',
        'category' => 'required',
        'content' => 'required',
        'visibility' => 'required|array',
    ]);

    $knowledge = KnowledgeBase::findOrFail($id);

    $knowledge->update([
        'title' => $request->title,
        'category' => $request->category,
        'content' => $request->content,
        'visibility' => $request->visibility,
        'status' => $request->status,
    ]);

    return back()->with('success', 'Knowledge updated successfully.');
}
public function destroy($id)
{
    KnowledgeBase::findOrFail($id)->delete();
    return back()->with('success', 'Deleted successfully.');
}


    public function downloadKnowledgeBaseCSV()
{
    // English: Setting high limits for processing large content and datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Base Query with efficient sorting
    // English: Order by ID is faster for large table scans
    $query = KnowledgeBase::latest('id');

    $fileName = 'knowledge_base_export_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['ID', 'Title', 'Category', 'Content', 'Visibility', 'Status', 'Created At'];

    // English: Using streamDownload to handle 100k+ records and large text blobs
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Crucial to prevent "Invalid Response" by clearing existing output buffers
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility (Fixes encoding issues with special characters)
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to loop through huge tables without 
           loading everything into RAM at once.
        */
        foreach ($query->cursor() as $kb) {
            // Handle visibility array (convert to comma separated string)
            $visibility = is_array($kb->visibility) 
                ? implode(', ', $kb->visibility) 
                : $kb->visibility;

            // English: strip_tags is recommended if content contains HTML
            $cleanContent = strip_tags($kb->content);

            fputcsv($file, [
                $kb->id,
                $kb->title,
                $kb->category,
                $cleanContent, 
                $visibility,
                ucfirst($kb->status),
                $kb->created_at ? $kb->created_at->format('Y-m-d H:i') : ''
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}

}
