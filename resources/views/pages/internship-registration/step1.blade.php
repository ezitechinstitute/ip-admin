@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Intern Registration - Step 1 | Ezitech')

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
    max-width: 600px;
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

.form-note {
    background: #f5f5f5;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    color: #666;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.row.g-3 > [class*='col-'] {
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

.btn-container {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-container .btn {
    flex: 1;
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

        <!-- Registration Form -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="registration-container w-100">
                <!-- Back Link -->
                <div class="mb-4">
                    <a href="{{ url('/') }}" class="btn btn-link btn-sm text-muted">
                        <i class="icon-base ti tabler-arrow-left me-2"></i> Back to Login
                    </a>
                </div>

                <!-- Step Indicator -->
                <div class="step-indicator progress-bar-space">
                    <div class="step-item active">
                        <div class="step-number">1</div>
                        <div class="step-label">Basic Info</div>
                    </div>
                    <div class="step-item">
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
                    <h4>Ezitech Internship Application</h4>
                    <p class="mb-0">Complete this quick assessment to find the best internship path for you.</p>
                </div>

                <div class="form-note">
                    <i class="icon-base ti tabler-info-circle me-2"></i>
                    This assessment takes about 3–4 minutes and helps us recommend the best internship program based on your skills.
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endforeach
                @endif

                <!-- Form -->
                <form action="{{ route('intern.register.step2') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                id="full_name" name="full_name" placeholder="Enter your full name" 
                                value="{{ old('full_name') }}" required />
                            @error('full_name')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" placeholder="Enter your email" 
                                value="{{ old('email') }}" required />
                            @error('email')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                id="country" name="country" placeholder="Enter your country" 
                                value="{{ old('country') }}" required />
                            @error('country')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                id="city" name="city" placeholder="Enter your city" 
                                value="{{ old('city') }}" required />
                            @error('city')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="whatsapp" class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('whatsapp') is-invalid @enderror" 
                                id="whatsapp" name="whatsapp" placeholder="Enter your WhatsApp number" 
                                value="{{ old('whatsapp') }}" required />
                            @error('whatsapp')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" 
                                id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required />
                            @error('date_of_birth')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="university" class="form-label">University / Institute <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('university') is-invalid @enderror" 
                                id="university" name="university" placeholder="Enter your university or institute" 
                                value="{{ old('university') }}" required />
                            @error('university')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="technology" class="form-label">Technology Interest <span class="text-danger">*</span></label>
                            <select class="form-select @error('technology') is-invalid @enderror" 
                                id="technology" name="technology" required>
                                <option value="">Select Technology</option>
                                <option value="MERN Stack" {{ old('technology') == 'MERN Stack' ? 'selected' : '' }}>MERN Stack</option>
                                <option value="Frontend Development" {{ old('technology') == 'Frontend Development' ? 'selected' : '' }}>Frontend Development</option>
                                <option value="Backend Development" {{ old('technology') == 'Backend Development' ? 'selected' : '' }}>Backend Development</option>
                                <option value="Python Development" {{ old('technology') == 'Python Development' ? 'selected' : '' }}>Python Development</option>
                                <option value="UI/UX Design" {{ old('technology') == 'UI/UX Design' ? 'selected' : '' }}>UI/UX Design</option>
                                <option value="Android Development" {{ old('technology') == 'Android Development' ? 'selected' : '' }}>Android Development</option>
                                <option value="iOS Development" {{ old('technology') == 'iOS Development' ? 'selected' : '' }}>iOS Development</option>
                                <option value="DevOps" {{ old('technology') == 'DevOps' ? 'selected' : '' }}>DevOps</option>
                            </select>
                            @error('technology')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="profile_image" class="form-label">Profile Image (Optional)</label>
                            <input type="file" class="form-control @error('profile_image') is-invalid @enderror" 
                                id="profile_image" name="profile_image" accept="image/*" />
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                            @error('profile_image')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">
                            Next → Skill Assessment <i class="icon-base ti tabler-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <p class="text-center mt-6 text-muted">
                    Already have an account? <a href="{{ url('/') }}">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endsection
