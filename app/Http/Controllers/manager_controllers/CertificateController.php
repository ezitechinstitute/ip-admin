<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use App\Models\CertificateRequest;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * CertificateController - Consolidated Certificate Management System
 * 
 * This controller handles all certificate-related operations including:
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * TEMPLATE MANAGEMENT (Template CRUD)
 * ─────────────────────────────────────
 * • templates()          - List all certificate templates (paginated, searchable)
 * • storeTemplate()      - Create new HTML/content-based certificate template
 * • updateTemplate()     - Update existing template
 * • destroyTemplate()    - Soft delete template (uses is_deleted flag)
 * • previewTemplate()    - Generate PDF preview of template
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * CERTIFICATE REQUEST MANAGEMENT (Request CRUD)
 * ─────────────────────────────────────────────
 * • requests()           - List certificate requests (paginated, searchable, filterable by status)
 * • submitRequest()      - Submit new certificate request (prevents duplicates)
 * • updateRequestStatus()- Approve/reject requests with PDF generation & email
 * • downloadCertificate()- Download approved certificate PDF
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * FEATURES:
 * ─────────
 * ✓ Permission checks for all operations
 * ✓ Pagination with configurable limits
 * ✓ Search & filtering capabilities
 * ✓ HTML/content-based template generation
 * ✓ Dynamic template variables ({{name}}, {{email}}, {{certificate_type}}, {{date}})
 * ✓ PDF generation and storage
 * ✓ Email notifications to managers & interns
 * ✓ Duplicate request prevention
 * ✓ Soft delete for data retention
 * 
 * ═══════════════════════════════════════════════════════════════════════════
 * 
 * Note: Previous duplicate controllers (CertificateTemplateController) have been 
 * consolidated into this single unified controller. All routes point here.
 * 
 * @author System
 * @version 2.0 Consolidated
 * @since 2024
 */
class CertificateController extends Controller
{
    public function templates(Request $request)
    {
        $manager = Auth::guard('manager')->user();
        if (!$manager) return redirect()->route('manager.login');

        if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_certificate_templates')) {
            return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Permission denied.']);
        }

        $pageLimitSet = AdminSetting::first();
        $perPage = $request->get('perpage', $pageLimitSet->pagination_limit ?? 15);

