@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Intern Registration - Step 3 | Ezitech')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
.registration-container {
    max-width: 800px;
    margin: 0 auto;
}

.step-indicator {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.step-item {
    flex: 1;
    text-align: center;
    position: relative;
}

.step-item::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 50%;
    right: -50%;
    height: 2px;
    background: #e0e0e0;
}

.step-item:last-child::after {
    display: none;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
    position: relative;
    z-index: 1;
}

.step-item.active .step-number {
    background: #696cff;
    color: white;
}

.step-item.completed .step-number {
    background: #71dd5a;
    color: white;
}

.step-item.completed .step-number {
    font-size: 1.25rem;
}

.step-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 8px;
}

.step-item.active .step-label {
    color: #696cff;
    font-weight: 600;
}

.form-section-title {
    margin-bottom: 1.5rem;
}

.form-section-title h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.25rem;
}

.form-section-title p {
    color: #666;
    margin-bottom: 0.25rem;
}

.recommendation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 0.375rem;
}

.skill-match {
    text-align: right;
}

.skill-match-value {
    font-size: 1.75rem;
    font-weight: bold;
    color: #696cff;
}

.skill-match-label {
    color: #666;
    font-size: 0.875rem;
}

.plan-container {
    margin-bottom: 1.5rem;
}

.plan-card {
    border: 2px solid #e0e0e0;
    border-radius: 0.5rem;
    padding: 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.plan-card:hover {
    border-color: #696cff;
    box-shadow: 0 4px 12px rgba(105, 108, 255, 0.1);
}

.plan-card.recommended {
    border: 3px solid #696cff;
    background: #f5f5ff;
    box-shadow: 0 4px 20px rgba(105, 108, 255, 0.2);
    transform: scale(1.02);
}

.plan-card input[type="radio"] {
    margin-right: 1rem;
    margin-top: 0.25rem;
}

.plan-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.25rem;
}

.plan-badge {
    display: inline-block;
    background: #696cff;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.plan-badge.other {
    background: #e0e0e0;
    color: #666;
}

.plan-title {
    font-weight: 600;
    font-size: 1.1rem;
    color: #333;
    margin-bottom: 0.5rem;
}

