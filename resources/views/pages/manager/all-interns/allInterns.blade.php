@extends('layouts/layoutMaster')

@section('title', 'My Interns')

@section('vendor-style')
<link rel="stylesheet" href="path-to/datatables.bootstrap5.css">
<link rel="stylesheet" href="path-to/responsive.bootstrap5.css">
<link rel="stylesheet" href="path-to/buttons.bootstrap5.css">
<link rel="stylesheet" href="path-to/select2.css">
<link rel="stylesheet" href="path-to/form-validation.css">
<link rel="stylesheet" href="path-to/animate.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
@endsection

@section('vendor-script')
<script src="path-to/moment.js"></script>
<script src="path-to/datatables-bootstrap5.js"></script>
<script src="path-to/select2.js"></script>
<script src="path-to/form-validation.js"></script>
<script src="path-to/cleave-zen.js"></script>
<script src="path-to/sweetalert2.js"></script>
@endsection

@section('content')
<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">All Interns</h4>
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
          <div class="dt-length mb-md-6 mb-0 d-flex items-center mt-5">

            <form id="perPageForm" method="GET">
              <select name="per_page" class="form-select" onchange="this.form.submit()">
                @foreach([15, 25, 50, 100] as $val)
                <option value="{{ $val }}" {{ $perPage==$val ? 'selected' : '' }}>{{ $val }}</option>
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
          <form method="GET" action="{{ route('manager.myInterns') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control"
              placeholder="Search by Name or Email" value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <select name="tech" class="form-select" onchange="this.form.submit()">
    <option value="">Select Technology</option>
    @foreach ($allowedTechNames as $techName)
        @php $slug = strtolower(str_replace(' ', '-', $techName)); @endphp
        <option value="{{ $slug }}" {{ request('tech') == $slug ? 'selected' : '' }}>
            {{ $techName }}
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

           <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
  <option value="">Select Status</option>
  @foreach (['Active', 'Interview', 'Contact', 'Test', 'Completed'] as $statusName) 
    <option value="{{ $statusName }}" {{ request('status') == $statusName ? 'selected' : '' }}>
      {{ $statusName }} </option>
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadCompletedCSV()">
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
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Contact</span><span
                    class="dt-column-order"></span></th>
                    <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">City</span><span
                    class="dt-column-order"></span></th>
                    <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Internship Type</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Technology</span><span
                    class="dt-column-order"></span></th>
                    <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Join Date</span><span
                    class="dt-column-order"></span></th>
                




                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Status</span><span class="dt-column-order"></span></th>
                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>


              </tr>
            </thead>
            <tbody>
              @forelse ($interns as $intern)
              <tr class="">
                <td class="">
                  <div class="d-flex justify-content-start align-items-center user-name">
                    <div class="avatar-wrapper">
                      @if ($intern->image)
                      <div class="avatar avatar-md me-4">
                        <img src="{{ 
    $intern->image
        ? (str_starts_with($intern->image, 'data:image')
            ? $intern->image
            : asset($intern->image)) 
        : '' 
    }}" alt="{{ $intern->name }}" class="rounded-circle" />
                      </div>
                      @else
                      <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                          {{ strtoupper(substr($intern->name, 0, 2)) }}
                        </span>
                      </div>
                      @endif
                    </div>

                  </div>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$intern->name}}</span></td>
                <td><span class="text-heading text-nowrap"><small><i
                        class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i>{{$intern->email}}</small></span></td>
                <td>@if ($intern->phone)
                  <span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-phone me-1 text-success icon-22px"></i>{{$intern->phone}}</span>
                  @else
                  N/A
                  @endif
                </td>
                <td><span class="text-heading text-nowrap">{{$intern->city}}</span></td>
                <td><span class="text-heading text-nowrap">{{$intern->intern_type}}</span></td>
                <td><span class="text-heading text-nowrap">{{$intern->technology}}</span></td>
                <td><span class="text-heading text-nowrap">@if ($intern->join_date)
                  <span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-phone me-1 text-success icon-22px"></i>{{$intern->join_date}}</span>
                  @else
                  N/A
                  @endif</span></td>
                <td>@php
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

                  $status = strtolower($intern->status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span></td>
                  <td>
                  <div class="dropdown">
                    <a href="javascript:;"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end m-0">


                      <a href="javascript:;" class="dropdown-item edit-intern" data-bs-toggle="modal"
                        data-bs-target="#editInternModal" data-id="{{ $intern->id }}"
                        data-status="{{ $intern->status }}"> Edit
                        Status
                      </a>
                      <a href="javascript:void(0);" class="dropdown-item delete-record"
                        data-id="{{ $intern->id }}" data-name="{{ $intern->name }}">
                        Remove
                      </a>

                      {{-- Hidden Form for Security --}}
                      <form id="delete-form-{{ $intern->id }}"
                        action="{{ route('manager.interns.remove', $intern->id) }}" method="POST"
                        style="display: none;">
                        @csrf
                        @method('PATCH') {{-- Status update ke liye PATCH best hai --}}
                      </form>




                    </div>
                  </div>
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

         {{-- Edit Status Modal --}}
<div class="modal fade" id="editInternModal" tabindex="-1" aria-hidden="true" style="z-index: 9999 !important;">
  <div class="modal-dialog modal-md modal-simple modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-start mb-6">
          <h4 class="role-title">Edit Intern Status</h4>
        </div>

        <form id="editInternForm" action="{{ route('update.intern.manager') }}" method="POST">
          @csrf
          {{-- English comments: Hidden input to store the intern ID --}}
          <input type="hidden" id="edit_intern_id" name="id">

          <div class="col-12 mb-3">
            <label class="form-label" for="edit_status">Status</label>
            <select name="status" id="edit_status" required class="form-select text-capitalize">
              <option value="Interview">Interview</option>
              <option value="Contact">Contact</option>
              <option value="Test">Test</option>
              <option value="Completed">Completed</option>
              <option value="Active">Active</option>
              <option value="Removed">Removed</option>
            </select>
          </div>

          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


        </div>
      </div>
      <div class="row mx-3 justify-content-between">
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $interns->firstItem() ?? 0 }} to {{ $interns->lastItem() ?? 0 }} of {{
            $interns->total() ??
            0 }} entries
          </div>
        </div>

        {{-- Pagination --}}
        <div
          class="d-md-flex align-items-center dt-layout-end mt-4 col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                {{-- First Page --}}
                <li class="dt-paging-button page-item {{ $interns->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $interns->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Previous Page --}}
                <li class="dt-paging-button page-item {{ $interns->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $interns->previousPageUrl() }}"
                    aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Page Numbers --}}
                @foreach ($interns->getUrlRange(max(1, $interns->currentPage() - 2),
                min($interns->lastPage(),
                $interns->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $interns->currentPage() ? 'active' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Page --}}
                <li
                  class="dt-paging-button page-item {{ $interns->currentPage() == $interns->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $interns->nextPageUrl() }}"
                    aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Last Page --}}
                <li
                  class="dt-paging-button page-item {{ $interns->currentPage() == $interns->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $interns->url($interns->lastPage()) }}"
                    aria-label="Last">
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

