<?php

namespace App\Http\Controllers\intern;

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

        $certificate = DB::table('certificate_requests')
            ->where('id', $id)
            ->where('intern_id', $intern->int_id)
            ->first();

        if (!$certificate || $certificate->status !== 'approved') {
            return back()->with('error', 'Not available.');
        }

        $path = storage_path('app/' . $certificate->pdf_path);

        if (!file_exists($path)) {
            return back()->with('error', 'File missing.');
        }

        return response()->download($path);
    }

    private function notify($intern)
{
    try {
        if (!Schema::hasTable('intern_notifications')) return;

        DB::table('intern_notifications')->insert([
            'intern_id' => $intern->int_id,
            'title' => 'Certificate Request',
            'message' => 'Your request is pending approval.',
            'type' => 'certificate',
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    } catch (\Exception $e) {
        // ❌ Do nothing (hide error)
        // optional: log it
        
    }

    }
}