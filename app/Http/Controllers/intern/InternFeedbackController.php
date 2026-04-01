<?php

namespace App\Http\Controllers\intern;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternFeedbackController extends Controller
{
    public function index()
    {
        $intern = Auth::guard('intern')->user();
        
        if (!$intern) {
            return redirect()->route('login');
        }
        
        $feedbacks = DB::table('intern_feedback')
            ->where('intern_id', $intern->int_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('pages.intern.feedback.index', compact('feedbacks'));
    }
}