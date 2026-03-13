<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Withdraw;
use App\Models\ManagersAccount;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawManagerController extends Controller
{
    public function index(Request $request)
{
    $pageLimitSet = AdminSetting::first();
    $perPage = $request->input('perPage', $pageLimitSet->pagination_limit ?? 15);

    $query = Withdraw::query();

    // 🔍 Search (Prefix search is much faster on large datasets)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('ac_name', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('req_status', $request->status);
    }

    // 📄 Efficient Pagination
    // English: latest() can be slow on 300k+ rows; adding 'id' ensures stable and fast sorting
    $withdraws = $query->latest('req_id')->paginate($perPage)->withQueryString();

    return view('pages.admin.withdraw.withdraw', compact('withdraws', 'perPage'));
}


    public function exportWithdrawCSV(Request $request)
{
    // English: Prevent timeouts and memory exhaustion for large exports
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $query = Withdraw::query();

    // 🔍 Search Filter (Prefix optimized)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('ac_name', 'like', "{$search}%");
    }

    // 🔘 Status Filter
    if ($request->filled('status')) {
        $query->where('req_status', $request->status);
    }

    $filename = "withdraw_requests_" . date('Y-m-d_H-i-s') . ".csv";
    
    $headers = [
        "Content-type"        => "text/csv; charset=UTF-8",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    $columns = ['Bank Name', 'Account No', 'Account Holder', 'Description', 'Date', 'Amount', 'Status'];

    // English: Using streamDownload to push data directly to the browser
    return response()->streamDownload(function() use ($query, $columns) {
        // English: Ensure no previous output or HTML interferes with the CSV
        if (ob_get_level() > 0) ob_end_clean();

        $file = fopen('php://output', 'w');
        
        // UTF-8 BOM for proper Excel formatting
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 
        
        // Write Header Row
        fputcsv($file, $columns);

        /* 🚀 English: cursor() allows us to process 300k+ rows by only 
           keeping one record in memory at a time.
        */
        foreach ($query->latest('req_id')->cursor() as $row) {
            // English: Mapping status based on your integer logic
            $statusText = 'Pending';
            if ($row->req_status == 1) $statusText = 'Completed';
            if ($row->req_status == 2) $statusText = 'Rejected';

            fputcsv($file, [
                $row->bank,
                $row->ac_no,
                $row->ac_name,
                $row->description,
                $row->date,
                $row->amount,
                $statusText
            ]);
        }

        fclose($file);
    }, $filename, $headers);
}
    public function create()
        {
            return view('pages.manager.withdraw.request');
        }

    public function store(Request $request)
        {
            $request->validate([
                'amount' => 'required|numeric|min:1',
                'bank' => 'required|string|max:255',
                'ac_no' => 'required|string|max:50',
                'ac_name' => 'required|string|max:255',
                'period' => 'required|string|max:50',
                'description' => 'required|string|max:500'
            ]);

            $manager = auth('manager')->user();

            Withdraw::create([
                'eti_id' => $manager->eti_id,
                'req_by' => $manager->manager_id,
                'bank' => $request->bank,
                'ac_no' => $request->ac_no,
                'ac_name' => $request->ac_name,
                'description' => $request->description,
                'period' => $request->period,
                'date' => now(),
                'amount' => $request->amount,
                'req_status' => 0
            ]);

            return redirect()->route('manager.dashboard')
                ->with('success','Withdraw request submitted successfully.');
        }

        public function approve($id)
{
    $withdraw = Withdraw::findOrFail($id);

    if ($withdraw->req_status != 0) {
        return back()->with('error','Request already processed');
    }

    $manager = ManagersAccount::where('eti_id', $withdraw->eti_id)->first();

    if (!$manager) {
        return back()->with('error','Manager not found');
    }

    // increase manager balance
    $manager->balance += $withdraw->amount;
    $manager->save();

    // update withdraw status
    $withdraw->req_status = 1;
    $withdraw->save();

    return back()->with('success','Withdraw approved successfully');
}

    // public function approve($id)
    //     {
    //         DB::beginTransaction();

    //         try {

    //             $withdraw = Withdraw::findOrFail($id);

    //             if ($withdraw->req_status != 0) {
    //                 return back()->with('error','Request already processed');
    //             }

    //             // Get latest account balance
    //             $lastAccount = Account::latest('id')->first();
    //             $currentBalance = $lastAccount ? $lastAccount->balance : 0;

    //             // Calculate new balance
    //             $newBalance = $currentBalance - $withdraw->amount;

    //             // Insert debit transaction
    //             Account::create([
    //                 'date' => now()->toDateString(),
    //                 'credit' => 0,
    //                 'debit' => $withdraw->amount,
    //                 'balance' => $newBalance,
    //                 'description' => 'Manager withdraw approved: '.$withdraw->eti_id
    //             ]);

    //             // Update withdraw status
    //             $withdraw->req_status = 1;
    //             $withdraw->save();

    //             DB::commit();

    //             return back()->with('success','Withdraw approved successfully');

    //         } catch (\Exception $e) {

    //             DB::rollBack();

    //             return back()->with('error',$e->getMessage());
    //         }
    // }

        public function reject($id)
            {
                $withdraw = Withdraw::findOrFail($id);

                if ($withdraw->req_status != 0) {
                    return back()->with('error','Request already processed');
                }

                $withdraw->req_status = 2;
                $withdraw->save();

                return back()->with('success','Withdraw rejected');
            }

            // public function reject($id)
            // {
            //     $withdraw = Withdraw::findOrFail($id);

            //     $withdraw->update([
            //         'req_status' => 2
            //     ]);

            //     return back()->with('success','Withdraw rejected.');
            // }
}
