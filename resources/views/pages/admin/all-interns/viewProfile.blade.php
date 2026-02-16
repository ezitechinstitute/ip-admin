@extends('layouts/layoutMaster')

@section('title', 'View Internee Profile')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-user-view.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-user-view.js', 'resources/assets/js/app-user-view-account.js'])
@endsection

@section('content')
<div class="row">
  <!-- User Sidebar -->
  <div class="col-xl-4 col-lg-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-6">
      <div class="card-body pt-12">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            @if ($interneeDetails->image)
                  <img class="img-fluid rounded mb-4" src="{{ 
    $interneeDetails->image
        ? (str_starts_with($interneeDetails->image, 'data:image')
            ? $interneeDetails->image
            : asset($interneeDetails->image)) 
        : asset('assets/img/avatars/1.png') 
    }}" height="120" width="120" alt="{{$interneeDetails->image}}" />
            @else
             <div
      class="rounded mb-4 d-flex align-items-center justify-content-center bg-label-warning text-warning fw-bold"
      style="width:120px; height:120px; font-size:32px;"
    >
      {{ strtoupper(substr($interneeDetails->name, 0, 2)) }}
    </div>
            @endif
            
            <div class="user-info text-center">
              <h5>{{$interneeDetails->name}}</h5>
              <span class="badge bg-label-success">Internee</span>
            </div>
          </div>
        </div>
      
        
      </div>
    </div>
    <!-- /User Card -->
   
  </div>
  <!--/ User Sidebar -->
<div class="col-xl-8 col-lg-7 order-1 order-md-0">
   <div class="card mb-6">
      <div class="card-body">
        <h5 class="pb-4 border-bottom mb-4"><i class="icon-base ti tabler-user me-1 text-primary icon-22px"></i> Details</h5>
        <div class="info-container">
          <ul class="list-unstyled row g-1">
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Username:</span>
              <span>
                @if ($interneeDetails->name)
                {{$interneeDetails->name}}
              @else
                N/A
              @endif
              </span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Email:</span>
              <span>
                @if ($interneeDetails->email)
                {{$interneeDetails->email}}
              @else
                N/A
              @endif
              </span>
            </li>
            
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Contact:</span>
              <span>@if ($interneeDetails->contact)
                {{$interneeDetails->contact}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">CNIC:</span>
              <span>@if ($interneeDetails->cnic)
                {{$interneeDetails->cnic}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Gender:</span>
              <span>@if ($interneeDetails->gender)
                {{$interneeDetails->gender}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Join Date:</span>
              <span>@if ($interneeDetails->join_date)
                {{$interneeDetails->join_date}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">DOB:</span>
              <span>@if ($interneeDetails->birth_date)
                {{$interneeDetails->birth_date}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">University:</span>
              <span>@if ($interneeDetails->university)
                {{$interneeDetails->university}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Country:</span>
              <span>@if ($interneeDetails->country)
                {{$interneeDetails->country}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">City:</span>
              <span>@if ($interneeDetails->city)
                {{$interneeDetails->city}}
              @else
                N/A
              @endif</span>
            </li>
          
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Allow:</span>
              <span>@if ($interneeDetails->interview_type)
                {{$interneeDetails->interview_type}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Technology:</span>
              <span>@if ($interneeDetails->technology)
                {{$interneeDetails->technology}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Duration:</span>
              <span>@if ($interneeDetails->duration)
                {{$interneeDetails->duration}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Status:</span>
              <span>@if ($interneeDetails->status)
                {{$interneeDetails->status}}
              @else
                N/A
              @endif</span>
            </li>
             <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Intern Type:</span>
              <span>@if ($interneeDetails->intern_type)
                {{$interneeDetails->intern_type}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Interview Date:</span>
              <span>@if ($interneeDetails->interview_date)
                {{$interneeDetails->interview_date}}
              @else
                N/A
              @endif</span>
            </li>
            <li class="mb-2 col-12 col-lg-6">
              <span class="h6">Interview Time:</span>
              <span>@if ($interneeDetails->interview_time)
                {{$interneeDetails->interview_time}}
              @else
                N/A
              @endif</span>
            </li>
            
          </ul>
          {{-- <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-primary me-4" data-bs-target="#editUser" data-bs-toggle="modal">Edit</a>
            <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspend</a>
          </div> --}}
        </div>
      </div>
   </div>
</div>
 
</div>

<!-- Modal -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modal -->
@endsection