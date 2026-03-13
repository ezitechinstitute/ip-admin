@extends('layouts/layoutMaster')

@section('title', 'Withdraw Requests')

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

@section('content')
<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Withdraw Requests</h4>
  <p class="mb-6">Manage and process withdrawal requests from managers</p>
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
  }, 5000);
</script>

<div class="card">
  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 my-0 justify-content-between">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-length mb-md-6 mb-0 d-flex items-center mt-5">
            <form id="perPageForm" method="GET" action="{{route('admin.withdraw')}}">
              <select name="perPage" id="dt-length-0" class="form-select ms-0"
                onchange="document.getElementById('perPageForm').submit()">
                <option value="15" {{ $perPage==15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage==25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
              </select>
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
            </form>
            <label for="dt-length-0"></label>
          </div>
        </div>

        <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <form method="GET" id="filterForm" class="d-flex gap-2">
            <input type="search" name="search" id="searchInput" class="form-control" 
                   placeholder="Search by bank or account holder" value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
              <option value="">All Status</option>
              <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Pending</option>
              <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Completed</option>
              <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Rejected</option>
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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadWithdrawCSV()">
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
        <div class="table-responsive overflow-auto" style="max-height: 600px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">
            <thead class="border-top sticky-top bg-card">
              <tr>
                <th class="text-nowrap">Bank Name</th>
                <th class="text-nowrap">Account Number</th>
                <th class="text-nowrap">Account Holder</th>
                <th class="text-nowrap">Description</th>
                <th class="text-nowrap">Period</th>
                <th class="text-nowrap">Date</th>
                <th class="text-nowrap">Amount</th>
                <th class="text-nowrap">Status</th>
                <th class="text-nowrap">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($withdraws as $withdraw)
              <tr>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->bank}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_no}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_name}}</span></td>
                <td>
                  <span class="text-truncate d-flex align-items-center text-heading text-nowrap" 
                        title="{{$withdraw->description}}">
                    {{-- {{ Str::limit($withdraw->description, 30) }} --}}
                  </span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->period}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ \Carbon\Carbon::parse($withdraw->date)->format('d M Y') }}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap fw-bold">৳ {{ number_format($withdraw->amount, 2) }}</span></td>
                <td>
                  @if ($withdraw->req_status == 1)
                    <span class="badge bg-label-success">Completed</span>
                  @elseif ($withdraw->req_status == 2)
                    <span class="badge bg-label-danger">Rejected</span>
                  @else
                    <span class="badge bg-label-warning">Pending</span>
                  @endif
                </td>
               <td>
                <div class="d-flex align-items-center">
                  @if($withdraw->req_status == 0)
                    <div class="dropdown">
                      <a href="javascript:;" class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-end m-0">
                        <form method="POST" action="{{ route('admin.withdraw.approve', $withdraw->req_id) }}" class="m-0">
                          @csrf
                          <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to approve this request?')">
                            <i class="icon-base ti tabler-check me-1"></i>Approve
                          </button>
                        </form>
                        <form method="POST" action="{{ route('admin.withdraw.reject', $withdraw->req_id) }}" class="m-0">
                          @csrf
                          <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to reject this request?')">
                            <i class="icon-base ti tabler-x me-1"></i>Reject
                          </button>
                        </form>
                      </div>
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </div>
              </td>
              </tr>
              @empty
              <tr>
                <td colspan="9" class="text-center py-4">
                  <div class="d-flex flex-column align-items-center">
                    <i class="icon-base ti tabler-wallet-off icon-48px text-muted mb-2"></i>
                    <p class="mb-0">No withdrawal requests found!</p>
                  </div>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="row mx-3 justify-content-between mt-4">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $withdraws->firstItem() ?? 0 }} to {{ $withdraws->lastItem() ?? 0 }} of {{ $withdraws->total() ?? 0 }} entries
          </div>
        </div>

        <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                <li class="dt-paging-button page-item {{ $withdraws->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $withdraws->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $withdraws->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $withdraws->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                @foreach ($withdraws->getUrlRange(max(1, $withdraws->currentPage() - 2), min($withdraws->lastPage(), $withdraws->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $withdraws->currentPage() ? 'active' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach
                <li class="dt-paging-button page-item {{ $withdraws->currentPage() == $withdraws->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $withdraws->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $withdraws->currentPage() == $withdraws->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $withdraws->url($withdraws->lastPage()) }}" aria-label="Last">
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

  document.getElementById('searchInput')?.addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500);
  });

  document.getElementById('statusFilter')?.addEventListener('change', function () {
    document.getElementById('filterForm').submit();
  });

  function downloadWithdrawCSV() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const params = new URLSearchParams({ search: search, status: status });
    window.location.href = "{{ route('admin.withdraw.export') }}?" + params.toString();
  }
</script>
@endpush

@endsection