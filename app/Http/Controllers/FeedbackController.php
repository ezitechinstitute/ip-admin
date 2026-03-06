<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\InternFeedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FeedbackController extends Controller
{
      

public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    // English: Explicitly selecting columns to avoid "Ambiguous column" errors on Live servers
    $query = InternFeedback::query()
        ->leftJoin('intern_accounts', 'intern_feedback.eti_id', '=', 'intern_accounts.eti_id')
        ->select([
            'intern_feedback.*', 
            'intern_accounts.name as internee_name'
        ]);

    // 🔍 Search (Optimized for 100k+ records)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            // English: Prefix search is safer and faster for large datasets
            $q->where('intern_feedback.feedback_text', 'like', "{$search}%")
              ->orWhere('intern_accounts.name', 'like', "{$search}%")
              ->orWhere('intern_feedback.eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        // English: Ensure table prefix is used to avoid 'Column status is ambiguous' error
        $status = ucfirst(strtolower($request->status)); 
        $query->where('intern_feedback.status', $status);
    }

    // 📅 Latest Records
    // English: Always specify the table name in orderBy when using Joins
    $query->orderBy('intern_feedback.created_at', 'desc');

    // 📄 Pagination
    $feedback = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.feedback.feedBack', compact('feedback', 'perPage'));
}
public function resolve($id)
{
    $feedback = InternFeedback::findOrFail($id);

    // Update status to 'Resolved' and resolved_at to now
    $feedback->status = 'Resolved';
    $feedback->resolved_at = Carbon::now();
    $feedback->save();

    return redirect()->back()->with('success', 'Feedback marked as resolved.');
}


    public function exportFeedbackCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large feedback datasets
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    // 1. Base Query with Table Prefixes
    $query = InternFeedback::query()
        ->leftJoin('intern_accounts', 'intern_feedback.eti_id', '=', 'intern_accounts.eti_id')
        ->select([
            'intern_feedback.*', 
            'intern_accounts.name as internee_name'
        ]);

    // 🔍 Search Filters (Prefix optimized)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('intern_feedback.feedback_text', 'like', "{$search}%")
              ->orWhere('intern_accounts.name', 'like', "{$search}%")
              ->orWhere('intern_feedback.eti_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('intern_feedback.status', ucfirst(strtolower($request->status)));
    }

    $fileName = 'feedback_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['#ID', 'ETI-ID', 'Internee Name', 'Feedback Text', 'Status'];

    // English: Using streamDownload to push data directly to browser without RAM overload
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Crucial for Live Server - clean buffers to prevent 500 errors or corruption
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility (Fixes encoding issues)
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() fetches one row at a time. 
           Even with 300,000 feedback records, memory usage stays near 2MB.
        */
        foreach ($query->orderBy('intern_feedback.created_at', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->id,
                $row->eti_id,
                $row->internee_name ?? 'N/A',
                // English: stripping tags in case feedback contains HTML
                strip_tags($row->feedback_text), 
                $row->status
            ]);
        }

        fclose($file);
    }, $fileName, $headers);
}

}
