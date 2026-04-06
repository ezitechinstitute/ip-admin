@extends('layouts/layoutMaster')

@section('title', 'Leave Requests')

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
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Intern Leave Requests</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

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
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([15, 25, 50, 100] as $val)
                                <option value="{{ $val }}" {{ request('per_page',15)==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="search" value="{{ request('search') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    {{-- <form method="GET" action="{{ route('manager.leave.requests') }}" id="filterForm" class="d-flex gap-2">
                        <input type="search" name="search" id="searchInput" class="form-control"
                            placeholder="Search by Name, Email or Supervisor ID" value="{{ request('search') }}">
                        <style>
                            input[type="search"]::-webkit-search-cancel-button,
                            input[type="search"]::-webkit-search-decoration {
                                -webkit-appearance: none;
                                appearance: none;
                            }
                        </style>

                        <select name="status" id="statusFilter" class="form-select text-capitalize" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </form> --}}
                </div>
            </div>

            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive overflow-auto" style="max-height: 700px;">
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
                        aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Leave ID</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Supervisor ID</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Name</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Email</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">From Date</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">To Date</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Days</th>
                                <th class="dt-orderable-none">Reason</th>
                                <th class="dt-orderable-none">Status</th>
                                <th class="dt-orderable-none">Created At</th>
                                <th class="dt-orderable-none">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $leave)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $leave->leave_id }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ $leave->supervisor_id }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ $leave->name }}</span></td>
                                <td>
                                    <span class="text-heading text-nowrap">
                                        <small><i class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i>{{ $leave->email }}</small>
                                    </span>
                                </td>
                                <td><span class="text-heading text-nowrap">{{ $leave->from_date }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ $leave->to_date }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ $leave->days }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ $leave->reason }}</span></td>
                                <td>
                                    @php
                                    $statusClass = 'bg-label-warning';
                                    $statusText = 'Pending';

                                    if($leave->leave_status === 1){
                                        $statusClass = 'bg-label-success';
                                        $statusText = 'Approved';
                                    }
                                    elseif($leave->leave_status === 0 || $leave->leave_status === null){
                                        $statusClass = 'bg-label-danger';
                                        $statusText = 'Rejected';
                                    }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    <span class="text-heading text-nowrap">{{ $leave->created_at ? \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d') : 'N/A' }}</span> </td>
                                <td>
                                    <div class="dropdown">
                                        <a href="javascript:;"
                                            class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end m-0">
                                            <form action="{{ route('manager.leave.supervisor.approve', $leave->leave_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success" onclick="return confirm('Approve this leave request?')">
                                                    <i class="icon-base ti tabler-check me-1"></i> Approve
                                                </button>
                                            </form>
                                            <form action="{{ route('manager.leave.supervisor.reject', $leave->leave_id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Reject this leave request?')">
                                                    <i class="icon-base ti tabler-x me-1"></i> Reject
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            {{-- <a href="{{ route('manager.leave.view',$leave->leave_id) }}"
                                                class="dropdown-item">
                                                <i class="icon-base ti tabler-eye me-1"></i> View Details
                                            </a> --}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11">
                                    <p class="text-center mb-0">No Leave Requests Found</p>
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
                        Showing {{ $leaves->firstItem() ?? 0 }} to {{ $leaves->lastItem() ?? 0 }} of {{ $leaves->total() ?? 0 }} entries
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end mt-4 col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <div class="dt-paging">
                        <nav aria-label="pagination">
                            <ul class="pagination">
                                <li class="dt-paging-button page-item {{ $leaves->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $leaves->url(1) }}" aria-label="First">
                                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="dt-paging-button page-item {{ $leaves->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $leaves->previousPageUrl() }}" aria-label="Previous">
                                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                @foreach ($leaves->getUrlRange(max(1, $leaves->currentPage() - 2), min($leaves->lastPage(), $leaves->currentPage() + 2)) as $page => $url)
                                <li class="dt-paging-button page-item {{ $page == $leaves->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                                </li>
                                @endforeach
                                <li class="dt-paging-button page-item {{ $leaves->currentPage() == $leaves->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $leaves->nextPageUrl() }}" aria-label="Next">
                                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="dt-paging-button page-item {{ $leaves->currentPage() == $leaves->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $leaves->url($leaves->lastPage()) }}" aria-label="Last">
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
    document.getElementById('searchInput').addEventListener('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });

    document.getElementById('statusFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>

@endsection