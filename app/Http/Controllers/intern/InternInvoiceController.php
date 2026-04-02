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
}