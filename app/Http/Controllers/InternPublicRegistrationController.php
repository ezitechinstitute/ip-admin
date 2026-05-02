<?php

namespace App\Http\Controllers;
use App\Models\AdminAccount;
use App\Models\Intern;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\UnifiedNotificationService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

        // 1. Create Intern Record (Stores application and interview data)
        $intern = \App\Models\Intern::create([
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
            'interview_date' => null, 
            'interview_time' => null, 
            'status' => 'interview',
            'intern_type' => $this->mapPlanToInternType($request->selected_plan),
            'join_date' => now(),
        ]);

        // 2. Create Unified User (Replacing the old InternAccount)
        // This allows the intern to log in via the main 'users' table.
        $user = \App\Models\User::create([
            'name' => $step1Data['full_name'] ?? '',
            'email' => $step1Data['email'] ?? '',
            // Password must be hashed for the new User model.
            'password' => \Illuminate\Support\Facades\Hash::make('default_password_' . $intern->id),
            'role' => 'intern',
            'legacy_intern_id' => $intern->id, // Keeps the link to the Intern application
            'eti_id' => 'ETI-' . $intern->id,
            'int_technology' => $step1Data['technology'] ?? '',
            'portal_status' => 'pending_activation',
            
            // 3. Assign Default Modules
            // These slugs allow the intern to access specific features.
            'assigned_modules' => [
                'intern.dashboard',
                'intern.tasks',
                'intern.profile',
                'intern.invoices'
            ],
        ]);

        // 4. Send email to intern (AFTER data saved, BEFORE redirect)
        $notificationService = app(UnifiedNotificationService::class);
        
        $notificationService->send(
            $intern,
            'intern',
            'registration_complete',
            'Welcome to Ezitech Internship Program!',
            "Dear {$step1Data['full_name']},\n\n" .
            "Thank you for registering with Ezitech Internship Program.\n\n" .
            "Your registration has been received successfully.\n" .
            "Your ETI ID: ETI-{$intern->id}\n\n" .
            "Our team will review your application and contact you soon for an interview.\n\n" .
            "Best regards,\nEzitech Team",
            ['action_url' => '/intern/login']
        );
        
        // 5. Send email to Admin
        $admin = \App\Models\AdminAccount::first();
        if ($admin) {
            $notificationService->send(
                $admin,
                'admin',
                'new_registration',
                'New Intern Registration',
                "A new intern has registered:\n" .
                "Name: {$step1Data['full_name']}\n" .
                "Email: {$step1Data['email']}\n" .
                "Technology: {$step1Data['technology']}",
                ['action_url' => '/admin/all-interns']
            );
        }

        Session::forget(['intern_reg_step1', 'intern_reg_step2']);
        
        // Store registration success data in session for success page
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