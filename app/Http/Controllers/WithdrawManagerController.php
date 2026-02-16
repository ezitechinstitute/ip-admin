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
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);
        $query = Withdraw::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ac_name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('req_status', $request->status);
        }

        $withdraws = $query->latest()->paginate($perPage);

        return view('pages.admin.withdraw.withdraw', compact('withdraws', 'perPage'));
    }


    public function exportWithdrawCSV(Request $request)
{
    $query = Withdraw::query();

    // Wahi filters apply karein jo index method mein hain
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('ac_name', 'like', "%{$search}%");
    }

    if ($request->filled('status')) {
        $query->where('req_status', $request->status);
    }

    $withdraws = $query->latest()->get();

    $filename = "withdraw_requests_" . date('Y-m-d_H-i-s') . ".csv";
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Bank Name', 'Account No', 'Account Holder', 'Description', 'Date', 'Amount', 'Status'];

    $callback = function() use($withdraws, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($withdraws as $row) {
            fputcsv($file, [
                $row->bank,
                $row->ac_no,
                $row->ac_name,
                $row->description,
                $row->date,
                $row->amount,
                $row->req_status == 1 ? 'Completed' : 'Pending'
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
