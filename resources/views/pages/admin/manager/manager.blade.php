@extends('layouts/layoutMaster')

@section('title', 'Managers')

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
  <h4 class="mt-6 mb-1">Managers</h4>
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
          <form method="GET" action="{{ route('managers') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search manager"
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadManagerCSV()">
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
            data-bs-target="#AddManagerModel"><span><i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span
                class="d-none d-sm-inline-block">Add Manager</span></span></button>
          <!-- Add manager Modal -->
          <div class="modal fade" id="AddManagerModel" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Add New Manager</h4>
                    <p class="text-body-secondary">Set role permissions</p>
                  </div>
                  <!-- Add role form -->
                  <form id="addRoleForm" class="row g-3" method="POST" action="{{route('add-manager')}}">
                    @csrf
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleName">Name</label>
                      <input type="text" id="name" name="name" class="form-control" placeholder="Enter manager name"
                        tabindex="-1" />
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="modalRoleName">Email</label>
                      <input type="text" id="email" name="email" class="form-control" placeholder="Enter manager email"
                        tabindex="-1" />
                    </div>
                    <div
                      class="col-12 col-md-6 form-control-validation mb-3 form-password-toggle form-control-validation">
                      <label class="form-label" for="password">Password</label>
                      <div class="input-group col-12 col-md-4 input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                      </div>
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="contact">Phone</label>
                      <input type="text" id="contact" name="contact" class="form-control"
                        placeholder="Enter manager phone" tabindex="-1" />
                    </div>



                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label for="flatpickr-date" class="form-label">Join Date</label>
                      <input type="text" class="form-control" name='join_date' placeholder="YYYY-MM-DD"
                        id="flatpickr-date" />
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="comission">Comission (Rs:)</label>
                      <input type="number" id="comission" name="comission" class="form-control"
                        placeholder="Enter comission" tabindex="-1" value="1000" />
                    </div>
                    <div class="col-12 col-md-6 form-control-validation mb-3">
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
                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label for="manager" class="form-label">Role</label>
                      <div class="form-check mb-0 me-1 me-lg-1 mt-2">
                        <input class="form-check-input" type="checkbox" name='manager' id="manager" />
                        <label class="form-check-label" for="manager"> Manager </label>
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

          {{-- Edit namager modal --}}
          <div class="modal fade" id="editManagerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Edit Manager</h4>
                    <p class="text-body-secondary">Update manager details and permissions</p>
                  </div>

                  <form id="editManagerForm" class="row g-3" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_manager_id" name="id">

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="edit_name">Name</label>
                      <input type="text" id="edit_name" name="name" class="form-control"
                        placeholder="Enter manager name" />
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="edit_email">Email</label>
                      <input type="text" id="edit_email" name="email" class="form-control"
                        placeholder="Enter manager email" />
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="edit_password">Password</label>
                      <div class="input-group input-group-merge">
                        <input type="password" id="edit_password" class="form-control" name="password"
                          placeholder="············" />
                        <span class="input-group-text cursor-pointer" id="toggleEditPassword" style="font-size: 20px;">
                          <i class="ti tabler-eye-off"></i>
                        </span>
                      </div>
                    </div>






                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="edit_contact">Phone</label>
                      <input type="text" id="edit_contact" name="contact" class="form-control"
                        placeholder="Enter manager phone" />
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label for="edit_flatpickr-date" class="form-label">Join Date</label>
                      <input type="text" class="form-control" name='join_date' placeholder="YYYY-MM-DD"
                        id="edit_flatpickr-date" />
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label" for="edit_comission">Commission (Rs:)</label>
                      <input type="number" id="edit_comission" name="comission" class="form-control"
                        placeholder="Enter commission" />
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label">Status</label>
                      <div class="d-flex gap-4">
                        <div class="form-check mt-2">
                          <input class="form-check-input" type="radio" name="status" id="editStatusActive" value="1" />
                          <label class="form-check-label" for="editStatusActive">Active</label>
                        </div>
                        <div class="form-check mt-2">
                          <input class="form-check-input" type="radio" name="status" id="editStatusFreeze" value="0" />
                          <label class="form-check-label" for="editStatusFreeze">Freeze</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-12 col-md-6 form-control-validation mb-3">
                      <label class="form-label">Role</label>
                      <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name='manager' id="edit_role_manager" />
                        <label class="form-check-label" for="edit_role_manager"> Manager </label>
                      </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>





          <!-- Manager Permissions Modal -->
          <div class="modal fade" id="ManagerPermissionsModel" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Manager Permissions</h4>
                    <p class="text-body-secondary">Set Manager Permissions</p>
                  </div>

                  <form id="addRoleForm" action="{{ route('manager.permissions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="manager_id" id="perm_manager_id">

                    <div class="table-responsive">
                      <table class="table table-flush-spacing">
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    <div class="col-12 text-end mt-4">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save Permissions</button>
                    </div>
                  </form>



                </div>
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
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">ETI‑ID</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="2" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Avatar
                  </span><span class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Full Name" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Phone</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Join date</span><span
                    class="dt-column-order"></span></th>
                {{-- <th data-dt-column="6" rowspan="1" colspan="1"
                  class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Internship Duration" tabindex="0">
                  <span class="dt-column-title" role="button">Internship
                    Duration</span><span class="dt-column-order"></span>
                </th> --}}

                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Commission</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Status</span><span class="dt-column-order"></span></th>
                <th data-dt-column="8" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Action"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



              </tr>
            </thead>
            <tbody>
              @forelse ($allManagers as $manager)
              <tr class="">
                <td><span class="text-heading text-nowrap"><small>{{$manager->eti_id}}</small></span>
                </td>
                <td class="">
                  <div class="d-flex justify-content-start align-items-center user-name">
                    <div class="avatar-wrapper">
                      @if ($manager->image)
                      <div class="avatar avatar-md me-4">
                        <img src="{{ 
    $manager->image
        ? (str_starts_with($manager->image, 'data:image')
            ? $manager->image
            : asset($manager->image)) 
        : '' 
    }}" alt="{{ $manager->name }}" class="rounded-circle" />
                      </div>
                      @else
                      <div class="avatar avatar-md me-4">
                        <span class="avatar-initial rounded-circle bg-label-warning">
                          {{ strtoupper(substr($manager->name, 0, 2)) }}
                        </span>
                      </div>
                      @endif
                    </div>


                  </div>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$manager->name}}</span>
                </td>
                <td>@if ($manager->contact)
                  <span class="text-heading text-nowrap"><i style="color: rgb(40, 186, 40)" class="icon-base me-1 ti tabler-phone icon-22px"></i>{{$manager->contact}}</span>
                  @else
                  <span class="text-heading text-nowrap">N/A</span>
                  @endif
                </td>
                <td><span class="text-heading text-nowrap">{{$manager->join_date}}</span></td>
                <td><span class="fw-bold">Rs: </span><span
                    class="text-heading text-nowrap">{{$manager->comission}}</span>
                </td>
                <td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  '1' => 'bg-label-success',
                  '0' => 'bg-label-danger',
                  ];

                  $status = strtolower($manager->status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">@if ($manager->status == 1)
                    Active
                    @else
                    Freeze
                    @endif</span>
                </td>

                <td>
                  <div class="d-flex align-items-center">
                    <div class="dropdown">
                      <a href="javascript:;"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end m-0">
                        <a href="javascript:;" class="dropdown-item edit-btn" data-bs-toggle="modal"
                          data-bs-target="#editManagerModal" data-id="{{ $manager->manager_id }}"
                          data-name="{{ $manager->name }}" data-email="{{ $manager->email }}"
                          data-password="{{ $manager->password }}" {{-- Add this line --}}
                          data-contact="{{ $manager->contact }}" data-join_date="{{ $manager->join_date }}"
                          data-comission="{{ $manager->comission }}" data-status="{{ $manager->status }}">
                          Edit
                        </a>
                        <a href="javascript:;" class="dropdown-item permission-btn" data-bs-toggle="modal"
                          data-bs-target="#ManagerPermissionsModel" data-id="{{ $manager->manager_id }}">
                          Permissions
                        </a>
                      </div>
                    </div>

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
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $allManagers->firstItem() ?? 0 }} to {{ $allManagers->lastItem() ?? 0 }} of {{
            $allManagers->total()
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
                <li class="dt-paging-button page-item {{ $allManagers->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allManagers->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Previous Page --}}
                <li class="dt-paging-button page-item {{ $allManagers->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allManagers->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Page Numbers --}}
                @foreach ($allManagers->getUrlRange(max(1, $allManagers->currentPage() - 2),
                min($allManagers->lastPage(),
                $allManagers->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $allManagers->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Page --}}
                <li
                  class="dt-paging-button page-item {{ $allManagers->currentPage() == $allManagers->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allManagers->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Last Page --}}
                <li
                  class="dt-paging-button page-item {{ $allManagers->currentPage() == $allManagers->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $allManagers->url($allManagers->lastPage()) }}" aria-label="Last">
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
    const nameInput     = document.getElementById('name');
    const emailInput    = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const phoneInput    = document.getElementById('contact');
    // const dateInput     = document.getElementById('flatpickr-date');
    const commissionInput = document.getElementById('comission');
    const managerCheckbox = document.getElementById('manager');

    // ✅ 1. Initialize Flatpickr with Today's Date
   const dateInput = document.getElementById('flatpickr-date');
    const addModalEl = document.getElementById('AddManagerModel');

    // 1. FIXED FLATPICKR INITIALIZATION
    const addPicker = flatpickr(dateInput, {
        dateFormat: "Y-m-d",
        defaultDate: "today",
        allowInput: true,
        monthSelectorType: 'static',
        static: true, // Crucial for modals
        appendTo: addModalEl.querySelector('.modal-content'), // Forces picker to stay inside modal
        onOpen: function(selectedDates, dateStr, instance) {
            instance.calendarContainer.style.zIndex = "9999"; // Ensures it sits on top of the modal
        }
    });
    
    function showError(input, message) {
        removeError(input);
        const div = document.createElement('div');
        div.className = 'text-danger small mt-1 error-msg';
        div.innerText = message;
        input.closest('.form-control-validation')?.appendChild(div);
        input.classList.add('is-invalid');
    }

    function removeError(input) {
        const wrapper = input.closest('.form-control-validation');
        if (!wrapper) return;
        const err = wrapper.querySelector('.error-msg');
        if (err) err.remove();
        input.classList.remove('is-invalid');
    }

    function validateName() {
        nameInput.value.trim() === ''
            ? showError(nameInput, 'Name is required')
            : removeError(nameInput);
    }

    function validateEmail() {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        !regex.test(emailInput.value.trim())
            ? showError(emailInput, 'Valid email is required')
            : removeError(emailInput);
    }

    function validatePassword() {
        passwordInput.value.length < 6
            ? showError(passwordInput, 'Minimum 6 characters required')
            : removeError(passwordInput);
    }

    function validatePhone() {
        phoneInput.value.length < 10
            ? showError(phoneInput, 'Valid phone number required')
            : removeError(phoneInput);
    }

    function validateDate() {
        dateInput.value.trim() === ''
            ? showError(dateInput, 'Join date is required')
            : removeError(dateInput);
    }

    function validateCommission() {
        const value = commissionInput.value.trim();
        if (value === '') {
            showError(commissionInput, 'Commission is required');
        } else if (isNaN(value)) {
            showError(commissionInput, 'Commission must be a number');
        } else if (Number(value) < 0) {
            showError(commissionInput, 'Commission cannot be negative');
        } else {
            removeError(commissionInput);
        }
    }

    function validatePermission() {
        const wrapper = managerCheckbox.closest('.form-control-validation');
        let err = wrapper.querySelector('.error-msg');

        if (!managerCheckbox.checked) {
            if (!err) {
                err = document.createElement('div');
                err.className = 'text-danger small mt-1 error-msg';
                err.innerText = 'Manager role must be selected';
                wrapper.appendChild(err);
            }
        } else if (err) {
            err.remove();
        }
    }

    // ✅ Live validation
    nameInput.addEventListener('input', validateName);
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);
    phoneInput.addEventListener('input', validatePhone);
    dateInput.addEventListener('change', validateDate); // Triggers when date picked
    managerCheckbox.addEventListener('change', validatePermission);
    commissionInput.addEventListener('input', validateCommission);

    // ✅ Submit validation
    form.addEventListener('submit', function (e) {
        validateName();
        validateEmail();
        validatePassword();
        validatePhone();
        validateDate();
        validateCommission(); 
        validatePermission();

        if (form.querySelectorAll('.is-invalid, .error-msg').length > 0) {
            e.preventDefault();
        }
    });
});
</script>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    // 1. SELECT ELEMENTS
    const editForm = document.getElementById('editManagerForm');
    const editModalEl = document.getElementById('editManagerModal');
    const editPasswordInput = document.getElementById('edit_password');
    const toggleEditPassword = document.getElementById('toggleEditPassword');
    const editManagerCheckbox = document.getElementById('edit_role_manager');
    const editDateInput = document.getElementById('edit_flatpickr-date');

    // 2. INITIALIZE FLATPICKR
    const editPicker = flatpickr(editDateInput, {
        dateFormat: "Y-m-d",
        monthSelectorType: 'static',
        static: true,
        appendTo: editModalEl.querySelector('.modal-content')
    });

    // 3. POPULATE DATA
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const data = this.dataset;
            editForm.action = `/admin/managers/update/${data.id}`;

            document.getElementById('edit_manager_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_contact').value = data.contact;
            document.getElementById('edit_comission').value = data.comission;
            document.getElementById('edit_password').value = data.password;

            if (data.join_date && data.join_date !== "N/A") {
                editPicker.setDate(data.join_date, true);
            } else {
                editPicker.clear();
            }

            if(data.status == "1") {
                document.getElementById('editStatusActive').checked = true;
            } else {
                document.getElementById('editStatusFreeze').checked = true;
            }

            // Set checkbox state
            editManagerCheckbox.checked = true;

            // Reset Toggle UI
            editPasswordInput.type = 'password';
            const icon = toggleEditPassword?.querySelector('i');
            if (icon) {
                icon.classList.remove('tabler-eye');
                icon.classList.add('tabler-eye-off');
            }
        });
    });

    // 4. VALIDATION HELPERS
    function showError(input, message) {
        removeError(input);
        const div = document.createElement('div');
        div.className = 'text-danger small mt-1 error-msg';
        div.innerText = message;
        // Finding the parent container to append the error
        input.closest('.form-control-validation')?.appendChild(div);
        input.classList.add('is-invalid');
    }

    function removeError(input) {
        const wrapper = input.closest('.form-control-validation');
        if (wrapper) {
            const err = wrapper.querySelector('.error-msg');
            if (err) err.remove();
            input.classList.remove('is-invalid');
        }
    }

    // 5. VALIDATION LOGIC (ADDED MANAGER CHECK)
    const validate = {
        name: () => {
            const el = document.getElementById('edit_name');
            el.value.trim() === '' ? showError(el, 'Name is required') : removeError(el);
        },
        email: () => {
            const el = document.getElementById('edit_email');
            !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim()) ? showError(el, 'Valid email required') : removeError(el);
        },
        phone: () => {
            const el = document.getElementById('edit_contact');
            el.value.length < 10 ? showError(el, 'Valid phone required') : removeError(el);
        },
        date: () => {
            editDateInput.value.trim() === '' ? showError(editDateInput, 'Join date is required') : removeError(editDateInput);
        },
        comm: () => {
            const el = document.getElementById('edit_comission');
            el.value.trim() === '' || isNaN(el.value) ? showError(el, 'Commission must be a number') : removeError(el);
        },
        password: () => {
            editPasswordInput.value.length < 6 ? showError(editPasswordInput, 'Min 6 characters required') : removeError(editPasswordInput);
        },
        // NEW: Manager Checkbox validation
        manager: () => {
            if (!editManagerCheckbox.checked) {
                showError(editManagerCheckbox, 'Manager role must be selected');
            } else {
                removeError(editManagerCheckbox);
            }
        }
    };

    // 6. ATTACH LISTENERS
    document.getElementById('edit_name').addEventListener('input', validate.name);
    document.getElementById('edit_email').addEventListener('input', validate.email);
    document.getElementById('edit_contact').addEventListener('input', validate.phone);
    editDateInput.addEventListener('change', validate.date); 
    document.getElementById('edit_comission').addEventListener('input', validate.comm);
    editPasswordInput.addEventListener('input', validate.password);
    editManagerCheckbox.addEventListener('change', validate.manager);

    // 7. FORM SUBMIT
    editForm.addEventListener('submit', function (e) {
        validate.name();
        validate.email();
        validate.phone();
        validate.date();
        validate.comm();
        validate.password();
        validate.manager(); // Run the manager check on submit

        if (this.querySelectorAll('.is-invalid, .error-msg').length > 0) {
            e.preventDefault();
        }
    });

    // 7. PASSWORD TOGGLE (FIXED FOR EDIT MODAL)
