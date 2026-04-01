<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternInvoiceController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $invoices = DB::table('invoices')
            ->where('intern_email', $intern->email)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.intern.invoices.index', compact('invoices'));
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
            abort(404);
        }
        
        return view('pages.intern.invoices.show', compact('invoice'));
    }
}