<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagerKnowledgeBaseController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect()->route('manager.login');
        }

        $query = KnowledgeBase::query();

        // Filter only articles visible to manager
        $query->whereJsonContains('visibility', 'manager');
        $query->where('status', 1);
        // Optional filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                  ->orWhere('content', 'LIKE', "%{$request->search}%");
            });
        }

        // Pagination
        $pageLimitSet = AdminSetting::first();
        $defaultLimit = $pageLimitSet->pagination_limit ?? 15;
        $perPage = $request->input('per_page', $defaultLimit);
        $articles = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        return view('pages.manager.knowledge-base.knowledgeBase', compact('articles', 'perPage'));
    }



    public function exportKnowledgeBaseCSV(Request $request)
{
    $manager = auth()->guard('manager')->user();
    if (!$manager) return abort(403);

    // 1️⃣ Base Query (Only manager visible + active)
    $query = DB::table('knowledge_bases')
        ->whereJsonContains('visibility', 'manager')
        ->where('status', 1);

    // 2️⃣ Filters (Same as index function)

    // Category Filter
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    // Status Filter (if manually passed)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Search Filter
    if ($request->filled('search')) {
        $query->where(function ($q) use ($request) {
            $q->where('title', 'LIKE', "%{$request->search}%")
              ->orWhere('content', 'LIKE', "%{$request->search}%");
        });
    }

    $data = $query->orderBy('created_at', 'desc')->get();

    // 3️⃣ CSV File Setup
    $fileName = 'manager_knowledge_base_' . now()->format('Y-m-d_His') . '.csv';

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // 4️⃣ CSV Columns
    $columns = [
        'ID',
        'Title',
        'Category',
        'Content',
        'Status',
        'Created At'
    ];

    // 5️⃣ CSV Stream Callback
    $callback = function() use($data, $columns) {

        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($data as $row) {
            fputcsv($file, [
                $row->id,
                $row->title,
                $row->category,
                strip_tags($row->content), // remove HTML
                $row->status,
                $row->created_at
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
