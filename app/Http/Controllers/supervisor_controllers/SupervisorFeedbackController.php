<?php

namespace App\Http\Controllers\supervisor_controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupervisorFeedbackController extends Controller
{
   public function index()
{
    $supervisorTechnology = session('manager_department');

    $feedbacks = \Illuminate\Support\Facades\DB::table('intern_feedback')
        ->join('intern_accounts', 'intern_feedback.eti_id', '=', 'intern_accounts.eti_id')
        ->select(
            'intern_feedback.id',
            'intern_feedback.eti_id',
            'intern_accounts.name',
            'intern_accounts.email',
            'intern_feedback.feedback_text',
            'intern_feedback.created_at'
        )
        ->when($supervisorTechnology, function ($query, $supervisorTechnology) {
            $query->where('intern_accounts.int_technology', $supervisorTechnology);
        })
        ->orderByDesc('intern_feedback.id')
        ->limit(20)
        ->get();

    return view('content.supervisor.feedback', compact('feedbacks'));
}
}