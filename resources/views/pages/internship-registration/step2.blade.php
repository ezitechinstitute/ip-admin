@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Intern Registration - Step 2 | Ezitech')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
.progress-bar-space {
    margin-bottom: 2rem;
}

.registration-container {
    max-width: 650px;
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

.question-container {
    margin-bottom: 2rem;
}

.question-label {
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1rem;
}

.question-text {
    color: #333;
}

.question-number {
    display: inline-block;
    background: #696cff;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.5rem;
    font-weight: 600;
    font-size: 0.875rem;
}

.radio-option {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.radio-option:hover {
    border-color: #696cff;
    background: #f5f5ff;
}

.radio-option input[type="radio"] {
    margin-top: 0.25rem;
    margin-right: 1rem;
    cursor: pointer;
}

.radio-option input[type="radio"]:checked + label {
    color: #696cff;
    font-weight: 600;
}

.radio-option.selected {
    border-color: #696cff;
    background: #f5f5ff;
}

.radio-option label {
    flex: 1;
    margin-bottom: 0;
    cursor: pointer;
}

.btn-container {
    display: flex;
    gap: 1rem;
    margin-top: 2.5rem;
}

.btn-container .btn {
    flex: 1;
}

.loading-spinner {
    display: none;
    text-align: center;
    padding: 2rem;
}

.loading-spinner.show {
    display: block;
}

.spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid #e0e0e0;
    border-top-color: #696cff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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

        <!-- Assessment Form -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="registration-container w-100">
                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ route('intern.register.step1') }}" class="btn btn-link btn-sm text-muted">
                        <i class="icon-base ti tabler-arrow-left me-2"></i> Back to Step 1
                    </a>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator progress-bar-space">
                    <div class="step-item completed">
                        <div class="step-number">✓</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step-item active">
                        <div class="step-number">2</div>
                        <div class="step-label">Assessment</div>
                    </div>
                    <div class="step-item">
                        <div class="step-number">3</div>
                        <div class="step-label">Recommendation</div>
                    </div>
                </div>

                <!-- Form Section Title -->
                <div class="form-section-title">
                    <h4>Skill Assessment</h4>
                    <p class="mb-0">Answer the following questions so we can recommend the most suitable internship program.</p>
                </div>

                <!-- Assessment Form -->
                <form id="assessmentForm" action="{{ route('intern.register.step3') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <!-- Question 1 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">1</span>
                            <span class="question-text">How would you describe your current skill level?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="skill_level_0" name="skill_level" value="0" required />
                                <label for="skill_level_0">I am a complete beginner</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="skill_level_1" name="skill_level" value="1" />
                                <label for="skill_level_1">I know basic development concepts</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="skill_level_2" name="skill_level" value="2" />
                                <label for="skill_level_2">I can build small projects myself</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="skill_level_3" name="skill_level" value="3" />
                                <label for="skill_level_3">I have built multiple projects</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">2</span>
                            <span class="question-text">Have you worked on any real projects before?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="real_projects_0" name="real_projects" value="0" required />
                                <label for="real_projects_0">No</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="real_projects_1" name="real_projects" value="1" />
                                <label for="real_projects_1">Only academic projects</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="real_projects_2" name="real_projects" value="2" />
                                <label for="real_projects_2">Personal projects</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="real_projects_3" name="real_projects" value="3" />
                                <label for="real_projects_3">Freelance or client projects</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">3</span>
                            <span class="question-text">How comfortable are you with solving development problems?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="problem_solving_0" name="problem_solving" value="0" required />
                                <label for="problem_solving_0">I struggle with most problems</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="problem_solving_1" name="problem_solving" value="1" />
                                <label for="problem_solving_1">I can solve simple problems</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="problem_solving_2" name="problem_solving" value="2" />
                                <label for="problem_solving_2">I can solve intermediate problems</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="problem_solving_3" name="problem_solving" value="3" />
                                <label for="problem_solving_3">I solve problems confidently</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">4</span>
                            <span class="question-text">What type of support do you expect during the internship?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="support_expectation_0" name="support_expectation" value="0" required />
                                <label for="support_expectation_0">I need a teacher and full guidance</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="support_expectation_1" name="support_expectation" value="1" />
                                <label for="support_expectation_1">I need some guidance but mostly practice</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="support_expectation_2" name="support_expectation" value="2" />
                                <label for="support_expectation_2">I prefer self-learning with project experience</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="support_expectation_3" name="support_expectation" value="3" />
                                <label for="support_expectation_3">I only need a professional work environment</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 5 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">5</span>
                            <span class="question-text">How many hours per week can you dedicate to this internship?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="hours_per_week_0" name="hours_per_week" value="0" required />
                                <label for="hours_per_week_0">5 hours</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="hours_per_week_1" name="hours_per_week" value="1" />
                                <label for="hours_per_week_1">10 hours</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="hours_per_week_2" name="hours_per_week" value="2" />
                                <label for="hours_per_week_2">15 hours</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="hours_per_week_3" name="hours_per_week" value="3" />
                                <label for="hours_per_week_3">20+ hours</label>
                            </div>
                        </div>
                    </div>

                    <!-- Question 6 -->
                    <div class="question-container">
                        <label class="question-label">
                            <span class="question-number">6</span>
                            <span class="question-text">What is your main goal for joining this internship?</span>
                        </label>
                        <div class="question-options">
                            <div class="radio-option">
                                <input type="radio" id="main_goal_0" name="main_goal" value="0" required />
                                <label for="main_goal_0">Learn development from scratch</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="main_goal_1" name="main_goal" value="1" />
                                <label for="main_goal_1">Improve my development skills</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="main_goal_2" name="main_goal" value="2" />
                                <label for="main_goal_2">Build projects for my portfolio</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="main_goal_3" name="main_goal" value="3" />
                                <label for="main_goal_3">Gain real industry exposure</label>
                            </div>
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='{{ route('intern.register.step1') }}'">
                            <i class="icon-base ti tabler-arrow-left me-2"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            Next → View Recommendation <i class="icon-base ti tabler-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <!-- Loading Spinner (hidden initially) -->
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner"></div>
                    <p class="text-muted">Analyzing your answers and preparing the best internship path for you...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Radio option selection effects
    document.querySelectorAll('.radio-option').forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        
        option.addEventListener('click', function() {
            // Remove selected class from siblings
            document.querySelectorAll(`.question-options:has(#${radio.name}) .radio-option`).forEach(sibling => {
                sibling.classList.remove('selected');
            });
            // Add selected class to clicked option
            option.classList.add('selected');
            radio.checked = true;
        });

        radio.addEventListener('change', function() {
            // Remove selected class from siblings
            document.querySelectorAll(`.question-options:has(#${radio.name}) .radio-option`).forEach(sibling => {
                sibling.classList.remove('selected');
            });
            option.classList.add('selected');
        });
    });

    // Form validation and loading spinner
    document.getElementById('assessmentForm').addEventListener('submit', function(event) {
        const form = this;
        
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Show loading spinner, hide form
        document.getElementById('loadingSpinner').classList.add('show');
        document.getElementById('submitBtn').disabled = true;

        // Simulate analysis delay (2 seconds)
        setTimeout(() => {
            form.submit();
        }, 2000);
    });

    // Form validation on radio change
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            this.classList.remove('is-invalid');
        });
    });
</script>
@endsection
