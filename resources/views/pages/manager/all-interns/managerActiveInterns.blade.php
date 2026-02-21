@extends('layouts/layoutMaster')

@section('title', 'Active-Interns')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js',])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@endsection

@section('content')

<!-- Users List Table -->
<div class="col-12 mb-6">
  <div class="col-xl-12 col-md-12">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title mb-0">Statistics</h5>
      </div>
      <div class="card-body d-flex align-items-end">
        <div class="w-100">
          <div class="row gy-3">

            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-primary me-4 p-2">
                  <i class="icon-base ti tabler-user-check icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{ $stats['interview'] ?? 0 }}</h5>
                  <small>Interview</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-info me-4 p-2"><i class="icon-base ti tabler-users icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{$stats['contacted'] ?? 0}}</h5>
                  <small>Contacted</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-danger me-4 p-2">
                  <i class="icon-base ti tabler-list-check icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{$stats['test_attempt'] ?? 0}}</h5>
                  <small>Test Attempts</small>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-6">
              <div class="d-flex align-items-center">
                <div class="badge rounded bg-label-success me-4 p-2">
                  <i class="icon-base ti tabler-certificate icon-lg"></i>
                </div>
                <div class="card-info">
                  <h5 class="mb-0">{{$stats['test_completed'] ?? 0}}</h5>
                  <small>Test Completed</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <h4 class="mt-6 mb-1">Active Interns</h4>
</div>
{{-- Error Messages --}}
@if($errors->any())
@foreach($errors->all() as $error)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ $error }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endforeach
@endif

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Auto-hide script --}}
<script>
  setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000); // 5 seconds
</script>