<script>
function downloadCompletedCSV() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    const params = new URLSearchParams(formData).toString();
    
    window.location.href = "{{ route('manager.myInterns.export') }}?" + params;
}
</script>


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {
    // English comments: Handle data loading into the Edit Modal
    const editInternModal = document.getElementById('editInternModal');
    if (editInternModal) {
        editInternModal.addEventListener('show.bs.modal', function (event) {
            // English comments: Button that triggered the modal
            const button = event.relatedTarget;
            
            // English comments: Extract info from data-* attributes
            const internId = button.getAttribute('data-id');
            const currentStatus = button.getAttribute('data-status');

            // English comments: Update the modal's content
            const idInput = editInternModal.querySelector('#edit_intern_id');
            const statusSelect = editInternModal.querySelector('#edit_status');

            idInput.value = internId;
            statusSelect.value = currentStatus;
        });
    }
});
</script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelectorAll('.delete-record').forEach(button => {
    button.addEventListener('click', function() {
        const internId = this.getAttribute('data-id');
        const internName = this.getAttribute('data-name');

        // English comments: Show a confirmation dialog before proceeding with removal
        Swal.fire({
            title: 'Are you sure?',
            text: `You want to remove ${internName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // English comments: Submit the specific hidden form for this intern
                document.getElementById(`delete-form-${internId}`).submit();
            }
        });
    });
});
</script>
<style>
  /* English comments: Force the SweetAlert2 container to be on top of everything */
  .swal2-container {
    z-index: 9999 !important;
  }
</style>
@endpush
@endsection