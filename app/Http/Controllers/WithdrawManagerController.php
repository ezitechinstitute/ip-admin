<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Withdraw;
use Illuminate\Http\Request;

class WithdrawManagerController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('perPage', $pageLimitSet->pagination_limit ?? 15);

    $query = Withdraw::query();

    // 🔍 Search (Prefix search is much faster on large datasets)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('ac_name', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('req_status', $request->status);
    }

    // 📄 Efficient Pagination
    // English: latest() can be slow on 300k+ rows; adding 'id' ensures stable and fast sorting
    $withdraws = $query->latest('id')->paginate($perPage)->withQueryString();

    return view('pages.admin.withdraw.withdraw', compact('withdraws', 'perPage'));
}


    public function exportWithdrawCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large exports
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $query = Withdraw::query();

    // 🔍 Search Filter (Prefix optimized)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('ac_name', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('req_status', $request->status);
    }

    $filename = "withdraw_requests_" . date('Y-m-d_H-i-s') . ".csv";
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Bank Name', 'Account No', 'Account Holder', 'Description', 'Date', 'Amount', 'Status'];

    // English: Using streamDownload to push data directly to the browser
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Ensure no previous output or HTML interferes with the CSV
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper Excel formatting
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Header Row
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to process 300k+ rows by only 
           keeping one record in memory at a time.
        */
        foreach ($query->latest('req_id')->cursor() as $row) {
            // English: Mapping status based on your integer logic
            $statusText = 'Pending';
            if ($row->req_status == 1) $statusText = 'Completed';
            if ($row->req_status == 2) $statusText = 'Rejected';

            fputcsv($file, [
                $row->bank,
                $row->ac_no,
                $row->ac_name,
                $row->description,
                $row->date,
                $row->amount,
                $statusText
            ]);
        }

        fclose($file);
    }, $filename, $headers);
}
}
