<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function invoice(Request $request){

    $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = invoice::query();
    

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
                     });
    }

    // 🔘 Status filter with default 'interview'
    $status = $request->status; // raw status from request
$totalAmount=$query->sum('total_amount');
    $receivedAmount=$query->sum('received_amount');
    $remainingAmount=$query->sum('remaining_amount');
   
    if(!empty($status)){
        $query->where('status', strtolower($status));
    }
    
   
     $query->latest();
    
    $invoice = $query->paginate($perPage)->withQueryString();
    
    //dd($query->sum('total_amount'));

    return view('pages.admin.invoice.invoice', compact('invoice', 'perPage', 'status', 'totalAmount','receivedAmount','remainingAmount'));
    }


    public function exportInvoiceCSV(Request $request)
{
    $fileName = 'invoices_export_' . date('Y-m-d_H-i-s') . '.csv';

    $query = invoice::query();

    // Search Filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', "%{$search}%");
    }

    // Status Filter
    if ($request->filled('status')) {
        $status = ($request->status == 'approved') ? 1 : 0;
        $query->where('status', $status);
    }

    $invoices = $query->latest()->get();

    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Invoice ID', 'Name', 'Intern Email', 'Contact', 'Due Date', 'Total Amount', 'Received Amount', 'Remaining Amount', 'Received By', 'Status'];

    $callback = function() use($invoices, $columns) {
        $file = fopen('php://output', 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
        fputcsv($file, $columns);

        foreach ($invoices as $row) {
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
    };

    return response()->stream($callback, 200, $headers);
}
    
}
