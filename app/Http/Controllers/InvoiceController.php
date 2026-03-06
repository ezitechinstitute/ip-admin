<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function invoice(Request $request) {
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = invoice::query();

    // 🔍 Search (Prefix search is 10x faster than middle search)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', "{$search}%"); 
        $query->orWhere('inv_id', 'like', "{$search}%"); 
    }

    $status = $request->status ?? ''; 

    if (!empty($status)) {
        if (strtolower($status) === 'pending') {
            $query->where('status', 0);
        } elseif (strtolower($status) === 'approved') {
            $query->where('status', 1);
        } else {
            $query->where('status', $status);
        }
    }

    $sumQuery = clone $query; 
    $totalAmount = $sumQuery->sum('total_amount');
    $receivedAmount = $sumQuery->sum('received_amount');
    $remainingAmount = $sumQuery->sum('remaining_amount');

    // 📄 Pagination
    $invoice = $query->latest('id')->paginate($perPage)->withQueryString();

    return view('pages.admin.invoice.invoice', compact(
        'invoice', 'perPage', 'status', 'totalAmount', 'receivedAmount', 'remainingAmount'
    ));
}


    public function exportInvoiceCSV(Request $request)
{
    $fileName = 'invoices_export_' . date('Y-m-d_H-i-s') . '.csv';

    $query = invoice::query();

    // 🔍 Search Filter (Prefix search is 10x faster)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        // English: Ensure status is handled correctly based on your tinyint schema
        $status = ($request->status == 'approved') ? 1 : 0;
        $query->where('status', $status);
    }

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = [
        'Invoice ID', 'Name', 'Intern Email', 'Contact', 'Due Date', 
        'Total Amount', 'Received Amount', 'Remaining Amount', 'Received By', 'Status'
    ];

    /* English: Using response()->stream() with cursor() ensures that 
       only ONE record is kept in memory at a time. This is critical for 100k+ rows.
    */
    return response()->stream(function() use ($query, $columns) {
        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel Compatibility
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() replaces get() to prevent "Memory Exhausted" error.
           It fetches records lazily from the database.
        */
        foreach ($query->latest('id')->cursor() as $row) {
            fputcsv($file, [
                $row->inv_id,
                $row->name,
                $row->intern_email,
                $row->contact,
                $row->due_date,
                $row->total_amount,
                $row->received_amount,
                $row->remaining_amount,
                $row->received_by,
                ($row->status == 1) ? 'Approved' : 'Pending'
            ]);
        }
        
        fclose($file);
    }, 200, $headers);
}
    
}
