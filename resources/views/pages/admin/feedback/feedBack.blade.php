@extends('layouts/layoutMaster')

@section('title', 'FeedBack')

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
  <h4 class="mt-6 mb-1">FeedBack & Complaints</h4>
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
      <div class="row m-3 my-6 justify-content-between">
       


         <form method="GET" action="{{ route('feedback.admin') }}" style="justify-content: space-between" id="filterForm" class="d-flex gap-2 justify-between align-items-center">
  
  <div>
    <!-- Per Page -->
   <select name="per_page" class="form-select" onchange="document.getElementById('filterForm').submit()">
    <option value="15" {{ (isset($perPage) ? $perPage : request('per_page')) == 15 ? 'selected' : '' }}>15</option>
    <option value="25" {{ (isset($perPage) ? $perPage : request('per_page')) == 25 ? 'selected' : '' }}>25</option>
    <option value="50" {{ (isset($perPage) ? $perPage : request('per_page')) == 50 ? 'selected' : '' }}>50</option>
    <option value="100" {{ (isset($perPage) ? $perPage : request('per_page')) == 100 ? 'selected' : '' }}>100</option>
</select>
  </div>
<div class="d-flex gap-3">
  <!-- Search -->
  <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search feedback..."
         value="{{ request('search') }}">
<style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
  <!-- Status -->
  <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
    <option value="">Select Status</option>
    @foreach (['Open','Resolved'] as $status)
      @php $slug = strtolower($status); @endphp
      <option value="{{ $slug }}" {{ request('status') == $slug ? 'selected' : '' }}>{{ $status }}</option>
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadFeedbackCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
                </a>
              </div>
            </div>
            @endif
</div>

</form>

      </div>
      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
          style="max-height: 500px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">
            <thead class="border-top sticky-top bg-card">
              <tr>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">#
                  </span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Name</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Feedback</span><span
                    class="dt-column-order"></span></th>

                
                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">STATUS</span><span
                    class="dt-column-order"></span></th>
                    <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Action</span><span
                    class="dt-column-order"></span></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($feedback as $fb)
              <tr class="">

                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ $loop->iteration + ($feedback->currentPage()-1)*$feedback->perPage() }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$fb->internee_name}}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$fb->feedback_text}}</span>
                </td>
                
                <td>
    @php
        // Map statuses to Bootstrap badge classes
        $statusClasses = [
            'open' => 'bg-label-info',
            'resolved' => 'bg-label-success',
        ];

        $status = strtolower($fb->status ?? '');
        $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
    @endphp
    <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
</td>


             

                <td>
    @if (strtolower($fb->status) === 'resolved')
        -
    @else
        <div class="dropdown">
            <a href="javascript:;"
               class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-end m-0">
                <form action="{{ route('feedback.resolve.admin', $fb->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="dropdown-item permission-btn">
                        Resolved
                    </button>
                </form>
            </div>
        </div>
    @endif
</td>

              </tr>
              @empty
    <tr>
        <td colspan="5" class="text-center">No data available!</td>
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
            Showing {{ $feedback->firstItem() ?? 0 }} to {{ $feedback->lastItem() ?? 0 }} of {{
            $feedback->total() ??
            0 }} entries
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">

                <li class="dt-paging-button page-item {{ $feedback->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $feedback->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li class="dt-paging-button page-item {{ $feedback->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $feedback->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                @foreach ($feedback->getUrlRange(max(1, $feedback->currentPage() - 2),
                min($feedback->lastPage(),
                $feedback->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $feedback->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach


                <li
                  class="dt-paging-button page-item {{ $feedback->currentPage() == $feedback->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $feedback->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li
                  class="dt-paging-button page-item {{ $feedback->currentPage() == $feedback->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $feedback->url($feedback->lastPage()) }}" aria-label="Last">
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
  function downloadFeedbackCSV() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    
    // Build URL
    const url = new URL("{{ route('feedback.export.admin') }}", window.location.origin);
    
    if (search) url.searchParams.append('search', search);
    if (status) url.searchParams.append('status', status);

    // Redirect to trigger download
    window.location.href = url.href;
}
</script>
@endpush








@endsection