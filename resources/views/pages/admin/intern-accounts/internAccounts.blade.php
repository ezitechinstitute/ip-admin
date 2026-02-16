@extends('layouts/layoutMaster')

@section('title', 'Intern Accounts')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@endsection

@section('content')

<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Intern Accounts</h4>
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
          <form method="GET" action="{{ route('intern-accounts-admin') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search Internee"
              value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <select name="status" id="statusFilter" class="form-select text-capitalize">
              <option value="">Select Status</option>

              @foreach (['Test','Active'] as $status)
              @php $slug = strtolower($status); @endphp

              <option value="{{ $slug }}" {{ request('status')==$slug ? 'selected' : '' }}>
                {{ $status }}
              </option>
              @endforeach
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadInternAccountsCSV()">
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
          style="max-height: 500px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">





            <thead class="border-top sticky-top bg-card">
              <tr>


                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">ETI-ID
                  </span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Email</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">PASSWORD</span><span
                    class="dt-column-order"></span></th>
                {{-- <th data-dt-column="6" rowspan="1" colspan="1"
                  class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Internship Duration" tabindex="0">
                  <span class="dt-column-title" role="button">Internship
                    Duration</span><span class="dt-column-order"></span>
                </th> --}}
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">TECH</span><span class="dt-column-order"></span>
                </th>
                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">STATUS</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



              </tr>
            </thead>
            <tbody>
              @forelse ($internAccounts as $intern)
              <tr class="">
                <td><span class="text-heading text-nowrap"><small>{{$intern->eti_id}}</small></span></td>

                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$intern->name}}</span>
                </td>
                <td><span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i><small>{{$intern->email}}</small></span>
                </td>
                <td><span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-key me-1 text-warning icon-22px"></i>*********</span></td>
                {{-- <td><span class="text-heading text-nowrap">{{$intern->duration}}</span></td> --}}
                <td><span class="text-heading text-nowrap"><i
                      class="icon-base ti tabler-cpu me-1 text-primary icon-22px"></i>{{$intern->int_technology}}</span>
                </td>
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

                  $status = strtolower($intern->int_status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                </td>

                {{-- <td><span class="text-heading text-nowrap">{{$intern->intern_type}}</span></td> --}}
                <td>
                  <div class="d-flex align-items-center">
                    







                    <div class="dropdown">
                      <a href="javascript:;"
                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                      </a>

                      <div class="dropdown-menu dropdown-menu-end m-0">

                        <a href="{{route('view.profile.interne.account.admin', $intern->int_id)}}"
                          class="dropdown-item permission-btn">
                          View Profile
                        </a>
                        <a href="javascript:;" class="dropdown-item edit-intern" data-bs-toggle="modal" data-bs-target="#editInternModal" data-id="{{ $intern->int_id }}"
                      data-name="{{ $intern->name }}" data-email="{{ $intern->email }}"
                      data-technology="{{ $intern->int_technology }}"
                      data-status="{{ strtolower($intern->int_status) }}">
                          Edit
                        </a>

                       



                      </div>
                    </div>





                    {{-- Edit Interne Account - Start --}}
                    <div class="modal fade" id="editInternModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
                        <div class="modal-content p-2">
                          <div class="modal-body">
                            <button type="button" class="btn-close"
                              style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                              data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-start mb-6">
                              <h4 class="role-title">Edit Intern Account</h4>
                            </div>

                            <form id="editInternForm" class="row g-3" action="{{route('update-intern-account')}}"
                              method="POST">
                              @csrf
                              <input type="hidden" id="int_id" name="int_id">

                              <div class="col-6 mb-3">
                                <label class="form-label" for="name">Name</label>
                                <input type="text" id="name" name="name" class="form-control" required />
                              </div>

                              <div class="col-6 mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required />
                              </div>

                              <div class="col-6 mb-3">
                                <label class="form-label" for="int_technology">Technology</label>
                                <input type="text" id="int_technology" name="int_technology" class="form-control"
                                  required />
                              </div>

                              <div class="col-6 mb-3">
                                <label class="form-label" for="int_status">Status</label>
                                <select name="int_status" id="int_status" required class="form-select text-capitalize">
                                  <option value="active">Active</option>
                                  <option value="test">Test</option>
                                  <option value="freeze">Freeze</option>
                                </select>
                              </div>

                              <div class="col-12 text-end">
                                <button type="button" class="btn btn-label-secondary me-2"
                                  data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update Account</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    {{-- Edit Interne Account - End --}}










                 
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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editInternModal');
    
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const technology = button.getAttribute('data-technology');
            const status = button.getAttribute('data-status');

            // Update the modal's content
            const modalBody = editModal.querySelector('.modal-body');
            
            modalBody.querySelector('#int_id').value = id;
            modalBody.querySelector('#name').value = name;
            modalBody.querySelector('#email').value = email;
            modalBody.querySelector('#int_technology').value = technology;
            
            // Set the dropdown value
            const statusSelect = modalBody.querySelector('#int_status');
            if (statusSelect) {
                statusSelect.value = status;
            }
        });
    }
});
</script>
@endpush


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


<script>
function downloadInternAccountsCSV() {
    // Current URL se search aur status parameters pakarna
    const urlParams = new URLSearchParams(window.location.search);
    const search = urlParams.get('search') || '';
    const status = urlParams.get('status') || '';

    // Export route par redirect karna parameters ke sath
    const exportUrl = "{{ route('export.intern.csv.admin') }}?" + 
                      "search=" + encodeURIComponent(search) + 
                      "&status=" + encodeURIComponent(status);
    
    window.location.href = exportUrl;
}
</script>
@endpush








@endsection