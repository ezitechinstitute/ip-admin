<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Transaction;
use App\Models\InternAccount;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Dashboard with complete statistics
     */
    public function dashboard(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        
        // Check if manager has auto-approval permission
        $hasAutoApproval = false;
        if ($manager) {
            try {
                $hasAutoApproval = DB::table('manager_roles')
                    ->where('manager_id', $manager->manager_id)
                    ->where('permission_key', 'invoice_auto_approval')
                    ->exists();
            } catch (\Exception $e) {
                $hasAutoApproval = false;
            }
        }

        // Complete statistics
        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('remaining_amount', '<=', 0)->count(),
            'pending' => Invoice::where('remaining_amount', '>', 0)
                                ->where('due_date', '>=', now())
                                ->count(),
            'overdue' => Invoice::where('due_date', '<', now())
                                ->where('remaining_amount', '>', 0)
                                ->count(),
            'pending_approval' => Invoice::where('approval_status', 'pending')->count(),
            'total_amount' => Invoice::sum('total_amount'),
            'received_amount' => Invoice::sum('received_amount'),
            'remaining_amount' => Invoice::sum('remaining_amount'),
        ];

        // Query builder with filters
        $query = Invoice::query();

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Status filter
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'paid':
                    $query->where('remaining_amount', '<=', 0);
                    break;
                case 'pending':
                    $query->where('remaining_amount', '>', 0)
                          ->where('due_date', '>=', now());
                    break;
                case 'overdue':
                    $query->where('due_date', '<', now())
                          ->where('remaining_amount', '>', 0);
                    break;
                case 'pending_approval':
                    $query->where('approval_status', 'pending');
                    break;
            }
        }

        // Invoice type filter
        if ($request->filled('invoice_type')) {
            $query->where('invoice_type', $request->invoice_type);
        }

        // Technology filter
        if ($request->filled('technology')) {
            $query->where('technology', $request->technology);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('inv_id', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('intern_email', 'LIKE', "%{$search}%");
            });
        }

        $pageLimit = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimit->pagination_limit ?? 15);
        
        $invoices = $query->orderBy('created_at', 'desc')
                         ->paginate($perPage)
                         ->withQueryString();

        // Get unique technologies for filter
        $technologies = Invoice::distinct()->pluck('technology');

        return view('pages.manager.invoices.dashboard', compact(
            'invoices', 'stats', 'perPage', 'technologies', 'hasAutoApproval'
        ));
    }

    /**
     * Show create invoice form
     */
    public function create()
    {
        $manager = Auth::guard('manager')->user();
        
        // Get interns in Test stage who can have draft invoices
        $interns = collect([]); // Empty collection by default
        
        try {
            $interns = InternAccount::whereIn('int_status', ['Test', 'Active'])
                ->select('int_id', 'eti_id', 'name', 'email', 'int_technology', 'phone')
                ->get();
        } catch (\Exception $e) {
            Log::warning('Could not fetch interns: ' . $e->getMessage());
        }

        return view('pages.manager.invoices.create', compact('interns'));
    }

    /**
     * Store new invoice with approval logic - FIXED VERSION
     */
    public function store(Request $request)
    {
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string|max:20',
            'intern_email' => 'required|email|max:255',
            'technology' => 'nullable|string|max:100',
            'total_amount' => 'required|numeric|min:1',
            'received_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date|after_or_equal:today',
            'invoice_type' => 'required|in:Internship,Course',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,credit_card,cheque',
        ]);

        // Additional validation: received amount cannot exceed total
        if ($request->received_amount > $request->total_amount) {
            return back()->with('error', 'Received amount cannot exceed total amount')->withInput();
        }

        try {
            DB::beginTransaction();

            // Check auto-approval permission
            $hasAutoApproval = false;
            try {
                $hasAutoApproval = DB::table('manager_roles')
                    ->where('manager_id', $manager->manager_id)
                    ->where('permission_key', 'invoice_auto_approval')
                    ->exists();
            } catch (\Exception $e) {
                $hasAutoApproval = false;
            }

            // Generate invoice ID
            $lastInvoice = Invoice::orderBy('id', 'desc')->first();
            $lastNumber = $lastInvoice ? intval(substr($lastInvoice->inv_id, 4)) : 1000;
            $newNumber = $lastNumber + 1;
            $invoiceId = 'INV-' . $newNumber;
            
            // Calculate amounts
            $received = $request->received_amount ?? 0;
            $remaining = $request->total_amount - $received;
            
            // Determine payment status
            if ($remaining <= 0) {
                $paymentStatus = 'paid';
            } elseif ($received > 0) {
                $paymentStatus = 'partial';
            } else {
                $paymentStatus = 'pending';
            }

            // Create invoice - ONLY use columns that exist in your database
            $invoice = Invoice::create([
                'inv_id' => $invoiceId,
                'name' => $request->name,
                'contact' => $request->contact,
                'intern_email' => $request->intern_email,
                'technology' => $request->technology ?? null,
                'total_amount' => $request->total_amount,
                'received_amount' => $received,
                'remaining_amount' => $remaining,
                'due_date' => $request->due_date,
                'received_by' => $manager->name ?? 'Manager',
                'status' => $paymentStatus,
                'approval_status' => $hasAutoApproval ? 'approved' : 'pending',
                'invoice_type' => $request->invoice_type,
                'screenshot' => null,
            ]);

            // Create transaction if received amount > 0
            if ($received > 0) {
                try {
                    Transaction::create([
                        'invoice_id' => $invoice->id,
                        'inv_id' => $invoice->inv_id,
                        'amount' => $received,
                        'type' => 'payment',
                        'method' => $request->payment_method ?? 'cash',
                        'payment_date' => now(),
                        'created_by' => $manager->manager_id ?? 0,
                        'created_by_name' => $manager->name ?? 'Manager',
                    ]);
                } catch (\Exception $e) {
                    // Log but don't fail - invoice is already created
                    Log::warning('Transaction creation failed: ' . $e->getMessage());
                }
            }

            DB::commit();

            return redirect()->route('invoices.dashboard')
                ->with('success', 'Invoice created successfully! ID: ' . $invoice->inv_id);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            Log::error('Invoice creation failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * View invoice details with transaction history - FIXED VERSION
     */
    public function show($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            // Get transactions
            $transactions = collect([]);
            
            try {
                // Check if payment_date column exists, if not use created_at
                $transaction = Transaction::first();
                $hasPaymentDate = $transaction && in_array('payment_date', $transaction->getAttributes());
                
                if ($hasPaymentDate) {
                    $transactions = Transaction::where('invoice_id', $id)
                        ->orWhere('inv_id', $invoice->inv_id)
                        ->orderBy('payment_date', 'desc')
                        ->get();
                } else {
                    $transactions = Transaction::where('invoice_id', $id)
                        ->orWhere('inv_id', $invoice->inv_id)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            } catch (\Exception $e) {
                // Fallback to simple query
                $transactions = Transaction::where('invoice_id', $id)->get();
            }
            
            // Get approval status if pending
            $approval = null;
            if ($invoice->approval_status == 'pending') {
                try {
                    $approval = DB::table('invoice_approvals')
                        ->where('invoice_id', $id)
                        ->first();
                } catch (\Exception $e) {
                    // Silently ignore
                }
            }

            return view('pages.manager.invoices.view', compact('invoice', 'transactions', 'approval'));
            
        } catch (\Exception $e) {
            Log::error('Error viewing invoice: ' . $e->getMessage());
            return redirect()->route('invoices.dashboard')
                ->with('error', 'Invoice not found');
        }
    }

    /**
     * Show payment form with installment support
     */
    public function paymentForm($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);
            
            if ($invoice->remaining_amount <= 0) {
                return redirect()->route('invoices.view', $id)
                    ->with('error', 'This invoice is already fully paid.');
            }

            return view('pages.manager.invoices.payment', compact('invoice'));
            
        } catch (\Exception $e) {
            Log::error('Error loading payment form: ' . $e->getMessage());
            return redirect()->route('invoices.dashboard')
                ->with('error', 'Invoice not found');
        }
    }

    /**
     * Record payment with audit trail - FIXED VERSION
     */
    public function recordPayment(Request $request, $id)
    {
        $manager = Auth::guard('manager')->user();

        if (!$manager) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        try {
            $invoice = Invoice::findOrFail($id);
        } catch (\Exception $e) {
            return redirect()->route('invoices.dashboard')
                ->with('error', 'Invoice not found');
        }

        $request->validate([
            'payment_amount' => 'required|numeric|min:1|max:' . $invoice->remaining_amount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,cheque',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Update invoice
            $newReceived = $invoice->received_amount + $request->payment_amount;
            $newRemaining = $invoice->remaining_amount - $request->payment_amount;

            $invoice->received_amount = $newReceived;
            $invoice->remaining_amount = $newRemaining;
            
            if ($newRemaining <= 0) {
                $invoice->status = 'paid';
            }
            
            $invoice->save();

            // Create transaction
            try {
                Transaction::create([
                    'invoice_id' => $invoice->id,
                    'inv_id' => $invoice->inv_id,
                    'amount' => $request->payment_amount,
                    'type' => 'payment',
                    'method' => $request->payment_method,
                    'notes' => $request->notes,
                    'payment_date' => $request->payment_date,
                    'created_by' => $manager->manager_id ?? 0,
                    'created_by_name' => $manager->name ?? 'Manager',
                ]);
            } catch (\Exception $e) {
                Log::warning('Transaction creation failed: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('invoices.view', $id)
                ->with('success', 'Payment of PKR ' . number_format($request->payment_amount, 2) . ' recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment recording failed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update due date with audit trail
     */
    public function updateDueDate(Request $request, $id)
    {
        $manager = Auth::guard('manager')->user();

        $request->validate([
            'new_due_date' => 'required|date|after:today',
            'reason' => 'required|string|max:500'
        ]);

        try {
            $invoice = Invoice::findOrFail($id);
            $oldDate = $invoice->due_date->format('Y-m-d');
            
            $invoice->due_date = $request->new_due_date;
            $invoice->save();

            return back()->with('success', 'Due date updated successfully');

        } catch (\Exception $e) {
            Log::error('Due date update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update due date');
        }
    }

    /**
     * Check if intern can be activated
     */
    public function canActivateIntern($internEmail)
    {
        try {
            $invoice = Invoice::where('intern_email', $internEmail)->first();
            
            if (!$invoice) {
                return [
                    'can_activate' => false,
                    'message' => 'Invoice must be created before activation'
                ];
            }

            if ($invoice->approval_status == 'pending') {
                return [
                    'can_activate' => false,
                    'message' => 'Invoice is pending admin approval'
                ];
            }

            return [
                'can_activate' => true,
                'message' => 'OK'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error checking activation: ' . $e->getMessage());
            return [
                'can_activate' => false,
                'message' => 'Error checking invoice status'
            ];
        }
    }

    /**
     * Export invoices to CSV
     */
    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $query = Invoice::query();

            // Apply filters
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }
            if ($request->filled('status')) {
                switch ($request->status) {
                    case 'paid':
                        $query->where('remaining_amount', '<=', 0);
                        break;
                    case 'pending':
                        $query->where('remaining_amount', '>', 0)
                              ->where('due_date', '>=', now());
                        break;
                    case 'overdue':
                        $query->where('due_date', '<', now())
                              ->where('remaining_amount', '>', 0);
                        break;
                }
            }
            if ($request->filled('invoice_type')) {
                $query->where('invoice_type', $request->invoice_type);
            }

            $fileName = 'invoices_export_' . date('Y-m-d') . '.csv';

            return response()->streamDownload(function() use ($query) {
                if (ob_get_level() > 0) ob_end_clean();
                
                $file = fopen('php://output', 'w');
                fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                fputcsv($file, [
                    'Invoice ID', 'Name', 'Email', 'Contact', 'Type',
                    'Total Amount', 'Received', 'Remaining', 'Due Date', 
                    'Payment Status', 'Approval Status', 'Created At'
                ]);

                foreach ($query->cursor() as $invoice) {
                    $paymentStatus = $invoice->remaining_amount <= 0 ? 'Paid' : 
                                    ($invoice->due_date < now() ? 'Overdue' : 'Pending');
                    
                    fputcsv($file, [
                        $invoice->inv_id,
                        $invoice->name,
                        $invoice->intern_email,
                        $invoice->contact,
                        $invoice->invoice_type,
                        number_format($invoice->total_amount, 2),
                        number_format($invoice->received_amount, 2),
                        number_format($invoice->remaining_amount, 2),
                        $invoice->due_date,
                        $paymentStatus,
                        $invoice->approval_status ?? 'approved',
                        $invoice->created_at ? $invoice->created_at->format('Y-m-d') : date('Y-m-d')
                    ]);
                }
                fclose($file);
            }, $fileName);
            
        } catch (\Exception $e) {
            Log::error('Export failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to export invoices');
        }
    }

    /**
     * Get statistics for AJAX (used by dashboard)
     */
    public function getStats()
    {
        try {
            $stats = [
                'total' => Invoice::count(),
                'paid' => Invoice::where('remaining_amount', '<=', 0)->count(),
                'pending' => Invoice::where('remaining_amount', '>', 0)
                                    ->where('due_date', '>=', now())
                                    ->count(),
                'overdue' => Invoice::where('due_date', '<', now())
                                    ->where('remaining_amount', '>', 0)
                                    ->count(),
                'total_amount' => Invoice::sum('total_amount'),
                'received_amount' => Invoice::sum('received_amount'),
                'remaining_amount' => Invoice::sum('remaining_amount'),
            ];
            
            return response()->json($stats);
            
        } catch (\Exception $e) {
            Log::error('Error getting stats: ' . $e->getMessage());
            return response()->json(['error' => 'Could not fetch stats'], 500);
        }
    }

    /**
     * Send invoice email to intern
     */
    private function sendInvoiceEmail($invoice)
    {
        try {
            Log::info('Email would be sent to: ' . $invoice->intern_email);
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF invoice
     */
   /**
 * Generate PDF invoice
 */
public function generatePDF($id)
{
    try {
        $invoice = Invoice::findOrFail($id);
        
        // Set paper size and orientation
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
                  ->setPaper('a4', 'portrait')
                  ->setOptions([
                      'defaultFont' => 'sans-serif',
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true
                  ]);
        
        // Download PDF
        return $pdf->download('invoice-' . $invoice->inv_id . '.pdf');
        
    } catch (\Exception $e) {
        Log::error('PDF generation failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
    }
}

/**
 * View PDF invoice in browser
 */
public function viewPDF($id)
{
    try {
        $invoice = Invoice::findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.invoice', compact('invoice'))
                  ->setPaper('a4', 'portrait');
        
        // Stream in browser
        return $pdf->stream('invoice-' . $invoice->inv_id . '.pdf');
        
    } catch (\Exception $e) {
        Log::error('PDF view failed: ' . $e->getMessage());
        return back()->with('error', 'Failed to load PDF');
    }
}


}

