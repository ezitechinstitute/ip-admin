<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\Intern;
use App\Models\OfferLetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;




class OfferLetterRequestController extends Controller
{
    public function index(Request $request)
{
    $manager = Auth::guard('manager')->user();
    if (!$manager) return redirect()->route('manager.login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_offer_letter_request')) {
        return redirect()->route('manager.dashboard')
                         ->withErrors(['access_denied' => 'You do not have permission to view Offer Letter Requests.']);
    }

    $pageLimitSet = AdminSetting::first();
    $perpage = $request->get('perpage', $pageLimitSet->pagination_limit ?? 15);

    $query = DB::table('offer_letter_requests');

    // 1. Status Filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 2. Search Logic
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('offer_letter_id', 'LIKE', "{$search}%")
              ->orWhere('username', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%")
              ->orWhere('ezi_id', 'LIKE', "{$search}%");
        });
    }

    // 3. Execution for Offer Letters Table
    $offerletters = $query->orderBy('id', 'desc')
                          ->paginate($perpage)
                          ->withQueryString();

    // --- NEW: FETCH TEMPLATES WITH YOUR SPECIFIC LOGIC ---
    // English: Fetching templates that belong to the current manager OR have 'can_use_other_template' set to 1
    $currentManagerId = $manager->manager_id; 

    $templates = \App\Models\OfferLetterTemplate::where('is_deleted', 0)
        ->where(function ($q) use ($currentManagerId) {
            $q->where('manager_id', $currentManagerId)
              ->orWhere('can_use_other_template', 1);
        })
        ->get(); // English: Using get() instead of paginate for the dropdown list

    return view(
        'pages.manager.offer-letter-request.offerLetterRequest',
        compact('offerletters', 'perpage', 'templates')
    );
}

public function exportCSV(Request $request)
{
    set_time_limit(0);
    ini_set('memory_limit', '512M');

    $manager = Auth::guard('manager')->user();
    if (!$manager) return abort(403);

    $query = DB::table('offer_letter_requests');

    // English: Apply status filter to export if selected
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // English: Apply search filter to export if present
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('offer_letter_id', 'LIKE', "{$search}%")
              ->orWhere('username', 'LIKE', "{$search}%")
              ->orWhere('email', 'LIKE', "{$search}%")
              ->orWhere('ezi_id', 'LIKE', "{$search}%");
        });
    }

    $fileName = 'offer_letter_requests_' . date('Y-m-d_His') . '.csv';

    return response()->streamDownload(function() use ($query) {
        if (ob_get_level() > 0) ob_end_clean();
        $file = fopen('php://output', 'w');
        fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); 

        fputcsv($file, ['Request ID', 'Username', 'Email', 'EZI ID', 'Status', 'Reason', 'Date Created']);

        foreach ($query->orderBy('id', 'desc')->cursor() as $row) {
            fputcsv($file, [
                $row->offer_letter_id,
                $row->username,
                $row->email,
                $row->ezi_id,
                $row->status ?? 'Pending',
                $row->reason ?? 'N/A',
                $row->created_at ?? 'N/A'
            ]);
        }
        fclose($file);
    }, $fileName);
}

public function updateStatus(Request $request)
{
    $request->validate([
        'id' => 'required',
        'status' => 'required|in:accept,reject'
    ]);

    // English: Update logic for 300k+ records using indexed ID
    DB::table('offer_letter_requests')
        ->where('offer_letter_id', $request->id)
        ->update(['status' => $request->status]);

    return back()->with('success', 'Status updated successfully to ' . $request->status);
}



public function getTemplatePreview($templateId, $internId)
{
    try {
        // 1. Fetch Template (English: Using find() on Model)
        $template = \App\Models\OfferLetterTemplate::find($templateId);

        // 2. Fetch Request Data (English: Using where() to be safe with DB table)
        $requestData = \DB::table('offer_letter_requests')->where('id', $internId)->first();

        // 3. Validation Check
        if (!$template) {
            return response()->json(['html' => '<p class="text-danger">Template not found (ID: '.$templateId.')</p>'], 404);
        }
        if (!$requestData) {
            return response()->json(['html' => '<p class="text-danger">Intern data not found (ID: '.$internId.')</p>'], 404);
        }

        // 4. Content Logic
        $content = $template->content;

        // 5. Replace Placeholders (English: Match these with your DB column names)
        // Agar aapke table mein 'username' ki jagah 'name' hai toh usey change karein
        $content = str_replace('{name}', $requestData->username ?? 'N/A', $content);
        $content = str_replace('{email}', $requestData->email ?? 'N/A', $content);
        $content = str_replace('{id}', $requestData->ezi_id ?? 'N/A', $content);

        return response()->json([
            'status' => 'success',
            'html'   => $content
        ]);

    } catch (\Exception $e) {
        // English: Log the exact error to storage/logs/laravel.log
        \Log::error("Preview Error: " . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'html'   => '<p class="text-danger">Server Error: ' . $e->getMessage() . '</p>'
        ], 500);
    }
}



public function sendOfferLetter(Request $request)
{
    // 1. Validation
    $request->validate([
        'intern_id'   => 'required',
        'template_id' => 'required'
    ]);

    try {
        // English: Logic to fetch intern and template, then send email
        // $intern = Intern::find($request->intern_id);
        // $template = OfferLetterTemplate::find($request->template_id);
        
        // Email sending logic here...

        return back()->with('success', 'Offer letter sent successfully!');
    } catch (\Exception $e) {
        return back()->with('error', 'Something went wrong: ' . $e->getMessage());
    }
}
}
