<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\OfferLetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  // English: Correctly imported for DB transactions
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;


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
        // 1. Fetch Template
        $template = OfferLetterTemplate::find($templateId);

        // 2. Fetch Request Data (Using the table you provided)
        $requestData = DB::table('offer_letter_requests')->where('id', $internId)->first();

        // 3. Validation
        if (!$template) {
            return response()->json(['html' => '<p class="text-danger">Template not found.</p>'], 404);
        }
        if (!$requestData) {
            return response()->json(['html' => '<p class="text-danger">Intern data not found.</p>'], 404);
        }

        /** * 4. Content Replacement 
         * English: We call the private helper method we created earlier 
         * to handle all table columns (name, cnic, university, etc.)
         */
        $content = $this->replacePlaceholders($template->content, $requestData);

        return response()->json([
            'status' => 'success',
            'html'   => $content
        ]);

    } catch (\Exception $e) {
        Log::error("Preview Error: " . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'html'   => '<div class="alert alert-danger">Server Error: ' . $e->getMessage() . '</div>'
        ], 500);
    }
}



public function sendOfferLetter(Request $request)
{
    $request->validate(['intern_id' => 'required', 'template_id' => 'required']);

    try {
        $template = OfferLetterTemplate::findOrFail($request->template_id);

        // English: Fetching data with joins as per your table structure
        $intern = DB::table('offer_letter_requests')
            ->leftJoin('intern_table', 'offer_letter_requests.username', '=', 'intern_table.name')
            ->where('offer_letter_requests.id', $request->intern_id)
            ->select('intern_table.*', 'offer_letter_requests.username as request_name', 'offer_letter_requests.email as request_email')
            ->first();

        if (!$intern) { return back()->with('error', 'Intern details not found.'); }

        // 1. Process Content
        $processedContent = $this->replacePlaceholders($template->content, $intern);

        // 2. Build PDF HTML
        // English: The "page-break-inside: avoid" rule is critical here
        $pdfHtml = '
        <html>
        <head>
            <style>
                @page { margin: 0px; }
                body { 
                    margin: 0; padding: 0; 
                    width: 100%; 
                    font-family: "serif";
                    line-height: 1.2; /* English: Reduced line height to save space */
                }
                .pdf-wrapper {
                    width: 100%;
                    page-break-inside: avoid; /* English: Forces everything to stay on one page */
                }
                table { 
                    width: 100% !important; 
                    border-collapse: collapse; 
                }
                [bgcolor="#003366"] {
                    border-bottom-left-radius: 400px 80px !important;
                    border-bottom-right-radius: 400px 80px !important;
                }
            </style>
        </head>
        <body>
            <div class="pdf-wrapper">
                ' . $processedContent . '
            </div>
        </body>
        </html>';

        // 3. Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($pdfHtml);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true, 
            'isRemoteEnabled' => true, 
            'dpi' => 90
        ]);

        $pdfOutput = $pdf->output();

        // 4. Email Body (Simple Wrapper)
        $emailBody = '<div style="font-family: sans-serif; padding: 20px;">
                        <p>Dear ' . ($intern->request_name ?? 'Intern') . ',</p>
                        <p>Please find your Internship Offer Letter attached below.</p>
                        <br>
                        <p>Regards,<br>Ezline Software House Team</p>
                      </div>';

        // 5. Send Email
        $emailTo = $intern->request_email ?? $intern->email;
        $fileName = "Offer_Letter_" . str_replace(' ', '_', $intern->request_name) . ".pdf";

        \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($emailTo, $emailBody, $pdfOutput, $fileName) {
            $message->to($emailTo)
                ->subject('Internship Offer Letter - Ezline Software House')
                ->html($emailBody)
                ->attachData($pdfOutput, $fileName, ['mime' => 'application/pdf']);
        });

        return back()->with('success', 'Offer letter sent successfully!');

    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}





    public function downloadOfferLetterPdf(Request $request)
{
    $template = OfferLetterTemplate::findOrFail($request->template_id);

    $requestData = DB::table('offer_letter_requests')
        ->leftJoin('intern_table', 'offer_letter_requests.username', '=', 'intern_table.name')
        ->where('offer_letter_requests.id', $request->intern_id)
        ->select('intern_table.*', 'offer_letter_requests.username as request_name', 'offer_letter_requests.email as request_email')
        ->first();

    if (!$requestData) {
        return back()->with('error', 'Database Join Error: Intern details not found in intern_table for this request.');
    }
    
    $content = $this->replacePlaceholders($template->content, $requestData);

    $html = '
    <html>
    <head>
        <style>
            @page { margin: 0px; }
            body { margin: 0; padding: 0; font-family: "Helvetica", sans-serif; font-size: 12px; background-color: white; }
            .page-container { width: 100%; position: relative; }
            .inner-content { width: 100%; margin: 0 auto; background-color: white; }
            table { width: 100% !important; }
            * { -webkit-print-color-adjust: exact; }
        </style>
    </head>
    <body>
        <div class="page-container">
            <div class="inner-content">' . $content . '</div>
        </div>
    </body>
    </html>';

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
    $pdf->setPaper('a4', 'portrait');
    $pdf->setOptions([
        'isHtml5ParserEnabled' => true, 
        'isRemoteEnabled' => true,
        'dpi' => 91
    ]);

   
  $baseName = ($requestData->request_name ?? 'Intern') . ' Offer Letter';
$fileName = str_replace(' ', '_', $baseName);
    return $pdf->download($fileName . ".pdf");
}

private function replacePlaceholders($content, $data)
{
    $joinDateRaw = $data->join_date ?? null;
    $join_date = $joinDateRaw ? date('d-M-Y', strtotime($joinDateRaw)) : date('d-M-Y');
    
    $duration = $data->duration ?? '3 Months';
    $end_date = $joinDateRaw ? date('d-M-Y', strtotime($joinDateRaw . ' + ' . $duration)) : 'N/A';

    $map = [
        'name'        => $data->name ?? $data->request_name ?? 'Intern',
        'email'       => $data->email ?? $data->request_email ?? 'N/A',
        'join_date'   => $join_date,
        'end_date'    => $end_date,
        'technology'  => $data->technology ?? 'Development',
        'duration'    => $duration,
    ];

    foreach ($map as $key => $value) {
        $pattern = '/\{{1,2}\s*' . preg_quote($key, '/') . '\s*\}{1,2}/i';
        $content = preg_replace($pattern, $value, $content);
    }

    return $content;
}



}