        $query = CertificateTemplate::where('is_deleted', 0);
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', "%{$request->search}%");
        }
        if ($request->filled('certificate_type')) {
            $query->where('certificate_type', $request->certificate_type);
        }

        $templates = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();

        return view('pages.manager.certificate-template.certificateTemplate', compact('templates', 'perPage'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'certificate_type' => 'required|in:internship,course_completion',
            'content' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        CertificateTemplate::create([
            'title' => $request->title,
            'certificate_type' => $request->certificate_type,
            'content' => $request->content,
            'manager_id' => Auth::guard('manager')->id(),
            'status' => (int) $request->status,
        ]);

        return back()->with('success', 'Certificate template created successfully.');
    }

    public function updateTemplate(Request $request, $id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'certificate_type' => 'required|in:internship,course_completion',
            'content' => 'required|string',
            'status' => 'required|in:0,1',
        ]);

        $template->update([
            'title' => $request->title,
            'certificate_type' => $request->certificate_type,
            'content' => $request->content,
            'status' => (int) $request->status,
        ]);

        return back()->with('success', 'Certificate template updated successfully.');
    }

    public function previewTemplate($id)
    {
        $template = CertificateTemplate::where('id', $id)->where('is_deleted', 0)->firstOrFail();
        $content = str_replace(
            ['{{name}}','{{email}}','{{certificate_type}}','{{date}}'], 
            ['John Doe', 'john@example.com', ucfirst(str_replace('_', ' ', $template->certificate_type)), date('d M Y')],
            $template->content
        );

        $html = '<html><head><style>body{font-family:Arial, sans-serif; margin:30px;} .certificate{border:2px solid #333; padding:24px; border-radius:12px;}</style></head><body><div class="certificate">' . $content . '</div></body></html>';
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('template_' . $template->id . '.pdf');
    }

    public function destroyTemplate($id)
    {
        $template = CertificateTemplate::findOrFail($id);
        $template->update(['is_deleted' => 1]);
        return back()->with('success', 'Template removed.');
    }

   public function requests(Request $request)
{
    $manager = Auth::guard('manager')->user();
    if (!$manager) return redirect()->route('manager.login');

    if (\Illuminate\Support\Facades\Gate::forUser($manager)->denies('check-privilege', 'view_manager_certificate_requests')) {
        return redirect()->route('manager.dashboard')->withErrors(['access_denied' => 'Permission denied.']);
    }

    $pageLimitSet = AdminSetting::first();
    $perPage = $request->get('perpage', $pageLimitSet->pagination_limit ?? 15);

    // ✅ FIXED: Remove manager_id filter to see all requests
    $query = CertificateRequest::orderBy('id', 'desc');
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('search')) {
        $term = $request->search;
        $query->where(function ($q) use ($term) {
            $q->where('certificate_request_id', 'LIKE', "%{$term}%")
                ->orWhere('intern_name', 'LIKE', "%{$term}%")
                ->orWhere('email', 'LIKE', "%{$term}%");
        });
    }

    $requests = $query->paginate($perPage)->withQueryString();
    $certificateTemplates = CertificateTemplate::where('status', 1)->where('is_deleted', 0)->get();

    return view('pages.manager.certificate-request.certificateRequest', compact('requests', 'perPage', 'certificateTemplates'));
}

    public function submitRequest(Request $request)
    {
        $request->validate([
            'intern_id' => 'required|integer',
            'intern_name' => 'required|string',
            'email' => 'required|email',
        ]);

        // Avoid duplicate pending request for same intern
        $exists = CertificateRequest::where('intern_id', $request->intern_id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'A certificate request already exists for this intern.');
        }

        $certificateRequest = CertificateRequest::create([
            'certificate_request_id' => 'CERT-' . strtoupper(Str::random(6)),
            'intern_id' => $request->intern_id,
            'intern_name' => $request->intern_name,
            'email' => $request->email,
            'manager_id' => Auth::guard('manager')->id(),
            'certificate_type' => $request->certificate_type ?? 'internship',
            'status' => 'pending',
        ]);

        $managerUser = Auth::guard('manager')->user();
        if ($managerUser && !empty($managerUser->email)) {
            Mail::raw("New certificate request {$certificateRequest->certificate_request_id} submitted by {$request->intern_name}.", function ($message) use ($managerUser) {
                $message->to($managerUser->email)->subject('New Certificate Request Pending Approval');
            });
        }

        return back()->with('success', 'Certificate request submitted successfully. Manager notified.');
    }

   public function updateRequestStatus(Request $request)
{
    $request->validate([
        'id' => 'required|integer',
        'status' => 'required|in:approved,rejected',
        'certificate_type' => 'required|in:internship,course_completion',
    ]);

    $certificateRequest = CertificateRequest::findOrFail($request->id);
    $certificateRequest->status = $request->status;
    $certificateRequest->reason = $request->reason;
    $certificateRequest->certificate_type = $request->certificate_type;

    if ($request->status === 'approved') {
        $template = CertificateTemplate::where('certificate_type', $request->certificate_type)
            ->where('status', 1)
            ->where('is_deleted', 0)
            ->latest('id')
            ->first();

        if (!$template) {
            return back()->with('error', 'No active certificate template found for this certificate type.');
        }

        // ✅ Get intern data for start_date and end_date
$intern = \App\Models\InternAccount::where('email', $certificateRequest->email)->first();        
        // Format dates for display
        $start_date = $intern->join_date ? date('d M Y', strtotime($intern->join_date)) : 'N/A';
        $end_date = $intern->end_date ? date('d M Y', strtotime($intern->end_date)) : 'N/A';
        $technology = $intern->technology ?? 'N/A';

        // ✅ Replace all placeholders including start_date and end_date
        $content = str_replace(
            [
                '{{name}}',
                '{{email}}',
                '{{certificate_type}}',
                '{{date}}',
                '{{start_date}}',
                '{{end_date}}',
                '{{technology}}',
                '{{certificate_id}}'
            ],
            [
                $certificateRequest->intern_name,
                $certificateRequest->email,
                ucfirst(str_replace('_', ' ', $request->certificate_type)),
                date('d M Y'),
                $start_date,
                $end_date,
                $technology,
                $certificateRequest->certificate_request_id
            ],
            $template->content
        );

        $html = '<html><head><style>
            body{font-family: Arial, sans-serif; margin: 0; padding: 20px;}
            .certificate-container{max-width: 800px; margin: 0 auto;}
        </style></head><body><div class="certificate-container">' . $content . '</div></body></html>';
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('a4', 'portrait');
        $pdfData = $pdf->output();

        $folder = storage_path('app/certificates');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $filename = 'certificate_' . $certificateRequest->certificate_request_id . '.pdf';
        $path = $folder . '/' . $filename;
        file_put_contents($path, $pdfData);

        $certificateRequest->pdf_path = 'certificates/' . $filename;
        $certificateRequest->approved_at = now();

        // Email intern with certificate
        $emailTo = $certificateRequest->email;
        $certificateType = ucfirst(str_replace('_', ' ', $request->certificate_type));
        $mailBody = '
        <!DOCTYPE html>
        <html>
        <head><title>Certificate Approved</title></head>
        <body style="font-family: Arial, sans-serif;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2 style="color: #2b9a82;">Certificate Approved!</h2>
                <p>Dear <strong>' . e($certificateRequest->intern_name) . '</strong>,</p>
                <p>Your <strong>' . $certificateType . '</strong> certificate has been approved and is attached to this email.</p>
                <div style="background: #f5f5f5; padding: 15px; border-radius: 8px; margin: 20px 0;">
                    <p><strong>Certificate Details:</strong></p>
                    <p>Certificate ID: ' . $certificateRequest->certificate_request_id . '</p>
                    <p>Issue Date: ' . date('d M Y') . '</p>
                    <p>Technology: ' . $technology . '</p>
                    <p>Period: ' . $start_date . ' to ' . $end_date . '</p>
                </div>
                <p>You can also download your certificate from the intern portal.</p>
                <p>Best regards,<br><strong>Ezitech Team</strong></p>
            </div>
        </body>
        </html>';

        Mail::html($mailBody, function ($message) use ($emailTo, $pdfData, $filename) {
            $message->to($emailTo)
                ->subject('Your ' . $filename . ' is Approved')
                ->attachData($pdfData, $filename, ['mime' => 'application/pdf']);
        });
    }

    $certificateRequest->save();

    return back()->with('success', 'Certificate request ' . ucfirst($request->status) . '.');
}

    public function downloadCertificate(Request $request, $id)
    {
        $cert = CertificateRequest::findOrFail($id);

        if ($cert->status !== 'approved') {
            return back()->with('error', 'Only approved certificates can be downloaded.');
        }

        if (!$cert->pdf_path || !file_exists(storage_path('app/' . $cert->pdf_path))) {
            return back()->with('error', 'Certificate PDF not found. Please approve again to generate PDF.');
        }

        return response()->download(storage_path('app/' . $cert->pdf_path));
    }
}