if (toggleEditPassword) {
    // We use a fresh listener to ensure it targets the correct input
    toggleEditPassword.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation(); // Prevents modal interference
        
        const icon = this.querySelector('i');
        const isPwd = editPasswordInput.type === 'password';

        // Toggle the input type
        editPasswordInput.type = isPwd ? 'text' : 'password';

        // Toggle the icons correctly using replace for better reliability
        if (isPwd) {
            icon.classList.replace('tabler-eye-off', 'tabler-eye');
        } else {
            icon.classList.replace('tabler-eye', 'tabler-eye-off');
        }
    });
}

// 3. POPULATE DATA (Small addition here to reset the icon when modal opens)
document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // ... (your existing population code)

        // Add this specific reset logic here
        editPasswordInput.type = 'password';
        const icon = toggleEditPassword?.querySelector('i');
        if (icon) {
            icon.classList.add('tabler-eye-off');
            icon.classList.remove('tabler-eye');
        }
    });
});

    // 9. MODAL RESET
    editModalEl.addEventListener('hidden.bs.modal', function () {
        editForm.reset();
        editPicker.clear();
        editForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        editForm.querySelectorAll('.error-msg').forEach(el => el.remove());
    });
});
</script>




<script>
document.addEventListener('DOMContentLoaded', function () {

  const permissionModal = document.getElementById('ManagerPermissionsModel');
  const techTableBody = permissionModal.querySelector('table tbody');

  document.querySelectorAll('.permission-btn').forEach(btn => {
    btn.addEventListener('click', function () {

      const managerId = this.dataset.id;
      document.getElementById('perm_manager_id').value = managerId;

      techTableBody.innerHTML = `
        <tr>
          <td colspan="2" class="text-center">
            <div class="spinner-border text-primary"></div>
          </td>
        </tr>`;

      Promise.all([
        fetch('{{ route("active.technologies.admin") }}').then(r => r.json()),
        fetch('{{ url("admin/manager") }}/' + managerId + '/permissions').then(r => r.json())
      ]).then(([techRes, permRes]) => {

        if (!techRes.success) return;

        const savedPermissions = permRes.data ?? {};
        techTableBody.innerHTML = '';

        // Header row
        techTableBody.insertAdjacentHTML('beforeend', `
          <tr>
            <td class="fw-medium text-heading">Manager Access <i class="icon-base ti tabler-info-circle icon-xs"
         data-bs-toggle="tooltip"
         data-bs-placement="top"
         title="Allows full access to the system">
      </i></td>
            <td class="text-end">
              <div class="form-check" style="display: flex; justify-content: end;">
                <input class="form-check-input me-2" type="checkbox" id="selectAll">
                <label for='selectAll' class="form-check-label">Select All</label>
              </div>
            </td>
          </tr>
        `);
          permissionModal.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
  new bootstrap.Tooltip(el);
});
        techRes.data.forEach(tech => {
          const checkedRemote =
            savedPermissions[tech.tech_id]?.includes('Remote') ? 'checked' : '';
          const checkedOnsite =
            savedPermissions[tech.tech_id]?.includes('Onsite') ? 'checked' : '';

          techTableBody.insertAdjacentHTML('beforeend', `
            <tr>
              <td class="fw-medium">${tech.technology}</td>
              <td>
                <div class="d-flex justify-content-end">
                  <div class="form-check me-4">
                    <input class="form-check-input tech-checkbox"
                      type="checkbox"
                      id="remote_${tech.tech_id}"
                      name="permissions[${tech.tech_id}][]"
                      value="Remote"
                      ${checkedRemote}>
                    <label class="form-check-label" for="remote_${tech.tech_id}">Remote</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input tech-checkbox"
                      type="checkbox"
                      id="onsite_${tech.tech_id}"
                      name="permissions[${tech.tech_id}][]"
                      value="Onsite"
                      ${checkedOnsite}>
                    <label class="form-check-label" for="onsite_${tech.tech_id}">Onsite</label>
                  </div>
                </div>
              </td>
            </tr>
          `);
        });
        // ✅ Auto-check Select All if all permissions are selected
const allCheckboxes = document.querySelectorAll('.tech-checkbox');
const selectAll = document.getElementById('selectAll');

if (allCheckboxes.length > 0) {
  const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
  selectAll.checked = allChecked;
}

        // Select all logic
        document.getElementById('selectAll').addEventListener('change', function () {
          document.querySelectorAll('.tech-checkbox').forEach(cb => {
            cb.checked = this.checked;
          });
        });

      });
    });
  });
});
</script>
<script>
  function downloadManagerCSV() {
    window.location.href = "{{ route('managers.export.admin') }}";
}
</script>
@endpush







@endsection