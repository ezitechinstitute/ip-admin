@extends('layouts/layoutMaster')

@section('title', 'Technology')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@vite(['resources/assets/js/forms-pickers.js'])
@endsection

@section('content')

<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Technology</h4>
  {{-- <p class="mb-0">Find all of your company’s administrator accounts and their associate roles.</p> --}}

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
  {{-- <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Interview Interns
    </h5>

  </div> --}}
  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 my-0 justify-content-between">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-length mb-md-6 mb-0 d-flex">

            <form id="perPageForm" method="GET">
              <select name="per_page" id="dt-length-0" class="form-select ms-0"
                onchange="document.getElementById('perPageForm').submit()">
                <option value="15" {{ $perPage==15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage==25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
              </select>
              <!-- Keep search & status in query -->
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
            </form>



            <label for="dt-length-0"></label>
          </div>
        </div>


        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <form method="GET" action="{{ route('technology') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search technology"
              value="{{ request('search') }}">

            <select name="status" id="statusFilter" class="form-select text-capitalize">
              <option value="">Select Status</option>

              <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>
                Active
              </option>

              <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                Freeze
              </option>
            </select>


        @php
            $adminSettings = \App\Models\AdminSetting::first();

            if (!$adminSettings) {
            $isAdminAllowed = true;
            } else {
            $permissions = $adminSettings->export_permissions;
            $isAdminAllowed = isset($permissions['admin']) && $permissions['admin'] == 1;
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadTechnologiesCSV()">
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



          <button class="btn add-new btn-primary rounded-2 waves-effect waves-light" tabindex="0"
            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
            data-bs-target="#addTechnologyModal"><span><i
                class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Add
                Technology</span></span></button>
          <!-- Add Technology -->
          <div class="modal fade" id="addTechnologyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Add New Technology</h4>
                  </div>
                  <!-- Add role form -->
                  <form id="addRoleForm" class="row g-3" method="POST" action="{{route('add-technology')}}">
                    @csrf
                    <div class="col-12 col-md-12 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleTechnology">Technology Name</label>
                      <input type="text" id="modalRoleTechnology" name="technology" class="form-control"
                        placeholder="Enter technology name" tabindex="-1" />
                    </div>




                    <div class="col-12 col-md-12 form-control-validation mb-3">
                      <label class="form-label">Status</label>

                      <div class="d-flex gap-4">
                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="status" id="statusActive" value="1"
                            checked />
                          <label class="form-check-label" for="statusActive">Active</label>
                        </div>

                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="status" id="statusFreeze" value="0" />
                          <label class="form-check-label" for="statusFreeze">Freeze</label>
                        </div>
                      </div>
                    </div>





                    <div class="col-12 text-end mt-3">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                      <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                  <!--/ Add role form -->
                </div>
              </div>
            </div>
          </div>
          <!-- Add Technology -->
          <!-- Edit Technology Modal -->
         <div class="modal fade" id="editTechnologyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content p-3">

      <div class="modal-header">
        <h5 class="modal-title">Edit Technology</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="editTechnologyForm" method="POST" action="{{ route('edit-technology') }}">
        @csrf
        @method('PUT')

        <!-- REQUIRED -->
        <input type="hidden" name="id" id="editTechId">

        <div class="modal-body">

          <div class="mb-3 form-control-validation">
            <label class="form-label">Technology Name</label>
            <input type="text" id="editTechnologyName" required name="technology" class="form-control">
          </div>

          <div class="mb-3 form-control-validation">
            <label class="form-label">Status</label>

            <div class="d-flex gap-4 mt-2">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="editStatusActive" value="1">
                <label class="form-check-label" for="editStatusActive">Active</label>
              </div>

              <div class="form-check">
                <input class="form-check-input" type="radio" name="status" id="editStatusFreeze" value="0">
                <label class="form-check-label" for="editStatusFreeze">Freeze</label>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </form>

    </div>
  </div>
</div>









        </div>
      </div>
      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
          style="max-height: 600px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">
            <colgroup>
              <col data-dt-column="1" style="width: 63.5375px;">
              <col data-dt-column="2" style="width: 326.863px;">
              <col data-dt-column="3" style="width: 170.125px;">
              <col data-dt-column="4" style="width: 131.825px;">
            </colgroup>
            <thead class="border-top sticky-top bg-card">
              <tr>

                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="#ID" tabindex="0"><span class="dt-column-title" role="button">#ID</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Technology Name" tabindex="0"><span class="dt-column-title" role="button">Technology
                    Name</span><span class="dt-column-order"></span></th>


                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Status"><span
                    class="dt-column-title">Status</span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Action"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



              </tr>
            </thead>
            <tbody>
              @forelse ($allTechnologies as $technology)
              <tr class="">
                <td><span class="text-heading text-nowrap"><small>{{$technology->tech_id}}</small></span>
                </td>

                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap"><i class="icon-base ti tabler-cpu me-1 text-primary icon-22px"></i>
{{$technology->technology}}</span>
                </td>

                <td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  '1' => 'bg-label-success',
                  '0' => 'bg-label-danger',
                  ];

                  $status = strtolower($technology->status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">@if ($technology->status == 1)
                    Active
                    @else
                    Freeze
                    @endif</span>
                </td>

                {{-- <td><span class="text-heading text-nowrap">{{$technology->intern_type}}</span></td> --}}
                <td>
                  <div class="d-flex align-items-center">



                    <a href="javascript:void(0)"
   class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-technology"
   data-id="{{ $technology->tech_id }}"
   data-name="{{ $technology->technology }}"
   data-status="{{ $technology->status }}">
   <i class="icon-base ti tabler-edit icon-22px"></i>
</a>


                  </div>

                </td>


              </tr>



              @empty
              <tr>
                <td colspan="4">
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
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $allTechnologies->firstItem() ?? 0 }} to {{ $allTechnologies->lastItem() ?? 0 }} of {{
            $allTechnologies->total()
            ??
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
                <li class="dt-paging-button page-item {{ $allTechnologies->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allTechnologies->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Previous Page --}}
                <li class="dt-paging-button page-item {{ $allTechnologies->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allTechnologies->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Page Numbers --}}
                @foreach ($allTechnologies->getUrlRange(max(1, $allTechnologies->currentPage() - 2),
                min($allTechnologies->lastPage(),
                $allTechnologies->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $allTechnologies->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Page --}}
                <li
                  class="dt-paging-button page-item {{ $allTechnologies->currentPage() == $allTechnologies->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allTechnologies->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Last Page --}}
                <li
                  class="dt-paging-button page-item {{ $allTechnologies->currentPage() == $allTechnologies->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allTechnologies->url($allTechnologies->lastPage()) }}"
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
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('addRoleForm');
  const nameInput = document.getElementById('modalRoleTechnology');

  // Function to show error
  function showError(input, message) {
    removeError(input);
    const div = document.createElement('div');
    div.className = 'text-danger small mt-1 error-msg';
    div.innerText = message;
    input.closest('.form-control-validation').appendChild(div);
    input.classList.add('is-invalid');
  }

  // Function to remove error
  function removeError(input) {
    const wrapper = input.closest('.form-control-validation');
    if (!wrapper) return;
    const err = wrapper.querySelector('.error-msg');
    if (err) err.remove();
    input.classList.remove('is-invalid');
  }

  // Live validation
  nameInput.addEventListener('input', () => {
    if (nameInput.value.trim() === '') {
      showError(nameInput, 'Technology name is required');
    } else {
      removeError(nameInput);
    }
  });

  // Form submit validation
  form.addEventListener('submit', function (e) {
    let isValid = true;

    // Validate Technology Name
    if (nameInput.value.trim() === '') {
      showError(nameInput, 'Technology name is required');
      isValid = false;
    }

    // Prevent form submission if invalid
    if (!isValid) e.preventDefault();
  });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.edit-technology').forEach(btn => {
    btn.addEventListener('click', function () {

      const id     = this.dataset.id;
      const name   = this.dataset.name;
      const status = this.dataset.status;

      // ✅ SET HIDDEN ID
      document.getElementById('editTechId').value = id;

      // ✅ SET NAME
      document.getElementById('editTechnologyName').value = name;

      // ✅ SET STATUS
      document.getElementById('editStatusActive').checked = status == 1;
      document.getElementById('editStatusFreeze').checked = status == 0;

      // ✅ OPEN MODAL
      new bootstrap.Modal(
        document.getElementById('editTechnologyModal')
      ).show();
    });
  });

});
</script>

<script>


function downloadTechnologiesCSV() {
    // Get current filter values
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    // Construct the URL with query parameters
    let url = "{{ route('download-technologies-csv.admin') }}";
    let params = new URLSearchParams();
    
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    
    // Redirect to the download URL
    window.location.href = url + (params.toString() ? '?' + params.toString() : '');
}
</script>


@endpush




@endsection