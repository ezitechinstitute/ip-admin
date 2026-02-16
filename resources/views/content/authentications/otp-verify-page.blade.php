@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts.blankLayout')
@section('title', 'Verify OTP - Pages')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
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
  <a href="{{ url('/') }}" class="app-brand auth-cover-brand">
    @php
  $settings = \App\Models\AdminSetting::first();
  $dynamicLogo = $settings && $settings->system_logo 
                 ? asset($settings->system_logo) 
                 : asset('assets/img/branding/logo.png');
@endphp
    <span class="app-brand-logo demo"><img src="{{ $dynamicLogo }}" class="logo-full" style="width: 160px;"></span>
    {{-- <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span> --}}
  </a>
  <div class="authentication-inner row m-0">
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img src="{{ asset('assets/img/illustrations/auth-verify-email-illustration-' . $configData['theme'] . '.png') }}" alt="auth-verify-otp-cover" class="my-5 auth-illustration d-lg-block d-none" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}" alt="auth-verify-otp-cover" class="platform-bg" />
      </div>
    </div>
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-sm-12 p-6">
      <div class="w-px-400 mx-auto mt-12 mt-5">
        
        {{-- Success/Error Messages --}}
        @if($errors->any() || session('success') || session('error'))
          <div class="alert alert-{{ session('success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('error') ?? $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
          <div id="ajax-alert-container" class="d-none">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span id="ajax-alert-message"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
        <h4 class="mb-1">Verify OTP 💬</h4>
        <p class="text-start mb-6">
          We sent a 6-digit code to <strong>{{ $email }}</strong>. <br>
          Enter the code below to continue.
        </p>

        <form id="otpForm" class="mb-6" action="{{ route('auth.otp.submit') }}" method="POST">
          @csrf
          <input type="hidden" name="email" value="{{ $email }}">
          
          <div class="mb-6">
            <div class="auth-input-wrapper d-flex align-items-center justify-content-between gap-2">
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" autofocus required pattern="\d*"/>
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" required pattern="\d*"/>
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" required pattern="\d*"/>
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" required pattern="\d*"/>
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" required pattern="\d*"/>
              <input type="text" name="otp[]" class="form-control auth-input h-px-50 text-center otp-input" maxlength="1" required pattern="\d*"/>
            </div>
          </div>

          <button type="submit" class="btn btn-primary d-grid w-100 mb-6">Verify OTP</button>
        </form>

       <div class="text-center">
  Didn't get the code? 
  <a href="javascript:void(0);" id="resendOtpBtn" class="resend-link">Resend</a>
  <span id="timerText" class="text-muted d-none"> (Wait <span id="seconds">30</span>s)</span>
</div>
      </div>
    </div>
    </div>
</div>

{{-- JavaScript for Auto-Focus --}}
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const inputs = document.querySelectorAll('.otp-input');

      inputs.forEach((input, index) => {
          // English comments: Move to next box on typing a digit
          input.addEventListener('input', (e) => {
              if (e.target.value.length === 1 && index < inputs.length - 1) {
                  inputs[index + 1].focus();
              }
          });

          // English comments: Move back on backspace if box is empty
          input.addEventListener('keydown', (e) => {
              if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                  inputs[index - 1].focus();
              }
          });
      });
  });
</script>


<script>
// English comments: Handling the Resend OTP click via AJAX
document.getElementById('resendOtpBtn').addEventListener('click', function(e) {
    e.preventDefault();
    const btn = this;
    const email = "{{ $email }}";
    const alertContainer = document.getElementById('ajax-alert-container');
    const alertMessage = document.getElementById('ajax-alert-message');
    
    btn.style.pointerEvents = "none";
    btn.style.opacity = "0.5";

    fetch("{{ route('auth.otp.resend') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // English comments: Show the green message above the form instead of browser alert
            alertMessage.innerText = data.message;
            alertContainer.classList.remove('d-none');
            
            // English comments: Auto-hide the green message after 5 seconds
            setTimeout(() => {
                alertContainer.classList.add('d-none');
            }, 5000);

            startTimer(); 
        } else {
            // English comments: If error, you can change alert class to danger here
            alertMessage.innerText = "Error: " + data.message;
            alertContainer.querySelector('.alert').classList.replace('alert-success', 'alert-danger');
            alertContainer.classList.remove('d-none');
            
            btn.style.pointerEvents = "auto";
            btn.style.opacity = "1";
        }
    })
    .catch(error => {
        console.error("Error:", error);
        btn.style.pointerEvents = "auto";
        btn.style.opacity = "1";
    });
});
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.otp-input');

    // English comments: Handle pasting the entire 6-digit OTP
    inputs[0].addEventListener('paste', (e) => {
        e.preventDefault();
        const data = e.clipboardData.getData('text').slice(0, inputs.length); // English comments: Get first 6 chars
        const digits = data.split('');

        digits.forEach((digit, index) => {
            if (inputs[index]) {
                inputs[index].value = digit;
            }
        });

        // English comments: Focus the last filled input or the next empty one
        const lastIndex = Math.min(digits.length, inputs.length - 1);
        inputs[lastIndex].focus();
    });

    inputs.forEach((input, index) => {
        // English comments: Move to next box on typing a digit
        input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // English comments: Move back on backspace if box is empty
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
});
</script>
@endsection