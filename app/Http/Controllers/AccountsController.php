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

    // 🔍 Search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('description', 'like', "%{$search}%");
    }

    // 📅 From Date Filter
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }

    // 📅 To Date Filter
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    // 💰 Totals (after applying filters)
    $totalCredit = $query->sum('credit');
    $totalDebit = $query->sum('debit');
    $totalBalance = $query->sum('balance');

    // 🗂 Latest first
    $query->latest('date');

    // 📄 Paginate
    $accounts = $query->paginate($perPage)->withQueryString();

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
    $query = Account::query();

    // Apply same filters as index
    if ($request->filled('search')) {
        $query->where('description', 'like', "%{$request->search}%");
    }
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    $accounts = $query->orderBy('date', 'asc')->get();

    $filename = "accounts_report_" . now()->format('Y-m-d') . ".csv";
    
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Date', 'Description', 'Credit', 'Debit', 'Balance'];

    $callback = function() use($accounts, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($accounts as $account) {
            fputcsv($file, [
                $account->date,
                $account->description,
                $account->credit ?? 0,
                $account->debit ?? 0,
                $account->balance ?? 0,
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


}
