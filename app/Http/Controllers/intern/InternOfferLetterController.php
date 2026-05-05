<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class InternOfferLetterController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $offerLetter = DB::table('offer_letter_requests')
            ->where('ezi_id', $intern->eti_id)
            ->orderBy('created_at', 'desc')
            ->first();
        
        // Get offer content if template exists
        $offerContent = null;
        if ($offerLetter && in_array($offerLetter->status, ['accept', 'approved'])) {
            $template = DB::table('offer_letter_templates')
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($template && isset($template->content)) {
                $offerContent = $this->generateOfferContent($template->content, $offerLetter);
            }
        }
        
        return view('pages.intern.offer-letter.index', compact('offerLetter', 'offerContent'));
    }
    
    public function requestOfferLetter(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Check if already has pending request
        $existing = DB::table('offer_letter_requests')
            ->where('ezi_id', $intern->eti_id)
            ->where('status', 'pending')
            ->first();
        
        if ($existing) {
            return back()->with('error', 'You already have a pending offer letter request.');
        }
        
        // Check if already approved
        $approved = DB::table('offer_letter_requests')
            ->where('ezi_id', $intern->eti_id)
            ->whereIn('status', ['accept', 'approved'])
            ->first();
        
        if ($approved) {
            return back()->with('info', 'You already have an approved offer letter.');
        }
        
        // Generate unique offer letter ID
        $offerLetterId = 'OL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        
        // Create new request
        DB::table('offer_letter_requests')->insert([
            'offer_letter_id' => $offerLetterId,
            'username'        => $intern->username ?? $intern->name ?? $intern->email,
            'email'           => $intern->email,
            'ezi_id'          => $intern->eti_id,
            'reason'          => 'Intern requested offer letter',
            'status'          => 'pending',
        ]);
        
        return back()->with('success', '✅ Offer letter request submitted! Request ID: ' . $offerLetterId);
    }
    
    public function download()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $offerLetter = DB::table('offer_letter_requests')
            ->where('ezi_id', $intern->eti_id)
            ->whereIn('status', ['accept', 'approved'])
            ->first();
        
        if (!$offerLetter) {
            return back()->with('error', 'No approved offer letter found.');
        }
        
        // Generate PDF
        $pdf = Pdf::loadView('pages.intern.offer-letter.pdf', compact('offerLetter', 'intern'));
        
        return $pdf->download('Offer_Letter_' . $offerLetter->offer_letter_id . '.pdf');
    }
    
    private function generateOfferContent($templateContent, $offerLetter)
    {
        $replacements = [
            '{name}'       => $offerLetter->username ?? 'Intern',
            '{email}'      => $offerLetter->email ?? '',
            '{technology}' => $offerLetter->tech ?? 'Not Assigned',
            '{join_date}'  => isset($offerLetter->created_at) ? date('F d, Y', strtotime($offerLetter->created_at)) : date('F d, Y'),
            '{end_date}'   => $offerLetter->end_date ?? 'To be decided',
            '{duration}'   => $offerLetter->duration ?? '3 Months',
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $templateContent);
    }
}