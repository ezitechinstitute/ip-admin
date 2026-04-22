<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Models\Withdraw;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\WithdrawApprovedMail;
use App\Mail\WithdrawRejectedMail;
use Illuminate\Support\Facades\Mail;

class WithdrawAdminController extends Controller
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
        // English: latest() can be slow on 300k+ rows; adding 'req_id' ensures stable and fast sorting
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

    // ========== APPROVAL WORKFLOW ==========
    
    public function approve($id)
    {
        try {
            $withdraw = Withdraw::findOrFail($id);
            
            // Update status to approved (1)
            $withdraw->req_status = 1;
            $withdraw->save();

            // Note: Skipping transaction logging for withdrawals since withdraw is not tied to an invoice
            // Uncomment below if you want to track withdraw transactions with a valid inv_id

            // Send notification email
            try {
                Mail::to($withdraw->manager->email)->send(new WithdrawApprovedMail($withdraw));
            } catch (\Exception $e) {
                // Log email failure but don't stop the process
            }

            // Activity log (only if table exists)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
                    DB::table('activity_logs')->insert([
                        'user_id' => Auth::id(),
                        'action' => 'Approved withdrawal request',
                        'details' => "Withdraw ID: {$id}, Amount: {$withdraw->amount}",
                        'created_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail if activity logging has issues
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request approved!',
                'redirect_url' => route('admin.withdraw')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $withdraw = Withdraw::findOrFail($id);
            
            // Update status to rejected (2)
            $withdraw->req_status = 2;
            $withdraw->save();

            // Send notification email
            try {
                Mail::to($withdraw->manager->email)->send(new WithdrawRejectedMail($withdraw, $request->reason));
            } catch (\Exception $e) {
                // Log email failure
            }

            // Activity log (only if table exists)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
                    DB::table('activity_logs')->insert([
                        'user_id' => Auth::id(),
                        'action' => 'Rejected withdrawal request',
                        'details' => "Withdraw ID: {$id}, Reason: {$request->reason}",
                        'created_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail if activity logging has issues
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request rejected!',
                'redirect_url' => route('admin.withdraw')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markPaid($id)
    {
        try {
            $withdraw = Withdraw::findOrFail($id);
            
            if ($withdraw->req_status != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved requests can be marked as paid.'
                ], 400);
            }

            // Update status to paid (3)
            $withdraw->req_status = 3;
            $withdraw->save();

            // Activity log (only if table exists)
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('activity_logs')) {
                    DB::table('activity_logs')->insert([
                        'user_id' => Auth::id(),
                        'action' => 'Marked withdrawal as paid',
                        'details' => "Withdraw ID: {$id}, Amount: {$withdraw->amount}",
                        'created_at' => now()
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail if activity logging has issues
            }

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal marked as paid!',
                'redirect_url' => route('admin.withdraw')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
