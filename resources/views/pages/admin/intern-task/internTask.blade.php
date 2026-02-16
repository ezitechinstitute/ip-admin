@extends('layouts/layoutMaster')

@section('title', 'Intern Tasks')

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
  <h4 class="mt-6 mb-1">Intern Tasks</h4>
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
          <form method="GET" action="{{ route('intern.tasks') }}" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control"
              placeholder="Search Intern Tasks..." value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <select name="status" id="statusFilter" class="form-select text-capitalize">
              <option value="">Select Status</option>

              @foreach (['Ongoing','Expired', 'Completed'] as $status)
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadProjectTasksCSV()">
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
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Name
                  </span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Task Title</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Start Date</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">End Date</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Duration</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Days</span><span class="dt-column-order"></span>
                </th>

                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">STATUS</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="7" rowspan="1" colspan="1" class="dt-orderable-none" aria-label="Join Date"><span
                    class="dt-column-title">Action</span><span class="dt-column-order"></span></th>



              </tr>
            </thead>
            <tbody>
              @forelse ($tasks as $task)
              <tr class="">
                <td><span class="text-heading text-nowrap">{{$task->intern->name}}</span></td>

                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_title}}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_start}}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_end}}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$task->task_duration}}</span>
                </td>

                <td><span class="text-heading text-nowrap">{{$task->task_days}}</span></td>
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
                  ];

                  $status = strtolower($task->task_status);
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span>
                </td>

                <td>
                  <div class="d-flex align-items-center">

                    <a href="javascript:void(0);"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-intern"
                      data-bs-toggle="modal" data-bs-target="#editInternModal" data-id="{{ $task->task_id }}"
                      data-title="{{ $task->task_title }}" data-status="{{ $task->task_status }}">
                      <i class="icon-base ti tabler-edit icon-22px"></i>
                    </a>

                    {{-- Edit Project Task - Start --}}
                    <div class="modal fade" id="editInternModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-md modal-simple modal-dialog-centered">
                        <div class="modal-content p-2">
                          <div class="modal-body">
                            <button type="button" class="btn-close"
                              style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                              data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-start mb-6">
                              <h4 class="role-title">Edit</h4>
                            </div>

                            <form id="editInternForm" class="row g-3" action="{{route('update.intern.task.admin')}}"
                              method="POST">
                              @csrf
                              <input type="hidden" id="task_id" name="task_id">

                              <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="task_title">Task Title</label>
                                <input type="text" id="task_title" required name="task_title" class="form-control" />
                              </div>



                              <div class="col-12 col-md-6 mb-3">
                                <label class="form-label" for="int_status">Status</label>
                                <select name="task_status" required id="task_status"
                                  class="form-select text-capitalize">
                                  <option value="Ongoing">Ongoing</option>
                                  <option value="Expired">Expired</option>
                                  <option value="Completed">Completed</option>
                                </select>
                              </div>

                              <div class="col-12 text-end">
                                <button type="button" class="btn btn-label-secondary me-2"
                                  data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    {{-- Edit Project Task - End --}}

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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {

    const editModal = document.getElementById('editInternModal');

    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {

            const button = event.relatedTarget;

            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const status = button.getAttribute('data-status');

            // Set values
            document.getElementById('task_id').value = id;
            document.getElementById('task_title').value = title;
            document.getElementById('task_status').value = status;

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
  function downloadProjectTasksCSV() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    let exportUrl = "{{ route('admin.export-intern-tasks') }}";
    let params = new URLSearchParams({
        search: search,
        status: status
    });

    window.location.href = exportUrl + "?" + params.toString();
}
</script>
@endpush








@endsection