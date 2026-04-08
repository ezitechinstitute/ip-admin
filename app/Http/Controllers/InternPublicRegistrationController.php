<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use App\Models\InternAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;

class InternPublicRegistrationController extends Controller
{
    /**
     * Step 1: Display basic information form
     */
    public function step1()
    {
        // Clear any previous session data
        Session::forget(['intern_reg_step1', 'intern_reg_step2']);
        return view('pages.internship-registration.step1');
    }

    /**
     * Step 2: Save step 1 data and show skill assessment form
     */
    public function step2(Request $request)
    {
        // Validate step 1 data
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:intern_accounts,email',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'gender' => 'required|in:Male,Female,Other',
            'date_of_birth' => 'required|date',
            'university' => 'required|string|max:255',
            'technology' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('interns', 'public');
            $validated['profile_image'] = $imagePath;
        }

        // Save step 1 data to session
        Session::put('intern_reg_step1', $validated);

        return view('pages.internship-registration.step2');
    }

    /**
     * Step 3: Save skill assessment and calculate recommendation
     */
    public function step3(Request $request)
    {
        $request->validate([
            'skill_level' => 'required|in:0,1,2,3',
            'real_projects' => 'required|in:0,1,2,3',
            'problem_solving' => 'required|in:0,1,2,3',
            'support_expectation' => 'required|in:0,1,2,3',
            'hours_per_week' => 'required|in:0,1,2,3',
            'main_goal' => 'required|in:0,1,2,3',
        ]);

        // Calculate skill scores based on answers
        $beginner = 0;
        $intermediate = 0;
        $advanced = 0;

        // Question 1: Skill level mapping
        $skillLevelMap = ['beginner', 'intermediate', 'intermediate', 'advanced'];
        $level = intval($request->skill_level);
        $this->incrementScore($skillLevelMap[$level], $beginner, $intermediate, $advanced);

        // Question 2: Real projects mapping
        $realProjectsMap = ['beginner', 'beginner', 'intermediate', 'advanced'];
        $level = intval($request->real_projects);
        $this->incrementScore($realProjectsMap[$level], $beginner, $intermediate, $advanced);

        // Question 3: Problem solving mapping
        $problemSolvingMap = ['beginner', 'intermediate', 'intermediate', 'advanced'];
        $level = intval($request->problem_solving);
        $this->incrementScore($problemSolvingMap[$level], $beginner, $intermediate, $advanced);

        // Question 4: Support expectation mapping
        $supportMap = ['beginner', 'intermediate', 'advanced', 'advanced'];
        $level = intval($request->support_expectation);
        $this->incrementScore($supportMap[$level], $beginner, $intermediate, $advanced);

        // Question 5: Hours per week mapping
        $hoursMap = ['beginner', 'intermediate', 'intermediate', 'advanced'];
        $level = intval($request->hours_per_week);
        $this->incrementScore($hoursMap[$level], $beginner, $intermediate, $advanced);

        // Question 6: Main goal mapping
        $goalMap = ['beginner', 'intermediate', 'intermediate', 'advanced'];
        $level = intval($request->main_goal);
        $this->incrementScore($goalMap[$level], $beginner, $intermediate, $advanced);

        // Determine recommendation based on scores
        $recommended = $this->getRecommendation($beginner, $intermediate, $advanced);
        
        // Calculate skill match percentage
        $totalScore = $beginner + $intermediate + $advanced; // Total is 6 (one per question)
        // Normalize to percentage: advanced = 100%, intermediate = 65%, beginner = 33%
        $skillMatch = round((($beginner * 33) + ($intermediate * 65) + ($advanced * 100)) / (6 * 100) * 100);

        // Save step 2 data to session
        Session::put('intern_reg_step2', [
            'skill_level' => $request->skill_level,
            'real_projects' => $request->real_projects,
            'problem_solving' => $request->problem_solving,
            'support_expectation' => $request->support_expectation,
            'hours_per_week' => $request->hours_per_week,
            'main_goal' => $request->main_goal,
            'beginner' => $beginner,
            'intermediate' => $intermediate,
            'advanced' => $advanced,
        ]);

        return view('pages.internship-registration.step3', [
            'recommended' => $recommended,
            'skillMatch' => $skillMatch,
            'beginner' => $beginner,
            'intermediate' => $intermediate,
            'advanced' => $advanced,
        ]);
    }

    /**
     * Complete registration: Create intern account
     */
    public function complete(Request $request)
    {
        $request->validate([
            'selected_plan' => 'required|in:training,practice,industrial',
        ]);

        // Get data from session
        $step1Data = Session::get('intern_reg_step1');
        $step2Data = Session::get('intern_reg_step2');

        if (!$step1Data || !$step2Data) {
            return redirect()->route('intern.register.step1')
                ->withErrors(['error' => 'Registration session expired. Please start again.']);
        }

        // Create intern record
        $intern = Intern::create([
            'name' => $step1Data['full_name'],
            'email' => $step1Data['email'],
            'city' => $step1Data['city'],
            'country' => $step1Data['country'],
            'gender' => $step1Data['gender'],
            'birth_date' => $step1Data['date_of_birth'],
            'university' => $step1Data['university'],
            'technology' => $step1Data['technology'],
            'phone' => $step1Data['whatsapp'],
            'image' => $step1Data['profile_image'] ?? '', // Empty string if no image uploaded
            'cnic' => '', // Not collected in registration, set as empty
            'interview_type' => '', // Will be set during interview
            'duration' => '', // Will be set when internship starts
            'interview_date' => '', // Will be scheduled
            'interview_time' => '', // Will be scheduled
            'status' => 'interview', // New registrations start in interview status
            'intern_type' => $this->mapPlanToInternType($request->selected_plan),
            'join_date' => now(),
            'created_at' => now(),
        ]);

        // Create intern account
        $internAccount = InternAccount::create([
            'int_id' => $intern->id,
            'eti_id' => '', // Will be assigned by admin if needed
            'name' => $step1Data['full_name'],
            'email' => $step1Data['email'],
            'phone' => $step1Data['whatsapp'], // Phone from step 1
            'password' => Hash::make('default_password_' . $intern->id), // Default password
            'int_technology' => $step1Data['technology'],
            'int_status' => 'active',
            'portal_status' => 'pending_activation', // Account pending manager activation
            'start_date' => now(),
        ]);

        // Clear session data
        Session::forget(['intern_reg_step1', 'intern_reg_step2']);

        return redirect()->route('intern.register.success')
            ->with('success', 'Registration completed successfully! Your account has been created.');
    }

    /**
     * Success page after registration
     */
    public function success()
    {
        return view('pages.internship-registration.success');
    }

    /**
     * Helper: Increment score based on category
     */
    private function incrementScore($category, &$beginner, &$intermediate, &$advanced)
    {
        if ($category === 'beginner') {
            $beginner++;
        } elseif ($category === 'intermediate') {
            $intermediate++;
        } elseif ($category === 'advanced') {
            $advanced++;
        }
    }

    /**
     * Helper: Get recommendation based on scores
     * Tie-breaking rule: Intermediate < Advanced (higher level wins)
     */
    private function getRecommendation($beginner, $intermediate, $advanced)
    {
        if ($advanced > $intermediate && $advanced > $beginner) {
            return 'industrial';
        } elseif ($intermediate >= $beginner) {
            // Intermediate is higher or equal to beginner
            return 'practice';
        } else {
            return 'training';
        }
    }

    /**
     * Helper: Map plan selection to intern type
     */
    private function mapPlanToInternType($plan)
    {
        return match($plan) {
            'training' => 'Training Internship',
            'practice' => 'Project Practice',
            'industrial' => 'Industrial Environment',
            default => 'Training Internship',
        };
    }
}