<div class="card">

  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 my-0 justify-content-between">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-length mb-md-6 mb-0 d-flex">

            <form id="perPageForm" method="GET">
              <select name="per_page" class="form-select" onchange="this.form.submit()">
                @foreach([15, 25, 50, 100] as $val)
                <option value="{{ $val }}" {{ request('per_page')==$val ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
              </select>
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
              <input type="hidden" name="intern_type" value="{{ request('intern_type') }}">
            </form>



            <label for="dt-length-0"></label>
          </div>
        </div>


        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <form method="GET" action="{{ route('manager.activeInterns') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control"
              placeholder="Search by Name or ETI ID" value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <input type="text" id="dateRangePicker" name="date_range" class="form-control"
              placeholder="Select Date Range" value="{{ request('date_range') }}" style="min-width: 200px;">
            <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
              <option value="">Select Technology</option>

              @foreach ($activeTechnologies as $tech)
              @php
              // English comments: Create slug by replacing spaces with hyphens
              $techSlug = strtolower(str_replace(' ', '-', $tech->technology));
              @endphp

              <option value="{{ $techSlug }}" {{ request('status')==$techSlug ? 'selected' : '' }}>
                {{ $tech->technology }}
              </option>
              @endforeach
            </select>

            <select name="intern_type" id="typeFilter" class="form-select text-capitalize"
              onchange="this.form.submit()">
              <option value="">Internship Type</option>
              @foreach (['Onsite','Remote'] as $type)
              <option value="{{ $type }}" {{ request('intern_type')==$type ? 'selected' : '' }}>
                {{ $type }}
              </option>
              @endforeach
            </select>







            @php
            $adminSettings = \App\Models\AdminSetting::first();

            if (!$adminSettings) {
            $isAdminAllowed = true;
            } else {
            $permissions = $adminSettings->export_permissions;
            $isAdminAllowed = isset($permissions['manager']) && $permissions['manager'] == 1;
            }
            @endphp

            @if($isAdminAllowed)
            <div class="btn-group" role="group">
              <button id="btnGroupDrop1" type="button" class="btn add-new btn-outline-primary dropdown-toggle"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-base ti tabler-dots-vertical icon-md d-sm-none"></i>
                <i class="icon-base ti tabler-upload icon-xs me-2"></i>
                <span class="d-none d-sm-block">Export</span>
              </button>
              <div class="dropdown-menu" style="z-index: 1021" aria-labelledby="btnGroupDrop1">
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadActiveCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
                </a>
              </div>
            </div>
            @endif
          </form>
        </div>
      </div>
      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
          style="max-height: 700px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">

            <thead class="border-top sticky-top bg-card">
              <tr>
                

                
                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title"
                    role="button">Image</span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Email</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">City</span><span
                    class="dt-column-order"></span></th>
                    <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Internship Type</span><span
                    class="dt-column-order"></span></th>
                
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Technology</span><span
                    class="dt-column-order"></span></th>
                
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Join Date</span><span
                    class="dt-column-order"></span></th>
              
                  <th data-dt-column="6" rowspan="1" colspan="1"
                  class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Internship Duration" tabindex="0">
                  <span class="dt-column-title" role="button">Status
                    </span><span class="dt-column-order"></span></th>
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap" aria-label="Join Date"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>


              </tr>
            </thead>
            <tbody>
              @forelse ($internAccounts as $internAccount)
              <tr class="">
               
                <td class="">
                  <div class="d-flex justify-content-start align-items-center user-name">
                    <div class="avatar-wrapper">
                      @if ($internAccount->profile_image)
                      <div class="avatar avatar-md me-4">
                        <img src="{{ 
    $internAccount->profile_image 
        ? (str_starts_with($internAccount->iprofile_imagee, 'data:image')
            ? $internAccount->iprofile_imagee
            : asset($internAccount->profile_image)) 
        : '' 
    }}" alt="{{ $internAccount->name }}" class="rounded-circle" />
                      </div>
                      @else
                      <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                          {{ strtoupper(substr($internAccount->name, 0, 2)) }}
                        </span>
                      </div>
                      @endif
                    </div>

                  </div>
                </td>


                <td><span class="text-heading text-nowrap">{{$internAccount->name}}</span></td>
                <td><i class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i><span
                    class="text-heading text-nowrap"><small>{{$internAccount->email}}</small></span></td>
                <td><span class="text-heading text-nowrap d-flex items-center">{{$internAccount->city}}</span>
                </td>
                <td><span class="text-heading text-nowrap d-flex items-center">{{$internAccount->intern_type}}</span>
                </td>
                <td><span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-cpu me-1 text-primary icon-22px"></i>
                    {{$internAccount->int_technology}}</span></td>
                
                  <td><span class="text-heading text-nowrap">
                    @if ($internAccount->joining_date)
                      {{$internAccount->joining_date}}
                    @else
                      N/A
                    @endif</span></td>

                    
                
                  <td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  'interview' => 'bg-label-primary',
                  'contact' => 'bg-label-info',
                  'test' => 'bg-label-warning',
                  'completed' => 'bg-label-success',
                  'active' => 'bg-label-success',
                  'removed' => 'bg-label-danger',
                  'freeze' => 'bg-label-danger',
                  ];

                  $status = strtolower($internAccount->int_status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                </td>
                <td>
                  <div class="dropdown">
                    <a href="javascript:;"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end m-0">

                      
                      <a href="javascript:;" 
   class="dropdown-item edit-intern" 
   data-bs-toggle="modal" 
   data-bs-target="#editInternModal" 
   data-id="{{ $internAccount->int_id }}"
   data-status="{{ $internAccount->int_status }}"
   data-review="{{ $internAccount->review }}"> Edit Status
</a>
                      <a href="javascript:void(0);" 
   class="dropdown-item text-danger delete-record" 
   data-id="{{ $internAccount->int_id }}" 
   data-name="{{ $internAccount->name }}">
   Remove
</a>

{{-- Hidden Form for Security --}}
<form id="delete-form-{{ $internAccount->int_id }}" 
      action="{{ route('manager.interns.remove', $internAccount->int_id) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('PATCH') {{-- Status update ke liye PATCH best hai --}}
</form>




                    </div>
                  </div>
                </td> {{--<td><span class="text-heading text-nowrap"></span>3rd June</td>--}}
                {{--<td><span class="text-heading text-nowrap"></span>Completed</td>--


                {{--<td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  'interview' => 'bg-label-primary',
                  'contact' => 'bg-label-info',
                  'test' => 'bg-label-warning',
                  'completed' => 'bg-label-success',
                  'active' => 'bg-label-success',
                  'removed' => 'bg-label-danger',
                  ];

                  $status = strtolower($intern->status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                </td>--}}

                {{-- <td><span class="text-heading text-nowrap">{{$intern->intern_type}}</span></td> --}}
                {{--<td>
                  <div class="d-flex align-items-center">
                    <div class="dropdown">
                      <a href="javascript:;"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end m-0">

                        <a href="{{route('view.profile.internee.admin', $intern->id)}}"
                          class="dropdown-item permission-btn">
                          View Profile
                        </a>
                        <a href="javascript:;" class="dropdown-item edit-intern" data-bs-toggle="modal"
                          data-bs-target="#editInternModal" data-id="{{ $intern->id }}" data-name="{{ $intern->name }}"
                          data-email="{{ $intern->email }}" data-technology="{{ $intern->technology }}"
                          data-status="{{ $intern->status }}">
                          Edit
                        </a>

                        @if (strtolower($intern->status) != 'removed')
                        <a href="javascript:;" class="dropdown-item permission-btn delete-record"
                          data-id="{{ $intern->id }}">
                          Remove
                        </a>
                        <form id="delete-form-{{ $intern->id }}" action="{{ route('interns.destroy', $intern->id) }}"
                          method="POST" style="display: none;">
                          @csrf
                          @method('DELETE')
                        </form>
                        @endif



                      </div>
                    </div>
                  </div>

                </td>--}}


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

          {{-- Change Status - Start --}}
          <div class="modal fade" id="editInternModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-simple modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" class="btn-close" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" data-bs-dismiss="modal" aria-label="Close"></button>

        <div class="text-start mb-4">
          <h5 class="role-title">Update Intern Status</h5>
        </div>

        <form id="editInternForm" novalidate class="row g-3" action="{{ route('update.intern.manager') }}" method="POST">
          @csrf
          <input type="hidden" id="id" name="id">

          <div class="col-12 mb-2">
            <label class="form-label" for="status">Status</label>
            <select name="status" id="status" required class="form-select text-capitalize">
              <option value="Active">Active</option>
              <option value="Freeze">Freeze</option>
              <option value="Test">Test</option>
              <option value="Completed">Completed</option>
            </select>
            <small class="text-danger error-status"></small>
          </div>

          <div class="col-12 mb-3">
            <label class="form-label" for="review">Review / Remarks</label>
            <textarea name="review" id="review" class="form-control" rows="3" placeholder="Add notes here..."></textarea>
            <small class="text-danger error-review"></small>
          </div>

          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
          {{-- Change Status - End --}}


        </div>
      </div>
      <div class="row mx-3 justify-content-between">
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $internAccounts->firstItem() ?? 0 }} to {{ $internAccounts->lastItem() ?? 0 }} of {{
            $internAccounts->total() ??
            0 }} entries
          </div>
        </div>

        {{-- Pagination --}}
        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                {{-- First Page --}}
                <li class="dt-paging-button page-item {{ $internAccounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $internAccounts->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Previous Page --}}
                <li class="dt-paging-button page-item {{ $internAccounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $internAccounts->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Page Numbers --}}
                @foreach ($internAccounts->getUrlRange(max(1, $internAccounts->currentPage() - 2),
                min($internAccounts->lastPage(),
                $internAccounts->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $internAccounts->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Page --}}
                <li
                  class="dt-paging-button page-item {{ $internAccounts->currentPage() == $internAccounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $internAccounts->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Last Page --}}
                <li
                  class="dt-paging-button page-item {{ $internAccounts->currentPage() == $internAccounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $internAccounts->url($internAccounts->lastPage()) }}" aria-label="Last">
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



@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.delete-record').forEach(button => {
    button.addEventListener('click', function () {

      const id = this.dataset.id;

      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('delete-form-' + id).submit();
        }
      });

    });
  });

});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {

    const editModal = document.getElementById('editInternModal');
    const form = document.getElementById('editInternForm');

    // Populate modal
    editModal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;

        form.querySelector('#id').value = btn.dataset.id || '';
        form.querySelector('#name').value = btn.dataset.name || '';
        form.querySelector('#email').value = btn.dataset.email || '';
        form.querySelector('#technology').value = btn.dataset.technology || '';
        form.querySelector('#status').value = btn.dataset.status || '';

        clearErrors();
    });

    // Live validation
    ['name', 'email', 'technology'].forEach(field => {
        form[field].addEventListener('input', () => validateField(field));
    });

    form.status.addEventListener('change', () => validateField('status'));

    // Submit validation (IMPORTANT FIX)
    form.addEventListener('submit', function (e) {

        let valid = true;

        ['name', 'email', 'technology', 'status'].forEach(field => {
            if (!validateField(field)) valid = false;
        });

        if (!valid) {
            e.preventDefault(); // ❗ only prevent when invalid
        }
    });

    function validateField(field) {
        const value = form[field].value.trim();

        switch (field) {
            case 'name':
                if (!value) return showError(field, 'Name is required');
                break;

            case 'email':
                if (!value) return showError(field, 'Email is required');
                if (!isValidEmail(value)) return showError(field, 'Invalid email format');
                break;

            case 'technology':
                if (!value) return showError(field, 'Technology is required');
                break;

            case 'status':
                if (!value) return showError(field, 'Please select a status');
                break;
        }

        clearError(field);
        return true;
    }

    function showError(field, message) {
        const el = form.querySelector('.error-' + field);
        if (el) el.textContent = message;
        return false;
    }

    function clearError(field) {
        const el = form.querySelector('.error-' + field);
        if (el) el.textContent = '';
    }

    function clearErrors() {
        form.querySelectorAll('small.text-danger').forEach(el => el.textContent = '');
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

});
</script>

