<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 15);

    $query = Account::query();

    // 🔍 Search Filter (Prefix search is faster)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('description', 'like', "{$search}%");
    }

    // 📅 From Date Filter
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }

    // 📅 To Date Filter
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    // 💰 Optimized Totals (English: Calculate all 3 sums in ONE database hit)
    // English: Using clone() to preserve filters for both Sums and Pagination
$sums = (clone $query)->selectRaw('SUM(credit) as total_c, SUM(debit) as total_d, SUM(credit) - SUM(debit) as total_b')
                         ->first();

    $totalCredit = $sums->total_c ?? 0;
    $totalDebit = $sums->total_d ?? 0;
    $totalBalance = $sums->total_b ?? 0;

    // 🗂 Sorting & Pagination
    // English: Adding ID to latest() ensures stable sorting on large datasets
    $accounts = $query->latest('date')->latest('id')->paginate($perPage)->withQueryString();

    return view(
        'pages.admin.accounts.accounts', 
        compact('accounts', 'perPage', 'totalCredit', 'totalDebit', 'totalBalance')
    );
}


public function addTransaction(Request $request)
{
    // Validate inputs
    $request->validate([
        'date' => 'required|date',
        'operation' => 'required|in:credit,debit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
    ]);

    $amount = $request->amount;
    $credit = $request->operation === 'credit' ? $amount : 0;
    $debit = $request->operation === 'debit' ? $amount : 0;

    // Create the new transaction
    $transaction = Account::create([
        'date' => $request->date,
        'credit' => $credit,
        'debit' => $debit,
        'balance' => 0, // temporary, will recalc
        'description' => $request->description,
    ]);

    // Recalculate balances for all transactions
    $transactions = Account::orderBy('date')->orderBy('id')->get();
    $runningBalance = 0;

    foreach ($transactions as $t) {
        $runningBalance += $t->credit - $t->debit;
        $t->balance = $runningBalance;
        $t->save();
    }

    return redirect()->back()->with('success', 'Transaction added successfully!');
}



public function updateTransaction(Request $request, $id)
{
    // Validate input
    $request->validate([
        'date' => 'required|date',
        'operation' => 'required|in:credit,debit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'required|string|max:255',
    ]);

    // Find the transaction
    $transaction = Account::findOrFail($id);

    $amount = $request->amount;
    $transaction->credit = $request->operation === 'credit' ? $amount : 0;
    $transaction->debit = $request->operation === 'debit' ? $amount : 0;
    $transaction->date = $request->date;
    $transaction->description = $request->description;
    $transaction->save();

    // Recalculate balances for all transactions
    $transactions = Account::orderBy('date')->orderBy('id')->get();
    $runningBalance = 0;

    foreach ($transactions as $t) {
        $runningBalance += $t->credit - $t->debit;
        $t->balance = $runningBalance;
        $t->save();
    }

    return redirect()->back()->with('success', 'Transaction updated successfully!');
}

    public function exportAccountsCSV(Request $request)
{
    // English: Setting high limits for large dataset processing
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $query = Account::query();

    // 🔍 Same Filters as Index (Optimized Search)
    if ($request->filled('search')) {
        $query->where('description', 'like', $request->search . "%");
    }
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    $filename = "accounts_report_" . now()->format('Y-m-d') . ".csv";
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Date', 'Description', 'Credit', 'Debit', 'Balance'];

    // English: Using streamDownload with cursor to handle 3 Lakh+ records efficiently
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Clear output buffer to prevent "Invalid Response" or HTML injection
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Headers
        fputcsv($file, $columns);

        /* 🚀 English: cursor() is the secret. It fetches records one-by-one 
           from the database, keeping RAM usage near zero.
        */
        foreach ($query->orderBy('date', 'asc')->cursor() as $account) {
            fputcsv($file, [
                $account->date,
                $account->description,
                $account->credit ?? 0,
                $account->debit ?? 0,
                $account->balance ?? 0,
            ]);
        }

        fclose($file);
    }, $filename, $headers);
}


}
