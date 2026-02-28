@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

{{-- English comments: Use layoutBlank to remove admin sidebar and navbar --}}
@extends('layouts.blankLayout')

@section('title', 'Set New Password')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
  'resources/assets/vendor/libs/pickr/pickr-themes.scss'
])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
  'resources/assets/vendor/libs/pickr/pickr.js'
])
@endsection

@section('page-script')
@vite([
    'resources/assets/js/pages-auth.js',
    'resources/assets/js/forms-pickers.js'
])
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
  </a>
  <div class="authentication-inner row m-0">
    <div class="d-none d-xl-flex col-xl-8 p-0">
      <div class="auth-cover-bg d-flex justify-content-center align-items-center">
        <img
          src="{{ asset('assets/img/illustrations/auth-reset-password-illustration-' . $configData['theme'] . '.png') }}"
          alt="auth-reset-password-cover" class="my-5 auth-illustration"
          data-app-light-img="illustrations/auth-reset-password-illustration-light.png"
          data-app-dark-img="illustrations/auth-reset-password-illustration-dark.png" />
        <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}"
          alt="auth-reset-password-cover" class="platform-bg"
          data-app-light-img="illustrations/bg-shape-image-light.png"
          data-app-dark-img="illustrations/bg-shape-image-dark.png" />
      </div>
    </div>
    <div class="d-flex col-12 col-xl-4 align-items-center authentication-bg p-6 p-sm-12">
      <div class="w-px-400 mx-auto mt-12 pt-5">
        <h4 class="mb-1">Set Your Password 🔒</h4>
        <p class="mb-6">Enter your new password below to secure your manager account.</p>

        @if($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form id="formAuthentication" class="mb-6" action="{{ route('auth.password.update.set.new') }}" method="POST">
          @csrf
         
          <input type="hidden" name="m_email" value="{{ $email }}" />
          {{-- <div class="mb-6 form-control-validation">
            <label class="form-label" for="edit_email">Email</label>
            <input type="text" id="edit_email" name="email" class="form-control"
              placeholder="Enter manager email" />
          </div> --}}
          <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="password">New Password</label>
            <div class="input-group input-group-merge">
              <input type="password" id="password" class="form-control" name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
          </div>
          <div class="mb-6 form-password-toggle form-control-validation">
            <label class="form-label" for="confirm-password">Confirm Password</label>
            <div class="input-group input-group-merge">
              <input type="password" id="confirm-password" class="form-control" name="password_confirmation"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required />
              <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
            </div>
          </div>
          {{-- Join Date Field --}}
          <div class="form-control-validation mb-6">
            <label for="flatpickr-date" class="form-label">Join Date</label>
            <input type="text" required class="form-control" name='date' placeholder="YYYY-MM-DD"
              id="flatpickr-date" />
          </div>
          <button type="submit" class="btn btn-primary d-grid w-100 mb-6">Set new password</button>
          <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex justify-content-center">
              <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl me-1_5"></i>
              Back to login
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Apka flatpickr initialization yahan hona chahiye
    if (document.querySelector('#flatpickr-date')) {
        flatpickr("#flatpickr-date", {
            dateFormat: "Y-m-d",
            defaultDate: "today",
        });
    }
});
</script>
@endsection