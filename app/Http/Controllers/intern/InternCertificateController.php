<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class InternCertificateController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get certificates for this intern
        $certificates = DB::table('certificate_requests')
            ->where('intern_id', $intern->int_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Check if intern can request a certificate (internship completed)
        $canRequest = false;
        if ($intern->start_date) {
            $endDate = Carbon::parse($intern->start_date)->addMonths(6);
            $canRequest = Carbon::now()->greaterThanOrEqualTo($endDate);
        }
        
        return view('pages.intern.certificates.index', compact('certificates', 'canRequest'));
    }
    
    public function requestCertificate(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $validated = $request->validate([
            'certificate_type' => 'required|in:internship,course_completion',
        ]);
        
        // Check if already has pending request
        $existingRequest = DB::table('certificate_requests')
            ->where('intern_id', $intern->int_id)
            ->where('status', 'pending')
            ->first();
        
        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending certificate request.');
        }
        
        // Generate unique certificate request ID
        $certRequestId = 'CERT-' . strtoupper(uniqid());
        
        // Create certificate request
        DB::table('certificate_requests')->insert([
            'certificate_request_id' => $certRequestId,
            'intern_id' => $intern->int_id,
            'intern_name' => $intern->name,
            'email' => $intern->email,
            'certificate_type' => $validated['certificate_type'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Create notification
        $this->createCertificateNotification($intern);
        
        return redirect()->route('intern.certificates')
            ->with('success', 'Certificate request submitted successfully. You will be notified once approved.');
    }
    
    public function downloadCertificate($id)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $certificate = DB::table('certificate_requests')
            ->where('id', $id)
            ->where('intern_id', $intern->int_id)
            ->first();
        
        if (!$certificate || $certificate->status != 'approved') {
            abort(404, 'Certificate not found or not approved yet.');
        }
        
        if ($certificate->pdf_path && file_exists(storage_path('app/' . $certificate->pdf_path))) {
            return response()->download(storage_path('app/' . $certificate->pdf_path));
        }
        
        return redirect()->back()->with('error', 'Certificate file not found.');
    }
    
    private function createCertificateNotification($intern)
    {
        if (!Schema::hasTable('intern_notifications')) {
            return;
        }
        
        DB::table('intern_notifications')->insert([
            'intern_id' => $intern->int_id,
            'title' => 'Certificate Request Submitted',
            'message' => 'Your certificate request has been submitted and is pending manager approval.',
            'type' => 'certificate',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}