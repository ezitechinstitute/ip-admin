<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InternFeedbackController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        // Get feedback history
        $feedbacks = collect([]);
        if (Schema::hasTable('intern_feedback')) {
            $feedbacks = DB::table('intern_feedback')
                ->where('eti_id', $intern->eti_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        
        return view('pages.intern.feedback.index', compact('feedbacks'));
    }
    
    public function submitFeedback(Request $request)
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:1000',
        ]);
        
        // Submit feedback/complaint
        if (Schema::hasTable('intern_feedback')) {
            DB::table('intern_feedback')->insert([
                'eti_id' => $intern->eti_id,
                'feedback_text' => "Category: " . $validated['category'] . "\n\nSubject: " . $validated['subject'] . "\n\nMessage: " . $validated['message'],
                'status' => 'Open',
                'created_at' => now(),
            ]);
        } else {
            return redirect()->back()->with('error', 'Feedback system is temporarily unavailable.');
        }
        
        return redirect()->route('intern.feedback')
            ->with('success', 'Your feedback has been submitted successfully. Admin will review it.');
    }
}