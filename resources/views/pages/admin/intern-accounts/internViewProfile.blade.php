@extends('layouts/layoutMaster')

@section('title', 'View Internee Profile')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/page-user-view.scss')
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/modal-edit-user.js', 'resources/assets/js/app-user-view.js',
'resources/assets/js/app-user-view-account.js'])
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
            @if ($interneAccountDetails->image)
            <img class="img-fluid rounded mb-4" src="{{ 
    $interneAccountDetails->image
        ? (str_starts_with($interneAccountDetails->image, 'data:image')
            ? $interneAccountDetails->image
            : asset($interneAccountDetails->image)) 
        : ''
    }}" height="120" width="120" alt="{{$interneAccountDetails->image}}" />
            @else
            <div
              class="rounded mb-4 d-flex align-items-center justify-content-center bg-label-warning text-warning fw-bold"
              style="width:120px; height:120px; font-size:32px;">
              {{ strtoupper(substr($interneAccountDetails->name, 0, 2)) }}
            </div>
            @endif

            <div class="user-info text-center">
              <h5>{{$interneAccountDetails->name}}</h5>
              <span class="badge bg-label-success">Internee</span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
          <div class="d-flex align-items-center me-5 gap-4">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="icon-base ti tabler-checkbox icon-lg"></i>
              </div>
            </div>
            <div>
              <h5 class="mb-0">{{$totalTasksDone}}</h5>
              <span>Task Done</span>
            </div>
          </div>
          <div class="d-flex align-items-center gap-4">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="icon-base ti tabler-briefcase icon-lg"></i>
              </div>
            </div>
            <div>
              <h5 class="mb-0">{{$projectsDone}}</h5>
              <span>Project Done</span>
            </div>
          </div>
        </div>
        <h5 class="pb-4 border-bottom mb-4">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-2">
              <span class="h6">Username:</span>
              <span>
                @if ($interneAccountDetails->name)
                {{$interneAccountDetails->name}}
                @else
                N/A
                @endif
              </span>
            </li>
            <li class="mb-2">
              <span class="h6">Email:</span>
              <span>
                @if ($interneAccountDetails->email)
                {{$interneAccountDetails->email}}
                @else
                N/A
                @endif
              </span>
            </li>

            <li class="mb-2">
              <span class="h6">Phone:</span>
              <span>@if ($interneAccountDetails->phone)
                {{$interneAccountDetails->phone}}
                @else
                N/A
                @endif</span>
            </li>


            <li class="mb-2">
              <span class="h6">Start Date:</span>
              <span>@if ($interneAccountDetails->start_date)
                {{$interneAccountDetails->start_date}}
                @else
                N/A
                @endif</span>
            </li>





            <li class="mb-2">
              <span class="h6">Technology:</span>
              <span>@if ($interneAccountDetails->int_technology)
                {{$interneAccountDetails->int_technology}}
                @else
                N/A
                @endif</span>
            </li>

            <li class="mb-2">
              <span class="h6">Status:</span>
              <span>@if ($interneAccountDetails->int_status)
                {{$interneAccountDetails->int_status}}
                @else
                N/A
                @endif</span>
            </li>
            <li class="mb-2">
              <span class="h6">Review:</span>
              <span>@if ($interneAccountDetails->review)
                {{$interneAccountDetails->review}}
                @else
                N/A
                @endif</span>
            </li>



          </ul>
          {{-- <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-primary me-4" data-bs-target="#editUser"
              data-bs-toggle="modal">Edit</a>
            <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspend</a>
          </div> --}}
        </div>
      </div>
    </div>
    <!-- /User Card -->

  </div>
  <!--/ User Sidebar -->

  <!-- User Content -->
  <div class="col-xl-8 col-lg-7 order-0 order-md-1">
    <!-- User Pills -->
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
        <li class="nav-item">
          <a class="nav-link active" href="javascript:void(0);"><i
              class="icon-base ti tabler-user-check icon-sm me-1_5"></i>Account</a>
        </li>
        {{-- <li class="nav-item">
          <a class="nav-link" href="{{ url('app/user/view/security') }}"><i
              class="icon-base ti tabler-lock icon-sm me-1_5"></i>Security</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('app/user/view/billing') }}"><i
              class="icon-base ti tabler-bookmark icon-sm me-1_5"></i>Billing & Plans</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('app/user/view/notifications') }}"><i
              class="icon-base ti tabler-bell icon-sm me-1_5"></i>Notifications</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('app/user/view/connections') }}"><i
              class="icon-base ti tabler-link icon-sm me-1_5"></i>Connections</a>
        </li> --}}
      </ul>
    </div>
    <!--/ User Pills -->

    <!-- Project table -->
    <div class="card">
      {{-- <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Interview Interns
        </h5>

      </div> --}}
      <div class="card-datatable">
        <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
          <div class="row m-3 my-0 justify-content-between">
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
              <div class="dt-length mb-md-6 mb-0 d-flex">

                <h5 class="mb-0">Project Tasks List</h5>



                <label for="dt-length-0"></label>
              </div>
            </div>


            <div
              class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
              <form method="GET"
                action="{{ route('view.profile.interne.account.admin', $interneAccountDetails->int_id) }}"
                id="filterForm" class="d-flex gap-2">

                <input type="search" name="search" id="searchInput" class="form-control"
                  placeholder="Search Intern Tasks..." value="{{ request('search') }}">
                <style>
                  input[type="search"]::-webkit-search-cancel-button,
                  input[type="search"]::-webkit-search-decoration {
                    -webkit-appearance: none;
                    appearance: none;
                  }
                </style>
                {{-- <select name="status" id="statusFilter" class="form-select text-capitalize">
                  <option value="">Select Status</option>

                  @foreach (['Ongoing','Expired', 'Completed'] as $status)
                  @php $slug = strtolower($status); @endphp

                  <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' : '' }}>
                    {{ $status }}
                  </option>
                  @endforeach
                </select> --}}



              </form>










            </div>
          </div>
          <div class="justify-content-between dt-layout-table">
            <div
              class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
              style="max-height: 500px;">
              <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
                aria-describedby="DataTables_Table_0_info" style="width: 100%;">





                <thead class="border-top sticky-top bg-card">
                  <tr>


                    <th data-dt-column="1" rowspan="1" colspan="1"
                      class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Profile Picture" tabindex="0">
                      <span class="dt-column-title" role="button">Project Name
                      </span><span class="dt-column-order"></span>
                    </th>

                    <th data-dt-column="4" rowspan="1" colspan="1"
                      class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="City" tabindex="0"><span
                        class="dt-column-title" role="button">Task Title</span><span class="dt-column-order"></span>
                    </th>
                    <th data-dt-column="3" rowspan="1" colspan="1"
                      class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Email" tabindex="0"><span
                        class="dt-column-title" role="button">Start Date</span><span class="dt-column-order"></span>
                    </th>
                    <th data-dt-column="4" rowspan="1" colspan="1"
                      class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="City" tabindex="0"><span
                        class="dt-column-title" role="button">End Date</span><span class="dt-column-order"></span></th>

                    <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                      aria-label="Join Date"><span class="dt-column-title">Duration</span><span
                        class="dt-column-order"></span></th>


                    <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                      aria-label="Join Date"><span class="dt-column-title">STATUS</span><span
                        class="dt-column-order"></span></th>





                  </tr>
                </thead>
                <tbody>
                  @forelse ($tasks as $task)
                  <tr class="">
                    <td><span class="text-heading text-nowrap">{{$task->project->title}}</span></td>

                    <td><span
                        class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_title}}</span>
                    </td>
                    <td><span
                        class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->t_start_date}}</span>
                    </td>
                    <td><span
                        class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->t_end_date}}</span>
                    </td>
                    <td><span
                        class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_duration}}</span>
                    </td>

                    <td>
                      @php
                      $statusClasses = [
                      'ongoing' => 'bg-label-primary',
                      'contact' => 'bg-label-info',
                      'expired' => 'bg-label-danger',
                      'completed' => 'bg-label-success',
                      'active' => 'bg-label-success',
                      'removed' => 'bg-label-danger',
                      'freeze' => 'bg-label-danger',
                      'submitted' => 'bg-label-warning',
                      'approved' => 'bg-label-success',
                      'rejected' => 'bg-label-danger',
                      ];

                      $status = strtolower($task->task_status);
                      $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                      @endphp

                      <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                    </td>




                  </tr>



                  @empty
                  <tr>
                    <td colspan="11">
                      <p class="text-center mb-0">No data available!</p>
                    </td>
                  </tr>
                  @endforelse







                </tbody>
                <tfoot></tfoot>
              </table>




            </div>
          </div>
          <div class="row mx-3 justify-content-between">

            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
              <div class="dt-info" aria-live="polite">
                Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{
                $tasks->total() ??
                0 }} entries
              </div>
            </div>

            <div
              class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
              <div class="dt-paging">
                <nav aria-label="pagination">
                  <ul class="pagination">

                    <li class="dt-paging-button page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->url(1) }}" aria-label="First">
                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>


                    <li class="dt-paging-button page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->previousPageUrl() }}" aria-label="Previous">
                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>


                    @foreach ($tasks->getUrlRange(max(1, $tasks->currentPage() - 2),
                    min($tasks->lastPage(),
                    $tasks->currentPage() + 2)) as $page => $url)
                    <li class="dt-paging-button page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                      <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach


                    <li
                      class="dt-paging-button page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->nextPageUrl() }}" aria-label="Next">
                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>


                    <li
                      class="dt-paging-button page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->url($tasks->lastPage()) }}" aria-label="Last">
                        <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
    <!-- /Project table -->


  </div>
  <!--/ User Content -->
</div>
@push('scripts')
<script>
  let timer;

  document.getElementById('searchInput').addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500); // wait 500ms after typing
  });

  document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
  });
</script>
@endpush

<!-- Modal -->
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modal -->
@endsection