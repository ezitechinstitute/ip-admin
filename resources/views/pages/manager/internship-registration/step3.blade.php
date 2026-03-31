@extends('layouts.app')
@section('content')
<div class="container">
    <div class="progress-bar mb-4">
        <div class="progress-step completed">Step 1</div>
        <div class="progress-step completed">Step 2</div>
        <div class="progress-step active">Step 3</div>
    </div>
    <h2>Your Recommended Internship Path</h2>
    <p>Based on your answers, we recommend the following internship program.</p>
    <div class="recommendation-section">
        @if($recommended == 'training')
            <div class="card recommended">
                <span class="badge badge-primary">Recommended for You</span>
                <h3>Training Internship</h3>
                <ul>
                    <li>✔ Dedicated Mentor Support</li>
                    <li>✔ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Development</li>
                    <li>✔ Weekly Progress Reviews</li>
                    <li>✔ Portfolio Project Development</li>
                    <li>✔ Internship Completion Certificate</li>
                    <li>✔ Job Opportunity for Top Performers</li>
                    <li>✔ Offer Letter for University (if required)</li>
                </ul>
                <strong>Duration:</strong> 3 Months<br>
                <strong>Fee:</strong> PKR 6000<br>
                <button class="btn btn-success">Continue with this Program</button>
                <div class="skill-match">Skill Match: {{ max($beginner, $intermediate, $advanced) * 16 }}%</div>
            </div>
        @elseif($recommended == 'practice')
            <div class="card recommended">
                <span class="badge badge-primary">Recommended for You</span>
                <h3>Project Practice Internship</h3>
                <ul>
                    <li>✖ Dedicated Mentor Support</li>
                    <li>✖ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Development</li>
                    <li>✔ Weekly Progress Reviews</li>
                    <li>✔ Portfolio Project Development</li>
                    <li>✔ Internship Completion Certificate</li>
                    <li>✔ Job Opportunity for Top Performers</li>
                    <li>✔ Offer Letter for University (if required)</li>
                </ul>
                <strong>Fee:</strong> PKR 3000<br>
                <button class="btn btn-success">Continue with this Program</button>
                <div class="skill-match">Skill Match: {{ max($beginner, $intermediate, $advanced) * 16 }}%</div>
            </div>
        @else
            <div class="card recommended">
                <span class="badge badge-primary">Recommended for You</span>
                <h3>Industrial Environment Internship</h3>
                <ul>
                    <li>✖ Dedicated Mentor Support</li>
                    <li>✖ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Access</li>
                    <li>✖ Weekly Progress Reviews</li>
                    <li>✔ Real Development Environment</li>
                    <li>✔ Experience Letter</li>
                    <li>✖ Job Opportunity for Top Performers</li>
                    <li>✖ Offer Letter for University</li>
                </ul>
                <strong>Duration:</strong> 4 Weeks<br>
                <strong>Platform Fee:</strong> PKR 500<br>
                <button class="btn btn-success">Continue with this Program</button>
                <div class="skill-match">Skill Match: {{ max($beginner, $intermediate, $advanced) * 16 }}%</div>
            </div>
        @endif
        <div class="other-options mt-4">
            <h4>Other Internship Options</h4>
            <div class="card small">
                <h5>Training Internship</h5>
                <ul>
                    <li>✔ Dedicated Mentor Support</li>
                    <li>✔ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Development</li>
                    <li>✔ Weekly Progress Reviews</li>
                    <li>✔ Portfolio Project Development</li>
                    <li>✔ Internship Completion Certificate</li>
                    <li>✔ Job Opportunity for Top Performers</li>
                    <li>✔ Offer Letter for University (if required)</li>
                </ul>
                <strong>Duration:</strong> 3 Months<br>
                <strong>Fee:</strong> PKR 6000<br>
            </div>
            <div class="card small">
                <h5>Project Practice Internship</h5>
                <ul>
                    <li>✖ Dedicated Mentor Support</li>
                    <li>✖ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Development</li>
                    <li>✔ Weekly Progress Reviews</li>
                    <li>✔ Portfolio Project Development</li>
                    <li>✔ Internship Completion Certificate</li>
                    <li>✔ Job Opportunity for Top Performers</li>
                    <li>✔ Offer Letter for University (if required)</li>
                </ul>
                <strong>Fee:</strong> PKR 3000<br>
            </div>
            <div class="card small">
                <h5>Industrial Environment Internship</h5>
                <ul>
                    <li>✖ Dedicated Mentor Support</li>
                    <li>✖ Step-by-Step Training Guidance</li>
                    <li>✔ Industrial Project Access</li>
                    <li>✖ Weekly Progress Reviews</li>
                    <li>✔ Real Development Environment</li>
                    <li>✔ Experience Letter</li>
                    <li>✖ Job Opportunity for Top Performers</li>
                    <li>✖ Offer Letter for University</li>
                </ul>
                <strong>Duration:</strong> 4 Weeks<br>
                <strong>Platform Fee:</strong> PKR 500<br>
            </div>
        </div>
        <div class="outcome mt-4">
            <h4>What You Can Achieve in This Internship</h4>
            <ul>
                <li>✔ Build real-world development projects</li>
                <li>✔ Understand professional development workflows</li>
                <li>✔ Gain experience working on industrial-level tasks</li>
                <li>✔ Create portfolio-ready projects</li>
                <li>✔ Improve coding and problem-solving skills</li>
                <li>✔ Prepare for junior developer opportunities</li>
            </ul>
            <small>Students who complete this internship typically build 3–5 portfolio projects.</small>
        </div>
        <button class="btn btn-primary mt-4">Continue Registration</button>
    </div>
</div>
@endsection