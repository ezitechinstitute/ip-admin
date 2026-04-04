<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InternshipRegistrationController extends Controller
{
    public function step1()
    {
        return view('pages.manager.internship-registration.step1');
    }

    public function step2(Request $request)
    {
        // Save step 1 data to session
        Session::put('internship_step1', $request->all());
        return view('pages.manager.internship-registration.step2');
    }

    public function step3(Request $request)
    {
        // Skill assessment logic
        $beginner = 0;
        $intermediate = 0;
        $advanced = 0;
        $answers = $request->all();

        // Map answers to scores
        $mapping = [
            'skill_level' => ['beginner', 'intermediate', 'intermediate', 'advanced'],
            'real_projects' => ['beginner', 'beginner', 'intermediate', 'advanced'],
            'problem_solving' => ['beginner', 'intermediate', 'intermediate', 'advanced'],
            'support_expectation' => ['beginner', 'intermediate', 'advanced', 'advanced'],
            'hours_per_week' => ['beginner', 'intermediate', 'intermediate', 'advanced'],
            'main_goal' => ['beginner', 'intermediate', 'intermediate', 'advanced'],
        ];

        foreach ($mapping as $question => $categories) {
            $answer = $answers[$question] ?? null;
            if ($answer !== null) {
                $index = intval($answer);
                if ($categories[$index] === 'beginner') $beginner++;
                if ($categories[$index] === 'intermediate') $intermediate++;
                if ($categories[$index] === 'advanced') $advanced++;
            }
        }

        // Decide recommendation
        $recommended = 'training';
        if ($advanced > $intermediate && $advanced > $beginner) {
            $recommended = 'industrial';
        } elseif ($intermediate >= $beginner && $intermediate >= $advanced) {
            $recommended = 'practice';
        }

        return view('pages.manager.internship-registration.step3', [
            'recommended' => $recommended,
            'beginner' => $beginner,
            'intermediate' => $intermediate,
            'advanced' => $advanced,
        ]);
    }
}
