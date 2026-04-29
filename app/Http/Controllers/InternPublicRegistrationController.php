<?php

namespace App\Http\Controllers;
use App\Models\AdminAccount;
use App\Models\Intern;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Services\UnifiedNotificationService;

class InternPublicRegistrationController extends Controller
{
    // ========== STEP 1 ==========
    
    public function step1()
    {
        Session::forget(['intern_reg_step1', 'intern_reg_step2']);
        return view('pages.internship-registration.step1');
    }
    
    // ✅ ADD THIS METHOD
    public function postStep1(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:intern_table,email',
            'country' => 'required|string',
            'city' => 'required|string',
            'whatsapp' => 'required|string',
            'gender' => 'required|string',
            'join_date' => 'required|date',
            'dob' => 'required|date',
            'university' => 'required|string',
            'interview_type' => 'required|string',
            'technology' => 'required|string',
            'duration' => 'required|string',
            'internship_type' => 'required|string',
        ]);

        Session::put('intern_reg_step1', [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'country' => $request->country,
            'city' => $request->city,
            'whatsapp' => $request->whatsapp,
            'gender' => $request->gender,
            'join_date' => $request->join_date,
            'date_of_birth' => $request->dob,
            'university' => $request->university,
            'interview_type' => $request->interview_type,
            'technology' => $request->technology,
            'duration' => $request->duration,
            'internship_type' => $request->internship_type,
        ]);

        return redirect()->route('intern.register.step2');
    }
    
    // ========== STEP 2 ==========
    
    public function step2()
    {
        if (!Session::has('intern_reg_step1')) {
            return redirect()->route('intern.register.step1')
                ->with('error', 'Please complete step 1 first.');
        }
        return view('pages.internship-registration.step2');
    }
    
    // ✅ ADD THIS METHOD
    public function postStep2(Request $request)
    {
        $answers = [
            'q1' => $request->input('q1'),
            'q2' => $request->input('q2'),
            'q3' => $request->input('q3'),
            'q4' => $request->input('q4'),
            'q5' => $request->input('q5'),
            'q6' => $request->input('q6'),
        ];

        $skillMatchPercentage = $this->calculateSkillMatch($answers);
        $recommendedProgram = $this->getRecommendedProgram($answers);

        Session::put('intern_reg_step2', [
            'answers' => $answers,
            'skill_match_percentage' => $skillMatchPercentage,
            'recommended_program' => $recommendedProgram
        ]);

        return redirect()->route('intern.register.step3');
    }
    
    // ========== STEP 3 ==========
    
    public function step3()
    {
        if (!Session::has('intern_reg_step2')) {
            return redirect()->route('intern.register.step1')
                ->with('error', 'Please complete assessment first.');
        }

        $step2Data = Session::get('intern_reg_step2');

        return view('pages.internship-registration.step3', [
            'skill_match_percentage' => $step2Data['skill_match_percentage'],
            'recommended_program' => $step2Data['recommended_program'],
        ]);
    }
    
    // ========== COMPLETE REGISTRATION ==========
    
    public function complete(Request $request)
{
    $request->validate([
        'selected_plan' => 'required|in:training,practice,industrial',
    ]);

    $step1Data = Session::get('intern_reg_step1');
    $step2Data = Session::get('intern_reg_step2');

    if (!$step1Data) {
        return redirect()->route('intern.register.step1')
            ->with('error', 'Please complete step 1 first.');
    }

    // Create Intern - FIXED: interview_date and interview_time ko empty string/date do
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
        'interview_type' => $step1Data['interview_type'] ?? '',
        'duration' => $step1Data['duration'] ?? '',
        // ✅ FIX: Null nahi, empty string ya current date 
        'interview_date' => null,  // Change this to date if column allows null
        'interview_time' => null,  // Change this to time if column allows null
        'status' => 'interview',
        'intern_type' => $this->mapPlanToInternType($request->selected_plan),
        'join_date' => now(),
    ]);

    // Create Intern Account
    $internAccount = InternAccount::create([
        'int_id' => $intern->id,
        'eti_id' => 'ETI-' . $intern->id,
        'name' => $step1Data['full_name'] ?? '',
        'email' => $step1Data['email'] ?? '',
        'phone' => $step1Data['whatsapp'] ?? '',
        // AFTER (without Hash - plain text)
        'password' => 'default_password_' . $intern->id,
        'int_technology' => $step1Data['technology'] ?? '',
        'int_status' => 'active',
        'portal_status' => 'pending_activation',
        'start_date' => now(),
    ]);

    // ✅ Send Welcome Email to Intern
    try {
        Mail::send('mail.intern_welcome', [
            'name' => $step1Data['full_name'] ?? '',
            'email' => $step1Data['email'] ?? '',
            'eti_id' => 'ETI-' . $intern->id,
            'intern_id' => $intern->id,
            'password' => 'default_password_' . $intern->id,
        ], function($mail) use ($step1Data, $intern) {
            $mail->to($step1Data['email'], $step1Data['full_name'])
                ->subject('Welcome to Ezitech Internship Program!')
                ->from(config('mail.from.address', 'info@ezitech.org'), 'Ezitech Learning Institute');
        });
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Welcome email failed: ' . $e->getMessage());
    }
    
    // ✅ Send Notification to Admin
    $admin = AdminAccount::first();
    if ($admin) {
        try {
            Mail::send('mail.notification', [
                'name' => $admin->name ?? 'Admin',
                'messageBody' => "A new intern has registered:\n\n" .
                    "Name: {$step1Data['full_name']}\n" .
                    "Email: {$step1Data['email']}\n" .
                    "Technology: {$step1Data['technology']}\n" .
                    "ETI ID: ETI-{$intern->id}",
                'action_url' => url('/admin/all-interns'),
            ], function($mail) use ($admin) {
                $mail->to($admin->email, $admin->name)
                    ->subject('New Intern Registration - ' . date('Y-m-d'))
                    ->from(config('mail.from.address', 'info@ezitech.org'), 'Ezitech Learning Institute');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin notification email failed: ' . $e->getMessage());
        }
    }

    Session::forget(['intern_reg_step1', 'intern_reg_step2']);
    
    // ✅ Store registration success data in session for success page
    Session::put('registration_success', [
        'name' => $step1Data['full_name'] ?? '',
        'email' => $step1Data['email'] ?? '',
        'eti_id' => 'ETI-' . $intern->id,
        'selected_plan' => $request->selected_plan,
    ]);

    return redirect()->route('intern.register.success');
}
    
    public function success()
    {
        return view('pages.internship-registration.success');
    }
    
    // ========== RESEND CONFIRMATION EMAIL ==========
    
    public function resendConfirmationEmail(Request $request)
    {
        $registrationData = Session::get('registration_success');
        
        if (!$registrationData) {
            return back()->with('error', 'No registration data found. Please complete your registration first.');
        }
        
        try {
            Mail::send('mail.intern_welcome', [
                'name' => $registrationData['name'],
                'email' => $registrationData['email'],
                'eti_id' => $registrationData['eti_id'],
                'intern_id' => str_replace('ETI-', '', $registrationData['eti_id']),
                'password' => 'default_password_' . str_replace('ETI-', '', $registrationData['eti_id']),
            ], function($mail) use ($registrationData) {
                $mail->to($registrationData['email'], $registrationData['name'])
                    ->subject('Ezitech Internship Program - Confirmation Email')
                    ->from(config('mail.from.address', 'info@ezitech.org'), 'Ezitech Learning Institute');
            });
            
            return back()->with('success', 'Confirmation email has been resent to ' . $registrationData['email']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Resend confirmation email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email. Please try again later or contact support.');
        }
    }
    
    // ========== HELPER METHODS ==========
    
    private function calculateSkillMatch($answers)
    {
        $score = 0;
        $skillMap = [0 => 1, 1 => 2, 2 => 3, 3 => 4];
        
        for ($i = 1; $i <= 6; $i++) {
            $score += $skillMap[$answers['q' . $i] ?? 0] ?? 1;
        }
        
        $percentage = round(($score / 24) * 100);
        return min($percentage, 100);
    }
    
    private function getRecommendedProgram($answers)
    {
        $beginner = 0;
        $intermediate = 0;
        $advanced = 0;
        
        $levelMap = [
            'q1' => [0 => 'beginner', 1 => 'intermediate', 2 => 'intermediate', 3 => 'advanced'],
            'q2' => [0 => 'beginner', 1 => 'beginner', 2 => 'intermediate', 3 => 'advanced'],
            'q3' => [0 => 'beginner', 1 => 'intermediate', 2 => 'intermediate', 3 => 'advanced'],
            'q4' => [0 => 'beginner', 1 => 'intermediate', 2 => 'advanced', 3 => 'advanced'],
            'q5' => [0 => 'beginner', 1 => 'intermediate', 2 => 'intermediate', 3 => 'advanced'],
            'q6' => [0 => 'beginner', 1 => 'intermediate', 2 => 'intermediate', 3 => 'advanced'],
        ];
        
        foreach ($levelMap as $question => $levels) {
            $answer = $answers[$question] ?? 0;
            $level = $levels[$answer] ?? 'beginner';
            
            if ($level === 'beginner') $beginner++;
            if ($level === 'intermediate') $intermediate++;
            if ($level === 'advanced') $advanced++;
        }
        
        // Recommend based on dominant level (most answers in that category)
        // This is fairer than requiring a fixed threshold
        // IMPORTANT: Use >= for tie-breaking (higher level always chosen)
        if ($advanced >= $intermediate && $advanced >= $beginner) {
            return 'industrial';
        } elseif ($intermediate >= $beginner) {
            return 'practice';
        }
        return 'training';
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