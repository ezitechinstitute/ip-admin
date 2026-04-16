<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class InternOfferLetterController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get offer letter if exists - using ezi_id column
        $offerLetter = null;
        
        if (Schema::hasTable('offer_letter_requests')) {
            $offerLetter = DB::table('offer_letter_requests')
                ->where('ezi_id', $intern->eti_id)  // ezi_id stores the ETI ID
                ->first();
        }
        
        return view('pages.intern.offer-letter.index', compact('offerLetter'));
    }
    
    public function download()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get offer letter by ezi_id
        $offerLetter = DB::table('offer_letter_requests')
            ->where('ezi_id', $intern->eti_id)
            ->first();
        
        if (!$offerLetter) {
            return redirect()->back()->with('error', 'Offer letter not found.');
        }
        
        // Check if there's a PDF path column or generate from data
        // Since the table doesn't have pdf_path, we'll check if we need to generate
        
        return redirect()->back()->with('info', 'Offer letter download feature coming soon.');
    }
}