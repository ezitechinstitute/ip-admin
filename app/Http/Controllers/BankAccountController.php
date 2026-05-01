<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\FundTransfer;
use App\Models\Account;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankAccountController extends Controller
{
    /**
     * List all active payment accounts.
     */
    public function index(Request $request)
    {
        $pageLimitSet = AdminSetting::first();
        $perPage = $request->input('per_page', $pageLimitSet->pagination_limit ?? 25);

        $query = BankAccount::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_name', 'like', "{$search}%")
                  ->orWhere('account_number', 'like', "{$search}%");
            });
        }

        $bankAccounts = $query->orderBy('account_name')
            ->paginate($perPage)
            ->withQueryString();

        $totalBalance = (clone $query)->sum('current_balance');

        return view('pages.admin.bank-accounts.index', compact('bankAccounts', 'perPage', 'totalBalance'));
    }

    /**
     * Store a new payment account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'account_type' => 'nullable|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $bankAccount = BankAccount::create([
                'account_name' => $request->account_name,
                'account_number' => $request->account_number,
                'account_type' => $request->account_type ?? 'payment',
                'opening_balance' => $request->opening_balance,
                'current_balance' => $request->opening_balance,
                'note' => $request->note,
                'added_by' => auth()->user()->name ?? 'Admin',
            ]);

            if ($request->opening_balance > 0) {
                Account::create([
                    'date' => now()->format('Y-m-d'),
                    'credit' => $request->opening_balance,
                    'debit' => 0,
                    'balance' => $request->opening_balance,
                    'description' => 'Opening balance - ' . $request->account_name,
                    'bank_account_id' => $bankAccount->id,
                ]);
            }

            DB::commit();
            return redirect()->route('bank-accounts.index')
                ->with('success', 'Account "' . $request->account_name . '" created!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment account creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create account.');
        }
    }

    /**
     * Process fund transfer.
     */
    public function processTransfer(Request $request)
    {
        $request->validate([
            'from_bank_account_id' => 'required|exists:bank_accounts,id',
            'to_bank_account_id' => 'required|exists:bank_accounts,id|different:from_bank_account_id',
            'amount' => 'required|numeric|min:0.01',
            'transfer_date' => 'required|date',
            'note' => 'nullable|string|max:500',
            'document' => 'nullable|file|mimes:pdf,csv,zip,doc,docx,jpeg,jpg,png|max:5120',
        ]);

        $fromAccount = BankAccount::findOrFail($request->from_bank_account_id);
        $toAccount = BankAccount::findOrFail($request->to_bank_account_id);

        if ($fromAccount->current_balance < $request->amount) {
            return back()->with('error', 'Insufficient balance! Available: Rs ' . number_format($fromAccount->current_balance, 2));
        }

        DB::beginTransaction();
        try {
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('fund-transfers/' . now()->format('Y/m'), 'public');
            }

            $transfer = FundTransfer::create([
                'from_bank_account_id' => $fromAccount->id,
                'to_bank_account_id' => $toAccount->id,
                'amount' => $request->amount,
                'transfer_date' => $request->transfer_date,
                'note' => $request->note,
                'document_path' => $documentPath,
                'status' => 'completed',
                'created_by' => auth()->user()->name ?? 'Admin',
                'created_by_role' => auth()->user()->role ?? 'admin',
            ]);

            $fromAccount->current_balance -= $request->amount;
            $fromAccount->save();

            $toAccount->current_balance += $request->amount;
            $toAccount->save();

            Account::create([
                'date' => $request->transfer_date,
                'credit' => 0,
                'debit' => $request->amount,
                'balance' => $fromAccount->current_balance,
                'description' => 'Transfer to ' . $toAccount->account_name . ' [' . $transfer->transfer_id . ']',
                'bank_account_id' => $fromAccount->id,
            ]);

            Account::create([
                'date' => $request->transfer_date,
                'credit' => $request->amount,
                'debit' => 0,
                'balance' => $toAccount->current_balance,
                'description' => 'Transfer from ' . $fromAccount->account_name . ' [' . $transfer->transfer_id . ']',
                'bank_account_id' => $toAccount->id,
            ]);

            DB::commit();

            return redirect()->route('bank-accounts.index')
                ->with('success', 'Transfer completed! ID: ' . $transfer->transfer_id . ' | Rs ' . number_format($request->amount, 2));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fund Transfer Failed: ' . $e->getMessage());
            return back()->with('error', 'Transfer failed!');
        }
    }

    /**
     * Transfer history data (AJAX for modal).
     */
    public function transferHistoryData()
    {
        $transfers = FundTransfer::with(['fromAccount', 'toAccount'])
            ->latest('transfer_date')
            ->latest('id')
            ->limit(100)
            ->get()
            ->map(function ($t) {
                return [
                    'transfer_id' => $t->transfer_id,
                    'transfer_date' => $t->transfer_date->format('Y-m-d'),
                    'from_account' => $t->fromAccount->account_name ?? 'N/A',
                    'to_account' => $t->toAccount->account_name ?? 'N/A',
                    'amount' => $t->amount,
                    'note' => $t->note,
                ];
            });

        return response()->json($transfers);
    }

    /**
     * Deactivate a payment account.
     */
    public function deactivate($id)
    {
        $bankAccount = BankAccount::findOrFail($id);
        $bankAccount->is_active = false;
        $bankAccount->save();

        return back()->with('success', 'Account "' . $bankAccount->account_name . '" deactivated.');
    }
}