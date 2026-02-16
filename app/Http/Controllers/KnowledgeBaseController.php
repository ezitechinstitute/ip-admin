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

    // 🔍 Search by Title
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where('title', 'like', "%{$search}%");
    }

    // 🔘 Status Filter
    $status = $request->status;

    if (!empty($status)) {
        $query->where('status', strtolower($status));
    }

    // 📅 Latest Records
    $query->latest();

    // 📚 Pagination
    $knowledge = $query->paginate($perPage)->withQueryString();

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
    // Get records based on your current model
    $knowledge = KnowledgeBase::latest()->get();

    $fileName = 'knowledge_base_export_' . date('Y-m-d') . '.csv';
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Define columns for the CSV
    $columns = ['ID', 'Title', 'Category', 'Content', 'Visibility', 'Status', 'Created At'];

    $callback = function() use($knowledge, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($knowledge as $kb) {
            // Handle visibility array (convert to comma separated string)
            $visibility = is_array($kb->visibility) 
                ? implode(', ', $kb->visibility) 
                : $kb->visibility;

            fputcsv($file, [
                $kb->id,
                $kb->title,
                $kb->category,
                $kb->content,
                $visibility,
                ucfirst($kb->status),
                $kb->created_at->format('Y-m-d H:i')
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
