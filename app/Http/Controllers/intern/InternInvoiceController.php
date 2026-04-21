<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InternInvoiceController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get all invoices for this intern
        $invoices = DB::table('invoices')
            ->where('intern_email', $intern->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate statistics
        $stats = [
            'total' => DB::table('invoices')->where('intern_email', $intern->email)->count(),
            'paid' => 0,
            'pending' => 0,
            'overdue' => 0,
        ];
        
        foreach ($invoices as $invoice) {
            $remaining = $invoice->remaining_amount ?? $invoice->total_amount;
            if ($remaining <= 0) {
                $stats['paid']++;
            } elseif ($invoice->due_date && Carbon::parse($invoice->due_date)->isPast()) {
                $stats['overdue']++;
            } else {
                $stats['pending']++;
            }
        }
        
        return view('pages.intern.invoices.index', compact('invoices', 'stats'));
    }
    
    public function show($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $invoice = DB::table('invoices')
            ->where('id', $id)
            ->where('intern_email', $intern->email)
            ->first();
        
        if (!$invoice) {
            abort(404, 'Invoice not found');
        }
        
        return view('pages.intern.invoices.show', compact('invoice'));
    }
    public function store(Request $request)
{
    $intern = Auth::guard('intern')->user();

    if (!$intern) {
        return redirect()->route('login');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'intern_email' => 'required|email',
        'contact' => 'nullable|string|max:20',
        'invoice_type' => 'required|string',
        'total_amount' => 'required|numeric|min:0',
        'received_amount' => 'nullable|numeric|min:0',
        'due_date' => 'required|date',
    ]);

    $total = $request->total_amount;
    $paid = $request->received_amount ?? 0;

    // 🔥 HARD RULE (business logic protection)
    if ($paid > $total) {
        return back()->withErrors([
            'received_amount' => 'Paid amount cannot exceed total amount'
        ]);
    }

    $remaining = $total - $paid;

    // 🔥 BETTER ID GENERATION (don’t use rand)
    $invoiceId = 'INV-' . now()->format('YmdHis') . rand(10, 99);

    DB::table('invoices')->insert([
        'inv_id' => $invoiceId,
        'invoice_type' => $request->invoice_type,
        'name' => $request->name,
        'intern_email' => $request->intern_email,
        'contact' => $request->contact,
        'total_amount' => $total,
        'received_amount' => $paid,
        'remaining_amount' => $remaining,
        'due_date' => $request->due_date,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('intern.invoices')
        ->with('success', 'Invoice created successfully');
}
}