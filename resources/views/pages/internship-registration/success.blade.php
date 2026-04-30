@php
$customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/blankLayout')

@section('title', 'Registration Successful | Ezitech')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
<style>
.success-container {
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: #71dd5a;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
    animation: scaleIn 0.5s ease-out;
}

.success-icon i {
    color: white;
    font-size: 3rem;
}

@keyframes scaleIn {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.success-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
}

.success-description {
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.next-steps {
    background: #f5f5f5;
    padding: 1.5rem;
    border-radius: 0.5rem;
    margin-bottom: 2rem;
    text-align: left;
}

.next-steps-title {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.next-steps-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.next-steps-list li {
    padding: 0.75rem 0;
    color: #555;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: flex-start;
}

.next-steps-list li:last-child {
    border-bottom: none;
}

.next-steps-number {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    background: #696cff;
    color: white;
    border-radius: 50%;
    margin-right: 1rem;
    flex-shrink: 0;
    font-weight: 600;
    font-size: 0.875rem;
}

.info-box {
    background: #fffacd;
    border-left: 4px solid #ffc107;
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 2rem;
    text-align: left;
}

.info-box i {
    color: #ff9800;
    margin-right: 0.5rem;
}

.info-box-text {
    color: #855a00;
    font-size: 0.9rem;
}

.btn-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-container .btn {
    padding: 0.75rem 1.5rem;
}

.contact-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e0e0e0;
    color: #666;
    font-size: 0.9rem;
}

.contact-info strong {
    display: block;
    margin-bottom: 0.5rem;
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
                <img src="{{ asset('assets/img/illustrations/login-cover-img.png') }}" alt="auth-registration-success" 
                    class="my-5 auth-illustration" style="width: 100%; z-index: 0; height: 100%; object-fit: cover; max-block-size: 100%; max-inline-size: 100%;" />
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Success Message -->
        <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
            <div class="success-container w-100">
                @if(session('success'))
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 1rem; margin-bottom: 2rem; text-align: left;">
                        <div style="color: #155724; font-size: 0.95rem;">
                            <i class="icon-base ti tabler-check" style="color: #28a745; margin-right: 0.5rem;"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 1rem; margin-bottom: 2rem; text-align: left;">
                        <div style="color: #721c24; font-size: 0.95rem;">
                            <i class="icon-base ti tabler-alert-circle" style="color: #dc3545; margin-right: 0.5rem;"></i>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                <div class="success-icon">
                    <i class="icon-base ti tabler-check"></i>
                </div>

                <h4 class="success-title">Registration Completed!</h4>

                <p class="success-description">
                    Congratulations! Your internship registration has been successfully submitted. We've received your information and skill assessment.
                </p>

                @if(session('registration_success'))
                    <div style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 1rem; margin-bottom: 2rem; text-align: left;">
                        <div style="font-weight: 600; color: #0066cc; margin-bottom: 0.5rem;">Your Registration Details</div>
                        <div style="color: #333; font-size: 0.95rem; line-height: 1.8;">
                            <div><strong>Name:</strong> {{ session('registration_success.name') }}</div>
                            <div><strong>Email:</strong> {{ session('registration_success.email') }}</div>
                            <div style="background: #f0f8ff; padding: 0.75rem; border-radius: 4px; margin-top: 0.75rem;">
                                <div style="color: #0066cc; font-size: 0.85rem;">Your ETI (Ezitech Trainee ID)</div>
                                <div style="font-size: 1.25rem; font-weight: 700; color: #0047b3; font-family: monospace;">{{ session('registration_success.eti_id') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="info-box">
                    <i class="icon-base ti tabler-info-circle"></i>
                    <span class="info-box-text">
                        Your account has been created. Check your email for login credentials and next steps.
                    </span>
                </div>

                <div class="next-steps">
                    <div class="next-steps-title">What Happens Next?</div>
                    <ul class="next-steps-list">
                        <li>
                            <span class="next-steps-number">1</span>
                            <span>Check your email for registration confirmation and login details</span>
                        </li>
                        <li>
                            <span class="next-steps-number">2</span>
                            <span>Our team will review your assessment and skill level</span>
                        </li>
                        <li>
                            <span class="next-steps-number">3</span>
                            <span>You'll receive an interview call within 24-48 hours</span>
                        </li>
                        <li>
                            <span class="next-steps-number">4</span>
                            <span>After interview approval, you can start your internship</span>
                        </li>
                    </ul>
                </div>

                <div class="btn-container">
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        Back to Login <i class="icon-base ti tabler-arrow-right ms-2"></i>
                    </a>
                    <form action="{{ route('resend-confirmation-email') }}" method="POST" style="display: inline; width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary w-100">
                            Resend Confirmation Email
                        </button>
                    </form>
                </div>

                <div class="contact-info">
                    <strong>Need Help?</strong>
                    <p>If you don't receive your confirmation email within a few minutes, please check your spam folder or contact our support team.</p>
                    <p>📧 support@ezitech.io<br/>
                    📱 WhatsApp: +92 (xxx) xxx-xxxx</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
