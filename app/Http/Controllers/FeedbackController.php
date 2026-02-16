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

    // Start query with correct table name
    $query = InternFeedback::query()
        ->leftJoin('intern_accounts', 'intern_feedback.eti_id', '=', 'intern_accounts.eti_id')
        ->select(
            'intern_feedback.*', 
            'intern_accounts.name as internee_name'
        );

    // 🔍 Search by feedback text OR internee name OR ETI ID
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('intern_feedback.feedback_text', 'like', "%{$search}%")
              ->orWhere('intern_accounts.name', 'like', "%{$search}%")
              ->orWhere('intern_feedback.eti_id', 'like', "%{$search}%");
        });
    }

    // 🔘 Status filter
    if ($request->filled('status')) {
        $status = ucfirst(strtolower($request->status)); // ensure 'Open'/'Resolved'
        $query->where('intern_feedback.status', $status);
    }

    // Latest first
    $query->orderBy('intern_feedback.created_at', 'desc');

    // Paginate results
    $feedback = $query->paginate($perPage)->withQueryString();

    return view('pages.admin.feedback.feedback', compact('feedback', 'perPage'));
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
    // Reuse the query logic from your index method
    $query = InternFeedback::query()
        ->leftJoin('intern_accounts', 'intern_feedback.eti_id', '=', 'intern_accounts.eti_id')
        ->select(
            'intern_feedback.*', 
            'intern_accounts.name as internee_name'
        );

    // Apply Filters
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('intern_feedback.feedback_text', 'like', "%{$search}%")
              ->orWhere('intern_accounts.name', 'like', "%{$search}%")
              ->orWhere('intern_feedback.eti_id', 'like', "%{$search}%");
        });
    }

    if ($request->filled('status')) {
        $query->where('intern_feedback.status', ucfirst(strtolower($request->status)));
    }

    $results = $query->orderBy('intern_feedback.created_at', 'desc')->get();

    $fileName = 'feedback_export_' . date('Y-m-d') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['#ID', 'ETI-ID', 'Internee Name', 'Feedback Text', 'Status'];

    $callback = function() use($results, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($results as $row) {
            fputcsv($file, [
                $row->id,
                $row->eti_id,
                $row->internee_name ?? 'N/A',
                $row->feedback_text,
                $row->status
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
