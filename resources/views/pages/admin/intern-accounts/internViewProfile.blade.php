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
    }}" height="120" width="120" alt="{{$interneAccountDetails->name}}" />
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
        
        {{-- ====== EDIT BUTTON ====== --}}
        <div class="d-flex justify-content-center mb-4">
          <a href="javascript:;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editInternAccountModal"
            data-id="{{ $interneAccountDetails->int_id }}"
            data-name="{{ $interneAccountDetails->name }}"
            data-email="{{ $interneAccountDetails->email }}"
            data-technology="{{ $interneAccountDetails->int_technology }}"
            data-status="{{ strtolower($interneAccountDetails->int_status) }}">
            <i class="icon-base ti tabler-edit me-1"></i> Edit Account
          </a>
        </div>
        
        <h5 class="pb-4 border-bottom mb-4">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-2">
              <span class="h6">Username:</span>
              <span>{{$interneAccountDetails->name ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Email:</span>
              <span>{{$interneAccountDetails->email ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Phone:</span>
              <span>{{$interneAccountDetails->phone ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Start Date:</span>
              <span>{{$interneAccountDetails->start_date ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Technology:</span>
              <span>{{$interneAccountDetails->int_technology ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Status:</span>
              <span>{{$interneAccountDetails->int_status ?? 'N/A'}}</span>
            </li>
            <li class="mb-2">
              <span class="h6">Review:</span>
              <span>{{$interneAccountDetails->review ?? 'N/A'}}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /User Card -->
  </div>
  <!--/ User Sidebar -->

  <!-- User Content -->
  <div class="col-xl-8 col-lg-7 order-0 order-md-1">
    <div class="nav-align-top">
      <ul class="nav nav-pills flex-column flex-md-row flex-wrap mb-6 row-gap-2">
        <li class="nav-item">
          <a class="nav-link active" href="javascript:void(0);"><i class="icon-base ti tabler-user-check icon-sm me-1_5"></i>Account</a>
        </li>
      </ul>
    </div>

    <!-- Project table -->
    <div class="card">
      <div class="card-datatable">
        <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
          <div class="row m-3 my-0 justify-content-between">
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
              <div class="dt-length mb-md-6 mb-0 d-flex">
                <h5 class="mb-0">Project Tasks List</h5>
              </div>
            </div>
            <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
              <form method="GET" action="{{ route('view.profile.interne.account.admin', $interneAccountDetails->int_id) }}" id="filterForm" class="d-flex gap-2">
                <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search Intern Tasks..." value="{{ request('search') }}">
                <style>
                  input[type="search"]::-webkit-search-cancel-button,
                  input[type="search"]::-webkit-search-decoration {
                    -webkit-appearance: none; appearance: none;
                  }
                </style>
              </form>
            </div>
          </div>
          <div class="justify-content-between dt-layout-table">
            <div class="table-responsive overflow-auto" style="max-height: 500px;">
              <table class="datatables-users table dataTable dtr-column" style="width: 100%;">
                <thead class="border-top sticky-top bg-card">
                  <tr>
                    <th class="text-nowrap">Project Name</th>
                    <th class="text-nowrap">Task Title</th>
                    <th class="text-nowrap">Start Date</th>
                    <th class="text-nowrap">End Date</th>
                    <th class="text-nowrap">Duration</th>
                    <th class="text-nowrap">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($tasks as $task)
                  <tr>
                    <td><span class="text-heading text-nowrap">{{$task->project->title}}</span></td>
                    <td><span class="text-heading text-nowrap">{{$task->task_title}}</span></td>
                    <td><span class="text-heading text-nowrap">{{$task->t_start_date}}</span></td>
                    <td><span class="text-heading text-nowrap">{{$task->t_end_date}}</span></td>
                    <td><span class="text-heading text-nowrap">{{$task->task_duration}}</span></td>
                    <td>
                      @php
                      $statusClasses = [
                        'ongoing' => 'bg-label-primary',
                        'expired' => 'bg-label-danger',
                        'completed' => 'bg-label-success',
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
                  <tr><td colspan="6"><p class="text-center mb-0">No data available!</p></td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          <div class="row mx-3 justify-content-between">
            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
              <div class="dt-info">
                Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() ?? 0 }} entries
              </div>
            </div>
            <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-2 flex-wrap">
              <div class="dt-paging">
                <nav aria-label="pagination">
                  <ul class="pagination">
                    <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->url(1) }}" aria-label="First">
                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>
                    <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->previousPageUrl() }}" aria-label="Previous">
                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>
                    @foreach ($tasks->getUrlRange(max(1, $tasks->currentPage() - 2), min($tasks->lastPage(), $tasks->currentPage() + 2)) as $page => $url)
                    <li class="page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                      <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach
                    <li class="page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
                      <a class="page-link" href="{{ $tasks->nextPageUrl() }}" aria-label="Next">
                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                      </a>
                    </li>
                    <li class="page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
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

{{-- ====== EDIT INTERN ACCOUNT MODAL ====== --}}
<div class="modal fade" id="editInternAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-start mb-6">
          <h4 class="role-title">Edit Intern Account</h4>
        </div>
        <form id="editInternAccountForm" class="row g-3" action="{{ route('update-intern-account') }}" method="POST">
          @csrf
          <input type="hidden" id="edit_int_id" name="int_id">

          <div class="col-6 mb-3">
            <label class="form-label" for="edit_name">Name</label>
            <input type="text" id="edit_name" name="name" class="form-control" required />
          </div>

          <div class="col-6 mb-3">
            <label class="form-label" for="edit_email">Email</label>
            <input type="email" id="edit_email" name="email" class="form-control" required />
          </div>

          <div class="col-6 mb-3">
            <label class="form-label" for="edit_int_technology">Technology</label>
            <input type="text" id="edit_int_technology" name="int_technology" class="form-control" required />
          </div>

          <div class="col-6 mb-3">
            <label class="form-label" for="edit_int_status">Status</label>
            <select name="int_status" id="edit_int_status" required class="form-select text-capitalize">
              <option value="active">Active</option>
              <option value="test">Test</option>
              <option value="freeze">Freeze</option>
            </select>
          </div>

          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  let timer;
  document.getElementById('searchInput').addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
  });

  // ========== POPULATE EDIT MODAL ==========
  document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editInternAccountModal');
    if (editModal) {
      editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        document.getElementById('edit_int_id').value = button.getAttribute('data-id');
        document.getElementById('edit_name').value = button.getAttribute('data-name');
        document.getElementById('edit_email').value = button.getAttribute('data-email');
        document.getElementById('edit_int_technology').value = button.getAttribute('data-technology');
        document.getElementById('edit_int_status').value = button.getAttribute('data-status');
      });
    }
  });
</script>
@endpush

@endsection