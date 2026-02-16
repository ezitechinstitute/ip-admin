@extends('layouts/layoutMaster')

@section('title', 'University')

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
  <h4 class="mt-6 mb-1">Universities</h4>
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
          <form method="GET" action="{{ route('university.admin') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search University"
              value="{{ request('search') }}">

            <select name="status" id="statusFilter" class="form-select text-capitalize">
              <option value="">Select Uni Status</option>

              <option value="1" {{ request('status')==='1' ? 'selected' : '' }}>
                Active
              </option>

              <option value="0" {{ request('status')==='0' ? 'selected' : '' }}>
                Deactive
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
                University</span></span></button>
          <!-- Add University -->
          <div class="modal fade" id="addTechnologyModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Add New University</h4>
                  </div>
                  <form id="addRoleForm" class="row g-3" method="POST" action="{{route('add-university.admin')}}">
                    @csrf
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleUniName">University Name</label>
                      <input type="text" id="modalRoleUniName" name="uni_name" class="form-control"
                        placeholder="Enter university name" tabindex="-1" />
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleUniEmail">Email (Optional)</label>
                      <input type="text" id="modalRoleUniEmail" name="uni_email" class="form-control"
                        placeholder="Enter email" tabindex="-1" />
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleUniPhone">Phone (Optional)</label>
                      <input type="text" id="modalRoleUniPhone" name="uni_phone" class="form-control"
                        placeholder="Enter phone" tabindex="-1" />
                    </div>

                    <div
                      class="col-12 col-md-6 form-control-validation mb-3 form-password-toggle form-control-validation">
                      <label class="form-label" for="password">Password (Optional)</label>
                      <div class="input-group col-12 col-md-4 input-group-merge">
                        <input type="password" id="password" class="form-control" name="uni_password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                      </div>
                    </div>


                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label">University Status</label>

                      <div class="d-flex gap-4">
                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="uni_status" id="statusActive" value="1"
                            checked />
                          <label class="form-check-label" for="statusActive">Active</label>
                        </div>

                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="uni_status" id="statusDeactive"
                            value="0" />
                          <label class="form-check-label" for="statusDeactive">Freeze</label>
                        </div>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label">Account Status</label>

                      <div class="d-flex gap-4">
                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="account_status" id="statusActivate"
                            value="1" checked />
                          <label class="form-check-label" for="statusActivate">Activate</label>
                        </div>

                        <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                          <input class="form-check-input" type="radio" name="account_status" id="statusDeactivate"
                            value="0" />
                          <label class="form-check-label" for="statusDeactivate">Deactivate</label>
                        </div>
                      </div>
                    </div>





                    <div class="col-12 text-end mt-3">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                      <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Add University -->
          <!-- Edit University Modal -->
          <div class="modal fade" id="editUniversityModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content p-3">

                <div class="modal-header">
                  <div class="text-start mb-4">
                    <h4 class="modal-title">Edit University</h4>
                  </div>
                  <button type="button" style="inset-block-start: 0.5rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form id="editUniversityForm" class="" method="POST" action="{{ route('university.update.admin') }}">
                  @csrf
                  @method('PUT')

                  <input type="hidden" name="id" id="editUniId">

                  <div class="modal-body row g-3">

                    <div class="mb-3 col-12 col-md-6 form-control-validation">
                      <label class="form-label">University Name</label>
                      <input type="text" placeholder="Enter university name" id="editUniName" name="uni_name"
                        class="form-control" required>
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label">Email (Optional)</label>
                      <input type="text" placeholder="Enter university email" id="editUniEmail" name="uni_email"
                        class="form-control">
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label">Phone (Optional)</label>
                      <input type="text" id="editUniPhone" placeholder="Enter university phone" name="uni_phone"
                        class="form-control">
                    </div>

                    <div class="mb-3 col-12 col-md-6 form-control-validation form-password-toggle">
                      <label class="form-label" for="edit_password">Password (Optional)</label>

                      <div class="input-group input-group-merge">
                        <input type="password" id="edit_password" name="uni_password" class="form-control"
                          placeholder="••••••••" maxlength="8" />

                        <span class="input-group-text cursor-pointer">
                          <i class="icon-base ti tabler-eye-off"></i>
                        </span>
                      </div>
                    </div>


                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label">University Status</label>
                      <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="uni_status" id="editUniActive" value="1">
                          <label class="form-check-label">Active</label>
                        </div>

                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="uni_status" id="editUniInactive" value="0">
                          <label class="form-check-label">Deactive</label>
                        </div>
                      </div>
                    </div>

                    <div class="mb-3 col-12 col-md-6">
                      <label class="form-label">Account Status</label>
                      <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="account_status" id="editAccActive"
                            value="1">
                          <label class="form-check-label">Activate</label>
                        </div>

                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="account_status" id="editAccInactive"
                            value="0">
                          <label class="form-check-label">Deactivate</label>
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

            <thead class="border-top sticky-top bg-card">
              <tr>

                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="#ID" tabindex="0"><span class="dt-column-title" role="button">ETI-ID</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Technology Name" tabindex="0"><span class="dt-column-title" role="button">University
                    name</span><span class="dt-column-order"></span></th>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="#ID" tabindex="0"><span class="dt-column-title" role="button">EMAIL</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="#ID" tabindex="0"><span class="dt-column-title" role="button">PHONE</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="#ID" tabindex="0"><span class="dt-column-title" role="button">Interns</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Status"><span
                    class="dt-column-title text-nowrap">Uni Status</span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Status"><span
                    class="dt-column-title">Acc Status</span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Action"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



              </tr>
            </thead>
            <tbody>
              @forelse ($allUniversities as $university)
              <tr class="">
                <td><span class="text-heading text-nowrap"><small>
                      @if ($university->uti)
                      {{$university->uti}}
                      @else
                      -
                      @endif
                    </small></span>
                </td>

                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"><i
                      class="icon-base me-2 text-primary ti tabler-school icon-22px"></i>
                    {{$university->uni_name}}</span>
                </td>
                <td>
                  @if ($university->uni_email)
                  <span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-mail me-2 text-danger icon-22px"></i>
                    <small>{{$university->uni_email}}</small></span>
                  @else
                  -
                  @endif
                </td>
                <td>@if ($university->uni_phone)
                  <span class="text-truncate d-flex align-items-center text-heading text-nowrap"><i
                      style="color: rgb(40, 186, 40)" class="icon-base me-1 ti tabler-phone icon-22px"></i>
                    {{$university->uni_phone}}</span>
                  @else
                  -
                  @endif
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$university->interns_count}}</span>
                </td>

                <td>
                  @php
                  $statusClasses = [
                  1 => 'bg-label-success',
                  0 => 'bg-label-danger',
                  ];

                  $status = $university->uni_status;
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }}">
                    {{ $status == 1 ? 'Active' : 'Freeze' }}
                  </span>
                </td>

                <td>
                  @php
                  $statusClasses = [
                  1 => 'bg-label-success',
                  0 => 'bg-label-danger',
                  ];

                  $status = $university->account_status;
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-danger';
                  @endphp

                  <span class="badge {{ $badgeClass }}">
                    {{ $status == 1 ? 'Activated' : 'Deactivated' }}
                  </span>
                </td>


                {{-- <td><span class="text-heading text-nowrap">{{$university->intern_type}}</span></td> --}}
                <td>
                  <div class="d-flex align-items-center">



                    <a href="javascript:void(0)"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-university"
                      data-id="{{ $university->uni_id }}" data-name="{{ $university->uni_name }}"
                      data-email="{{ $university->uni_email }}" data-phone="{{ $university->uni_phone }}"
                      data-password="{{ $university->uni_password }}" data-uni_status="{{ $university->uni_status }}"
                      data-account_status="{{ $university->account_status }}">
                      <i class="icon-base ti tabler-edit icon-22px"></i>
                    </a>




                  </div>

                </td>


              </tr>



              @empty
              <tr>
                <td colspan="8">
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
            Showing {{ $allUniversities->firstItem() ?? 0 }} to {{ $allUniversities->lastItem() ?? 0 }} of {{
            $allUniversities->total()
            ??
            0 }} entries
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                <li class="dt-paging-button page-item {{ $allUniversities->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allUniversities->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                <li class="dt-paging-button page-item {{ $allUniversities->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allUniversities->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                @foreach ($allUniversities->getUrlRange(max(1, $allUniversities->currentPage() - 2),
                min($allUniversities->lastPage(),
                $allUniversities->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $allUniversities->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                <li
                  class="dt-paging-button page-item {{ $allUniversities->currentPage() == $allUniversities->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allUniversities->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                <li
                  class="dt-paging-button page-item {{ $allUniversities->currentPage() == $allUniversities->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allUniversities->url($allUniversities->lastPage()) }}"
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

    // Inputs
    const nameInput  = form.querySelector('input[name="uni_name"]');
    const emailInput = form.querySelector('input[name="uni_email"]');
    const phoneInput = form.querySelector('input[name="uni_phone"]');
    const passInput  = form.querySelector('input[name="uni_password"]');

    // Show error
    function showError(input, message) {
        removeError(input);

        const error = document.createElement('div');
        error.className = 'text-danger small mt-1 error-msg';
        error.innerText = message;

        input.closest('.form-control-validation')
             .appendChild(error);

        input.classList.add('is-invalid');
    }

    // Remove error
    function removeError(input) {
        const wrapper = input.closest('.form-control-validation');
        if (!wrapper) return;

        const error = wrapper.querySelector('.error-msg');
        if (error) error.remove();

        input.classList.remove('is-invalid');
    }

    // 🔴 Live validation (ONLY University Name)
    nameInput.addEventListener('input', function () {
        if (this.value.trim() === '') {
            showError(this, 'University name is required');
        } else {
            removeError(this);
        }
    });

    // 🟢 Optional fields → clear error if user types
    [emailInput, phoneInput, passInput].forEach(input => {
        if (!input) return;
        input.addEventListener('input', function () {
            removeError(this);
        });
    });

    // 🚫 Submit validation
    form.addEventListener('submit', function (e) {

        let isValid = true;

        if (nameInput.value.trim() === '') {
            showError(nameInput, 'University name is required');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
        }
    });

});
</script>



<script>
  document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('.edit-university').forEach(btn => {
    btn.addEventListener('click', function () {

      document.getElementById('editUniId').value    = this.dataset.id;
      document.getElementById('editUniName').value  = this.dataset.name;
      document.getElementById('editUniEmail').value = this.dataset.email ?? '';
      document.getElementById('editUniPhone').value = this.dataset.phone ?? '';

      document.getElementById('edit_password').value = this.dataset.password ?? '';

      // University Status
      document.getElementById('editUniActive').checked   = this.dataset.uni_status == 1;
      document.getElementById('editUniInactive').checked = this.dataset.uni_status == 0;

      // Account Status
      document.getElementById('editAccActive').checked   = this.dataset.account_status == 1;
      document.getElementById('editAccInactive').checked = this.dataset.account_status == 0;

      new bootstrap.Modal(
        document.getElementById('editUniversityModal')
      ).show();
    });
  });

});
</script>

<script>
  function downloadTechnologiesCSV() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    const url = new URL("{{ route('university.export.admin') }}", window.location.origin);
    if (search) url.searchParams.append('search', search);
    if (status) url.searchParams.append('status', status);

    window.location.href = url.href;
}
</script>


@endpush




@endsection