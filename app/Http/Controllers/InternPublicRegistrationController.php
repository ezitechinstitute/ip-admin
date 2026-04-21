<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class InternPublicRegistrationController extends Controller
{
    public function step1()
    {
        Session::forget(['intern_reg_step1', 'intern_reg_step2']);
        return view('pages.internship-registration.step1');
    }

    public function step2()
    {
        return view('pages.internship-registration.step2');
    }

    public function step3()
    {
        return view('pages.internship-registration.step3');
    }

    public function complete(Request $request)
    {
        // Validate request first
        $request->validate([
            'selected_plan' => 'required|in:training,practice,industrial',
        ]);

        // Get session data
        $step1Data = Session::get('intern_reg_step1');
        $step2Data = Session::get('intern_reg_step2');

        // Safety check
        if (!$step1Data) {
            return redirect()->route('intern.register.step1')
                ->with('error', 'Please complete step 1 first.');
        }

        // Create Intern
        $intern = Intern::create([
            'name' => $step1Data['full_name'] ?? '',
            'email' => $step1Data['email'] ?? '',
            'city' => $step1Data['city'] ?? '',
            'country' => $step1Data['country'] ?? '',
            'gender' => $step1Data['gender'] ?? '',
            'birth_date' => $step1Data['date_of_birth'] ?? null,
            'university' => $step1Data['university'] ?? '',
            'technology' => $step1Data['technology'] ?? '',
            'phone' => $step1Data['whatsapp'] ?? '',
            'image' => $step1Data['profile_image'] ?? '',
            'cnic' => '',
            'interview_type' => '',
            'duration' => '',
            'interview_date' => null,
            'interview_time' => null,
            'status' => 'interview',
            'intern_type' => $this->mapPlanToInternType($request->selected_plan),
            'join_date' => now(),
        ]);

        // Create Intern Account
        InternAccount::create([
            'int_id' => $intern->id,
            'eti_id' => null,
            'name' => $step1Data['full_name'] ?? '',
            'email' => $step1Data['email'] ?? '',
            'phone' => $step1Data['whatsapp'] ?? '',
            'password' => Hash::make('default_password_' . $intern->id),
            'int_technology' => $step1Data['technology'] ?? '',
            'int_status' => 'active',
            'portal_status' => 'pending_activation',
            'start_date' => now(),
        ]);

        // Clear session after success
        Session::forget(['intern_reg_step1', 'intern_reg_step2']);

        return redirect()->route('intern.register.success');
    }

    public function success()
    {
        return view('pages.internship-registration.success');
    }

    private function mapPlanToInternType($plan)
    {
        return match ($plan) {
            'training' => 'Training Internship',
            'practice' => 'Project Practice',
            'industrial' => 'Industrial Environment',
            default => 'Training Internship',
        };
    }
}