<script>
  function downloadActiveCSV() {
    // English comments: Get all current filter values from the UI
    const search = document.getElementById('searchInput')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const dateRange = document.getElementById('dateRangePicker')?.value || '';
    const internType = document.getElementById('typeFilter')?.value || '';
    
    let url = "{{ route('active.interns.export.csv.manager') }}";
    
    // English comments: Append all filters to the export URL
    const params = new URLSearchParams({
        search: search,
        status: status,
        date_range: dateRange,
        intern_type: internType
    });

    window.location.href = url + "?" + params.toString();
}
</script>


@endpush

@push('scripts')
<script>
  // English comments: Wait for the DOM and libraries to be fully loaded
  document.addEventListener('DOMContentLoaded', function () {
    const datePicker = document.querySelector('#dateRangePicker');
    
    if (datePicker) {
        // English comments: Initialize flatpickr with range mode
        flatpickr(datePicker, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            // English comments: Show calendar as a dropdown
            static: true, 
            allowInput: true,
            onClose: function(selectedDates, dateStr) {
                // English comments: Submit form only if 2 dates are selected or input is cleared
                if (selectedDates.length === 2 || dateStr === "") {
                    document.getElementById('filterForm').submit();
                }
            }
        });
    }
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-record');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name;

            Swal.fire({
                title: 'Are you sure?',
                text: `You want to remove ${name} from active interns?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // English comments: Submit the specific hidden form
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
});
</script>
<script>
 // English comments: Get the modal element
const editInternModal = document.getElementById('editInternModal');

if (editInternModal) {
    editInternModal.addEventListener('show.bs.modal', function (event) {
        // English comments: Button that triggered the modal
        const button = event.relatedTarget;
        
        // English comments: Extract info from data attributes
        const id = button.getAttribute('data-id');
        const status = button.getAttribute('data-status');
        const review = button.getAttribute('data-review'); // <--- Ye line check karein

        // English comments: Update the modal's content
        const modalForm = document.getElementById('editInternForm');
        
        modalForm.querySelector('#id').value = id;
        modalForm.querySelector('#status').value = status;
        
        // English comments: Populate the review/remarks textarea
        // Use .value for textarea to ensure content is displayed
        const reviewField = modalForm.querySelector('#review');
        if (reviewField) {
            reviewField.value = review || ''; 
        }
    });
}
</script>
@endpush

@endsection