@php
$customizerHidden = 'customizer-hide';
$configData = Helper::appClasses();
@endphp

@extends('layouts.blankLayout')

@section('title', 'Page Not Found')

@section('page-style')
<!-- Page -->
@vite(['resources/assets/vendor/scss/pages/page-misc.scss'])
@endsection

@section('content')
<!-- Page Not Found -->
<div class="container-xxl container-p-y">
  <div class="misc-wrapper">
    <h1 class="mb-2 mx-2" style="line-height: 6rem;font-size: 6rem;">404</h1>
    <h4 class="mb-2 mx-2">Page Not Found 😕</h4>
    <p class="mb-6 mx-2">The page you are looking for does not exist. Go back!</p>
    <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
    <div class="mt-12">
      <img src="{{ asset('assets/img/illustrations/page-misc-you-are-not-authorized.png') }}" alt="page-misc-not-authorized" width="170" class="img-fluid" />
    </div>
  </div>
</div>
<div class="container-fluid misc-bg-wrapper">
  <img src="{{ asset('assets/img/illustrations/bg-shape-image-' . $configData['theme'] . '.png') }}" height="355" alt="page-misc-not-authorized" data-app-light-img="illustrations/bg-shape-image-light.png" data-app-dark-img="illustrations/bg-shape-image-dark.png" />
</div>
<!-- /Page Not Found -->
@endsection