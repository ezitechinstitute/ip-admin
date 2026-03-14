@extends('layouts/layoutMaster')

@section('title', 'Supervisors')

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
@php
$manager = auth()->guard('manager')->user();
@endphp
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Supervisors</h4>
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
            <form id="perPageForm" method="GET">
              <select name="perpage" class="form-select" onchange="this.form.submit()">
                @foreach([15, 25, 50, 100] as $val)
                <option value="{{ $val }}" {{ request('perpage', 15)==$val ? 'selected' : '' }}>{{ $val }}</option>
                @endforeach
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
              style="width: 200px;" placeholder="Search by Name or ETI-ID" value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>

            <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
              <option value="">Select Status</option>
              <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Active</option>
              <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Frozen</option>
            </select>

            <input type="hidden" name="perpage" value="{{ request('perpage', 15) }}">
          </form>
        </div>
      </div>

      <div class="justify-content-between dt-layout-table">
        <div class="table-responsive overflow-auto" style="max-height: 700px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">
            <thead class="border-top sticky-top bg-card">
              <tr>
                <th class="text-nowrap">ETI‑ID</th>
                <th class="text-nowrap">Name</th>
                <th class="text-nowrap">Join Date</th>
                <th class="text-nowrap">Commission</th>
                <th class="text-nowrap">Status</th>
                <th class="text-nowrap">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($supervisors as $Sup)
              <tr>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->eti_id}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->name}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$Sup->join_date}}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ $Sup->comission }}</span></td>
                <td>
                  @php
                    $statusClasses = ['1' => 'bg-label-success', '0' => 'bg-label-danger'];
                    $badgeClass = $statusClasses[$Sup->status] ?? 'bg-label-secondary';
                  @endphp
                  <span class="badge {{ $badgeClass }} text-capitalize">{{ $Sup->status ? 'Active' : 'Frozen' }}</span>
                </td>
                <td>
                  <div class="dropdown">
                    <a href="javascript:;"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                      data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end m-0">
                      <a href="{{ route('manager.supervisor.show', $Sup->manager_id) }}" class="dropdown-item">Details</a>
                      <a href="{{ route('manager.supervisor.activityLog', $Sup->manager_id) }}" class="dropdown-item">Activity Log</a>
                      <form action="{{ route('manager.supervisor.toggleFreeze', $Sup->manager_id) }}" method="POST" class="m-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="dropdown-item">{{ $Sup->status ? 'Freeze' : 'Unfreeze' }}</button>
                      </form>
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6">
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
            Showing {{ $supervisors->firstItem() ?? 0 }} to {{ $supervisors->lastItem() ?? 0 }} of {{ $supervisors->total() ?? 0 }} entries
          </div>
        </div>

        <div class="d-md-flex align-items-center dt-layout-end mt-4 col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                <li class="dt-paging-button page-item {{ $supervisors->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $supervisors->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->previousPageUrl() }}"
                    aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                @foreach ($supervisors->getUrlRange(max(1, $supervisors->currentPage() - 2),
                min($supervisors->lastPage(), $supervisors->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $supervisors->currentPage() ? 'active' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach
                <li class="dt-paging-button page-item {{ $supervisors->currentPage() == $supervisors->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->nextPageUrl() }}"
                    aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $supervisors->currentPage() == $supervisors->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" style="border-radius: 5px;" href="{{ $supervisors->url($supervisors->lastPage()) }}"
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

  document.getElementById('searchInput')?.addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500);
  });

  document.getElementById('statusFilter')?.addEventListener('change', function () {
    document.getElementById('filterForm').submit();
  });
</script>
@endsection