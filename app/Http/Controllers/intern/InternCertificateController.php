<?php

namespace App\Http\Controllers\intern;
use App\Helpers\PortalFreezeHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class InternCertificateController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login');
        }

        $certificates = DB::table('certificate_requests')
            ->where('intern_id', $intern->int_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // ✅ SIMPLE & REALISTIC LOGIC
        $canRequest = true;

        return view('pages.intern.certificates.index', compact('certificates', 'canRequest'));
    }

    public function requestCertificate(Request $request)
    {
        $intern = Auth::guard('intern')->user();

        if (!$intern) {
            return redirect()->route('login');
        }

            $freezeStatus = PortalFreezeHelper::getStatus($intern->email);
            if ($freezeStatus['frozen']) {
            return back()->with('error', $freezeStatus['message']);
            }


        $validated = $request->validate([
            'certificate_type' => 'required|in:internship,course_completion',
            'purpose' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Prevent duplicate request (same type)
        $exists = DB::table('certificate_requests')
            ->where('intern_id', $intern->int_id)
            ->where('certificate_type', $validated['certificate_type'])
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return back()->with('error', 'You already requested this certificate.');
        }

     DB::table('certificate_requests')->insert([
    'certificate_request_id' => 'CERT-' . strtoupper(uniqid()),
    'intern_id' => $intern->int_id,
    'intern_name' => $intern->name,
    'email' => $intern->email,

    // 🔥 ADD THIS LINE (VERY IMPORTANT)
    'manager_id' => 1,

    'certificate_type' => $validated['certificate_type'],
    'purpose' => $request->purpose,
    'notes' => $request->notes,
    'status' => 'pending',
    'created_at' => now(),
    'updated_at' => now(),
]);

        $this->notify($intern);

        return redirect()->route('intern.certificates')
            ->with('success', 'Certificate request submitted!');
    }



  public function downloadCertificate($id)
{
    $intern = Auth::guard('intern')->user();

    if (!$intern) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }

    $certificate = DB::table('certificate_requests')
        ->where('id', $id)
        ->where('intern_id', $intern->int_id)
        ->first();

    // Check if certificate exists
    if (!$certificate) {
        return back()->with('error', 'Certificate request not found.');
    }

    // Check if certificate is approved
    if ($certificate->status !== 'approved') {
        return back()->with('error', 'Certificate is not approved yet. Status: ' . ucfirst($certificate->status));
    }

    // Check if PDF path exists in database
    if (!$certificate->pdf_path) {
        return back()->with('error', 'PDF file not generated yet. Please contact your manager.');
    }

    // Check if file actually exists on disk
    $path = storage_path('app/' . $certificate->pdf_path);

    if (!file_exists($path)) {
        return back()->with('error', 'Certificate file missing. Please contact your manager to regenerate the certificate.');
    }

    // Download with proper filename
    $filename = 'certificate_' . $certificate->certificate_request_id . '.pdf';
    
    return response()->download($path, $filename, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
}

private function notify($intern)
{
    try {
        if (!Schema::hasTable('intern_notifications')) {
            Log::warning('intern_notifications table does not exist');  // ✅ Changed
            return;
        }

        DB::table('intern_notifications')->insert([
            'intern_id' => $intern->int_id,
            'title' => 'Certificate Request Submitted',
            'message' => 'Your certificate request has been submitted and is pending manager approval.',
            'type' => 'certificate_request',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Certificate request notification sent to intern', [  // ✅ Changed
            'intern_id' => $intern->int_id,
            'intern_name' => $intern->name
        ]);

    } catch (\Exception $e) {
        Log::error('Failed to send certificate notification: ' . $e->getMessage(), [  // ✅ Changed
            'intern_id' => $intern->int_id ?? 'unknown'
        ]);
    }
}
}