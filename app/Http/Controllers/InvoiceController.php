<?php

namespace App\Http\Controllers;
use App\Models\Intern;
use App\Services\Invoices\InvoiceApprovalService;
use App\Models\AdminSetting;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function invoice(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Invoice::query();

    // 🔍 Search (Grouping orWhere is important!)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "{$search}%")
              ->orWhere('inv_id', 'like', "{$search}%");
        });
    }

    // 🔘 Status Mapping (0 = Pending, 1 = Approved)
    $status = $request->status ?? ''; 
    if ($request->filled('status')) {
        if (strtolower($status) === 'pending') {
            $query->where('status', 0);
        } elseif (strtolower($status) === 'approved') {
            $query->where('status', 1);
        } else {
            $query->where('status', $status);
        }
    }

    // 🔘 Invoice Type Filter
    if ($request->filled('invoice_type')) {
        $query->where('invoice_type', $request->invoice_type);
    }

    // 💰 Calculate Totals AFTER all filters are applied
    // English: Cloning the query ensures we get totals for the filtered results only
    $sumQuery = clone $query; 
    $totalAmount = $sumQuery->sum('total_amount');
    $receivedAmount = $sumQuery->sum('received_amount');
    $remainingAmount = $sumQuery->sum('remaining_amount');

    
    // 📄 Pagination & Sorting
    // English: latest('id') is faster than latest() without arguments
    $invoice = $query->latest('id')->paginate($perPage)->withQueryString();

    return view('pages.admin.invoice.invoice', compact(
        'invoice', 'perPage', 'status', 'totalAmount', 'receivedAmount', 'remainingAmount'
    ));
}

    public function exportInvoiceCSV(Request $request)
{
    $fileName = 'invoices_export_' . date('Y-m-d_H-i-s') . '.csv';

    $query = Invoice::query();

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
    




public function store(Request $request)
{
    // Handle both form data and JSON
    $data = $request->isJson() ? $request->json()->all() : $request->all();
    
    $total = $data['total_amount'];
    $paid = $data['received_amount'] ?? 0;
    
    if($paid > $total){
        if($request->isJson()) {
            return response()->json(['success' => false, 'message' => 'Received amount cannot exceed total amount']);
        }
        return back()->with('error','Received amount cannot exceed total amount');
    }
    
    $remaining = $total - $paid;
    
    if($paid == 0){
        $status = 'pending';
    }
    elseif($remaining == 0){
        $status = 'paid';
    }
    else{
        $status = 'partial';
    }
    
    $invoice = Invoice::create([
        'inv_id' => $data['inv_id'],
        'name' => $data['name'],
        'contact' => $data['contact'] ?? null,
        'intern_email' => $data['intern_email'],
        'total_amount' => $total,
        'received_amount' => $paid,
        'remaining_amount' => $remaining,
        'due_date' => $remaining > 0 ? $data['due_date'] : null,
        'received_by' => auth()->user()->name ?? 'Admin',
        'invoice_type' => $data['invoice_type'] ?? 'Internship',
        'status' => $status,
        'approval_status' => 'approved',
        'created_by_role' => 'admin',
    ]);
    
    if($request->isJson()) {
        return response()->json(['success' => true, 'invoice' => $invoice]);
    }
    
    return redirect()->back()->with('success', 'Invoice Created Successfully');
}

public function addPayment(Request $request)
{
    try {
        // Get invoice_id from request body (not from URL)
        $invoice = Invoice::findOrFail($request->invoice_id);
        $amount = $request->amount;
        
        if($amount > $invoice->remaining_amount){
            return response()->json(['success' => false, 'message' => 'Amount exceeds remaining balance']);
        }
        
        $invoice->received_amount += $amount;
        $invoice->remaining_amount -= $amount;
        
        if($invoice->remaining_amount == 0){
            $invoice->status = 'paid';
            $invoice->due_date = null;
        } else {
            $invoice->status = 'partial';
        }
        
        $invoice->save();
        
        return response()->json(['success' => true, 'message' => 'Payment added successfully']);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


    /**
 * Show create invoice form with pre-filled intern data
 * Called from Intern Profile page when clicking "Create Invoice" button
 */
// public function createFromProfile(Request $request)
// {
//     $internEmail = $request->query('email');
//     $internName = $request->query('name');
    
//     $lastInvoice = \App\Models\Invoice::latest('id')->first();
//     $newInvId = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);
    
//     return view('pages.admin.invoice.create-from-profile', compact('internEmail', 'internName', 'newInvId'));
// }


public function createFromProfile(Request $request)
{
    $internEmail = $request->query('email');
    $internName = $request->query('name');
    $internPhone = $request->query('phone', '');
    $internTechnology = $request->query('technology', '');
    
    $lastInvoice = \App\Models\Invoice::latest('id')->first();
    $newInvId = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);
    
    return view('pages.admin.invoice.create-from-profile', compact('internEmail', 'internName', 'newInvId', 'internPhone', 'internTechnology'));
}


/**
 * Update invoice
 */
public function updateInvoice(Request $request, $id)
{
    try {
        $invoice = \App\Models\Invoice::findOrFail($id);
        
        $total = $request->total_amount;
        $paid = $request->received_amount;
        
        if ($paid > $total) {
            return response()->json(['success' => false, 'message' => 'Received amount cannot exceed total'], 422);
        }
        
        $remaining = $total - $paid;
        
        if ($paid == 0) {
            $status = 'unpaid';
        } elseif ($remaining == 0) {
            $status = 'paid';
        } else {
            $status = 'partial';
        }
        
        $invoice->update([
            'total_amount' => $total,
            'received_amount' => $paid,
            'remaining_amount' => $remaining,
            'due_date' => $remaining > 0 ? $request->due_date : null,
            'status' => $status
        ]);
        
        return response()->json(['success' => true, 'message' => 'Invoice updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

/**
 * Delete invoice
 */
public function deleteInvoice($id)
{
    try {
        $invoice = \App\Models\Invoice::findOrFail($id);
        $invoice->delete();
        
        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}


/**
 * Create invoice from package selection (AJAX)
 */
public function createFromPackage(Request $request)
{
    try {
        $request->validate([
            'intern_email' => 'required|email',
            'intern_name' => 'required|string',
            'intern_phone' => 'nullable|string',
            'intern_technology' => 'nullable|string',
            'package_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'due_days' => 'required|integer|min:1'
        ]);

        $dueDate = now()->addDays($request->due_days);
        $invoiceId = 'INV-' . time() . rand(10, 99);
        
        $invoice = Invoice::create([
            'inv_id' => $invoiceId,
            'name' => $request->intern_name,
            'contact' => $request->intern_phone,
            'intern_email' => $request->intern_email,
            'technology' => $request->intern_technology,
            'total_amount' => $request->amount,
            'received_amount' => 0,
            'remaining_amount' => $request->amount,
            'due_date' => $dueDate->format('Y-m-d'),
            'received_by' => auth()->user()->name ?? 'Admin',
            'invoice_type' => 'Internship',
            'status' => 'pending',
            'approval_status' => 'approved',
            'created_by_role' => 'admin'
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Invoice created successfully',
            'invoice' => $invoice,
            'amount' => $request->amount
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Package invoice creation failed: ' . $e->getMessage());
        return response()->json([
            'success' => false, 
            'message' => 'Failed to create invoice: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Print invoice view
 */
public function printInvoice($id)
{
    $invoice = Invoice::findOrFail($id);
    return view('pages.admin.invoice.print', compact('invoice'));
}


}
