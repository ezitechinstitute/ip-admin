@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Revenue & Commission')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/fonts/flag-icons.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
  'resources/assets/vendor/libs/pickr/pickr-themes.scss'
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/cards-advance.scss')
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
  'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
  'resources/assets/vendor/libs/pickr/pickr.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <h4 class="mb-3">Revenue & Commission Overview</h4>
  </div>

  <div class="col-xl-3 col-md-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-primary"><i class="ti tabler-users-group"></i></span>
          </div>
          <div class="ms-3">
            <h5 class="mb-0">{{ number_format($totalInternsConverted) }}</h5>
            <small class="text-muted">Total Interns Converted</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-info"><i class="ti tabler-currency-dollar"></i></span>
          </div>
          <div class="ms-3">
            <h5 class="mb-0">PKR {{ number_format($totalRevenue, 2) }}</h5>
            <small class="text-muted">Total Revenue Generated</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-success"><i class="ti tabler-percent"></i></span>
          </div>
          <div class="ms-3">
            <h5 class="mb-0">{{ number_format($commissionPercentage, 2) }}%</h5>
            <small class="text-muted">Commission Percentage</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6 col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start">
          <div class="avatar flex-shrink-0">
            <span class="avatar-initial rounded bg-label-danger"><i class="ti tabler-coin"></i></span>
          </div>
          <div class="ms-3">
            <h5 class="mb-0">PKR {{ number_format($commissionEarned, 2) }}</h5>
            <small class="text-muted">Commission Earned</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-2">How the commission is calculated</h6>
        <p class="mb-0">
          Commission = <strong>Internship Fee</strong> × <strong>Assigned Percentage</strong> (from your manager profile).
          <br />
          In this dashboard we use total revenue generated from your assigned interns and apply your commission rate.
        </p>
      </div>
    </div>
  </div>
</div>
@endsection