.plan-description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.plan-duration-price {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.plan-info {
    font-size: 0.9rem;
}

.plan-info-label {
    color: #999;
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.plan-info-value {
    color: #333;
    font-weight: 600;
}

.plan-features {
    list-style: none;
    padding: 0;
    margin-bottom: 1rem;
}

.plan-features li {
    padding: 0.5rem 0;
    color: #555;
    font-size: 0.9rem;
}

.plan-features li:before {
    content: "✓ ";
    color: #71dd5a;
    font-weight: bold;
    margin-right: 0.5rem;
}

.plan-features li.disabled:before {
    content: "✗ ";
    color: #e0e0e0;
}

.plan-features li.disabled {
    color: #999;
    text-decoration: line-through;
}

.plan-outcomes {
    border-top: 1px solid #e0e0e0;
    padding-top: 1rem;
    margin-top: 1rem;
}

.plan-outcomes-title {
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.75rem;
    color: #333;
}

.plan-outcomes-list {
    list-style: none;
    padding: 0;
    margin-bottom: 0.5rem;
}

.plan-outcomes-list li {
    padding: 0.35rem 0;
    color: #666;
    font-size: 0.875rem;
}

.plan-outcomes-list li:before {
    content: "✔ ";
    color: #71dd5a;
    font-weight: bold;
    margin-right: 0.5rem;
}

.plan-note {
    font-size: 0.825rem;
    color: #666;
    font-style: italic;
    padding: 0.75rem;
    background: #f9f9f9;
    border-left: 3px solid #696cff;
    margin-top: 1rem;
}

.plans-section-title {
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
    color: #333;
}

.trust-line {
    text-align: center;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 0.375rem;
    color: #666;
    font-size: 0.9rem;
    margin: 1.5rem 0;
    font-style: italic;
}

.btn-container {
    display: flex;
    gap: 1rem;
    margin-top: 2.5rem;
}

.btn-container .btn {
    flex: 1;
}

.scroll-container {
    max-height: 650px;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.scroll-container::-webkit-scrollbar {
    width: 6px;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.scroll-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

@media (max-width: 1200px) {
    .registration-container {
        max-width: 100%;
    }
    
    .plan-card.recommended {
        transform: scale(1.01);
    }
}
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/pages-auth.js'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
        <!-- /Left Text -->
        <div class="d-none d-xl-flex col-xl-8 p-0">
            <div class="auth-cover-bg d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/img/illustrations/login-cover-img.png') }}" alt="auth-login-cover" 
                    class="my-5 auth-illustration" style="width: 100%; z-index: 0; height: 100%; object-fit: cover; max-block-size: 100%; max-inline-size: 100%;" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Recommendation -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="registration-container w-100">
                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ route('intern.register.step2') }}" class="btn btn-link btn-sm text-muted">
                        <i class="icon-base ti tabler-arrow-left me-2"></i> Back to Step 2
                    </a>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator" style="margin-bottom: 2rem;">
                    <div class="step-item completed">
                        <div class="step-number">✓</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step-item completed">
                        <div class="step-number">✓</div>
                        <div class="step-label">Assessment</div>
                    </div>
                    <div class="step-item active">
                        <div class="step-number">3</div>
                        <div class="step-label">Recommendation</div>
                    </div>
                </div>

                <!-- Form Section Title -->
                <div class="form-section-title">
                    <h4>Your Recommended Internship Path</h4>
                    <p class="mb-0">Based on your answers, we recommend the following internship program.</p>
                </div>

                <!-- Recommendation Header -->
                <div class="recommendation-header">
                    <div>
                        <strong>Skill Match:</strong> <span class="ms-2">{{ $skillMatch }}%</span>
                    </div>
                    <div class="skill-match">
                        <div class="skill-match-value">{{ $skillMatch }}%</div>
                        <div class="skill-match-label">Your Skill Match</div>
                    </div>
                </div>

                <div class="scroll-container">
                    <form id="planSelectionForm" action="{{ route('intern.register.complete') }}" method="POST">
                        @csrf

                        <!-- Plan 1: Training Internship (Recommended) -->
                        <div class="plan-container">
                            <div class="plan-card {{ $recommended === 'training' ? 'recommended' : '' }}">
                                <div style="display: flex; align-items: flex-start;">
                                    <input type="radio" name="selected_plan" value="training" 
                                        id="plan_training" {{ $recommended === 'training' ? 'checked' : '' }} required />
                                    <div style="flex: 1;">
                                        @if($recommended === 'training')
                                            <div class="plan-badge">Recommended for You</div>
                                        @endif
                                        <div class="plan-title">Training Internship</div>
                                        <div class="plan-description">
                                            Best for students who need proper guidance and mentorship.
                                        </div>

                                        <div class="plan-duration-price">
                                            <div class="plan-info">
                                                <div class="plan-info-label">Duration</div>
                                                <div class="plan-info-value">3 Months</div>
                                            </div>
                                            <div class="plan-info">
                                                <div class="plan-info-label">Fee</div>
                                                <div class="plan-info-value">PKR 6,000</div>
                                            </div>
                                        </div>

                                        <div class="plan-features">
                                            <li>Dedicated Mentor Support</li>
                                            <li>Step-by-Step Training Guidance</li>
                                            <li>Industrial Project Development</li>
                                            <li>Weekly Progress Reviews</li>
                                            <li>Portfolio Project Development</li>
                                            <li>Internship Completion Certificate</li>
                                            <li>Job Opportunity for Top Performers</li>
                                            <li>Offer Letter for University (if required)</li>
                                        </div>

                                        <div class="plan-note">
                                            This recommendation is based on your skill assessment answers. Students with similar skill levels usually choose this program.
                                        </div>

                                        <div class="plan-outcomes">
                                            <div class="plan-outcomes-title">What You Can Achieve</div>
                                            <ul class="plan-outcomes-list">
                                                <li>Build real-world development projects</li>
                                                <li>Understand professional development workflows</li>
                                                <li>Gain experience working on industrial-level tasks</li>
                                                <li>Create portfolio-ready projects</li>
                                                <li>Improve coding and problem-solving skills</li>
                                                <li>Prepare for junior developer opportunities</li>
                                            </ul>
                                            <small class="text-muted" style="font-style: italic;">Students who complete this internship typically build 3–5 portfolio projects.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Other Options Section -->
                        <div class="plans-section-title">Other Internship Options</div>

                        <!-- Plan 2: Project Practice Internship -->
                        <div class="plan-container">
                            <div class="plan-card {{ $recommended === 'practice' ? 'recommended' : '' }}">
                                <div style="display: flex; align-items: flex-start;">
                                    <input type="radio" name="selected_plan" value="practice" 
                                        id="plan_practice" {{ $recommended === 'practice' ? 'checked' : '' }} />
                                    <div style="flex: 1;">
                                        @if($recommended === 'practice')
                                            <div class="plan-badge">Recommended for You</div>
                                        @else
                                            <div class="plan-badge other">Alternative</div>
                                        @endif
                                        <div class="plan-title">Project Practice Internship</div>
                                        <div class="plan-description">
                                            Best for students who already understand basic development concepts.
                                        </div>

                                        <div class="plan-duration-price">
                                            <div class="plan-info">
                                                <div class="plan-info-label">Duration</div>
                                                <div class="plan-info-value">3 Months</div>
                                            </div>
                                            <div class="plan-info">
                                                <div class="plan-info-label">Fee</div>
                                                <div class="plan-info-value">PKR 3,000</div>
                                            </div>
                                        </div>

                                        <div class="plan-features">
                                            <li class="disabled">Dedicated Mentor Support</li>
                                            <li class="disabled">Step-by-Step Training Guidance</li>
                                            <li>Industrial Project Development</li>
                                            <li>Weekly Progress Reviews</li>
                                            <li>Portfolio Project Development</li>
                                            <li>Internship Completion Certificate</li>
                                            <li>Job Opportunity for Top Performers</li>
                                            <li>Offer Letter for University (if required)</li>
                                        </div>

                                        <div class="plan-outcomes">
                                            <div class="plan-outcomes-title">What You Can Achieve</div>
                                            <ul class="plan-outcomes-list">
                                                <li>Gain hands-on experience working on real projects</li>
                                                <li>Strengthen practical development skills</li>
                                                <li>Build strong portfolio projects</li>
                                                <li>Improve debugging and problem-solving ability</li>
                                                <li>Prepare for freelance or entry-level development roles</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Plan 3: Industrial Environment Internship -->
                        <div class="plan-container">
                            <div class="plan-card {{ $recommended === 'industrial' ? 'recommended' : '' }}">
                                <div style="display: flex; align-items: flex-start;">
                                    <input type="radio" name="selected_plan" value="industrial" 
                                        id="plan_industrial" {{ $recommended === 'industrial' ? 'checked' : '' }} />
                                    <div style="flex: 1;">
                                        @if($recommended === 'industrial')
                                            <div class="plan-badge">Recommended for You</div>
                                        @else
                                            <div class="plan-badge other">Alternative</div>
                                        @endif
                                        <div class="plan-title">Industrial Environment Internship</div>
                                        <div class="plan-description">
                                            Best for students who already have development experience.
                                        </div>

                                        <div class="plan-duration-price">
                                            <div class="plan-info">
                                                <div class="plan-info-label">Duration</div>
                                                <div class="plan-info-value">4 Weeks</div>
                                            </div>
                                            <div class="plan-info">
                                                <div class="plan-info-label">Fee</div>
                                                <div class="plan-info-value">PKR 500</div>
                                            </div>
                                        </div>

                                        <div class="plan-features">
                                            <li class="disabled">Dedicated Mentor Support</li>
                                            <li class="disabled">Step-by-Step Training Guidance</li>
                                            <li>Industrial Project Access</li>
                                            <li class="disabled">Weekly Progress Reviews</li>
                                            <li>Real Development Environment</li>
                                            <li>Experience Letter</li>
                                            <li class="disabled">Job Opportunity for Top Performers</li>
                                            <li class="disabled">Offer Letter for University</li>
                                        </div>

                                        <div class="plan-outcomes">
                                            <div class="plan-outcomes-title">What You Can Achieve</div>
                                            <ul class="plan-outcomes-list">
                                                <li>Experience working in a real development environment</li>
                                                <li>Understand team collaboration workflow</li>
                                                <li>Gain exposure to real project structures</li>
                                                <li>Improve confidence working with production-level code</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="trust-line">
                            ⭐ This recommendation is personalized based on your assessment. You can still choose any program that fits your goals.
                        </div>

                        <div class="btn-container">
                            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('intern.register.step2') }}'">
                                <i class="icon-base ti tabler-arrow-left me-2"></i> Back
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Continue Registration <i class="icon-base ti tabler-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Plan selection interactivity
    document.querySelectorAll('input[name="selected_plan"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Reset all cards
            document.querySelectorAll('.plan-card').forEach(card => {
                card.classList.remove('recommended');
            });
            // Add recommended class to selected plan's card
            this.closest('.plan-card').classList.add('recommended');
        });
    });

    // Initialize checked plan card
    const checkedPlan = document.querySelector('input[name="selected_plan"]:checked');
    if (checkedPlan) {
        checkedPlan.closest('.plan-card').classList.add('recommended');
    }
</script>
@endsection
