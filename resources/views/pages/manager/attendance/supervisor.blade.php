@extends('layouts/layoutMaster')

@section('title', 'Supervisor Attendance')

@section('vendor-style')
<link rel="stylesheet" href="path-to/datatables.bootstrap5.css">
<link rel="stylesheet" href="path-to/responsive.bootstrap5.css">
<link rel="stylesheet" href="path-to/buttons.bootstrap5.css">
<link rel="stylesheet" href="path-to/select2.css">
<link rel="stylesheet" href="path-to/form-validation.css">
<link rel="stylesheet" href="path-to/animate.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
<link rel="stylesheet" href="path-to/flatpickr.css">
@endsection

@section('vendor-script')
<script src="path-to/moment.js"></script>
<script src="path-to/datatables-bootstrap5.js"></script>
<script src="path-to/select2.js"></script>
<script src="path-to/form-validation.js"></script>
<script src="path-to/cleave-zen.js"></script>
<script src="path-to/sweetalert2.js"></script>
<script src="path-to/flatpickr.js"></script>
@endsection

@section('content')
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Supervisor Attendance</h4>
    <p class="text-muted">View supervisor check-in and check-out records</p>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
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
                            <input type="hidden" name="date" value="{{ request('date', date('Y-m-d')) }}">
                            <input type="hidden" name="supervisor_id" value="{{ request('supervisor_id') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('manager.supervisor.attendance') }}" id="filterForm" class="d-flex gap-2">
                        <input type="text" id="attendanceDatePicker" name="date" class="form-control" style="width: 150px;" placeholder="Select Date" value="{{ request('date', date('Y-m-d')) }}">
                        
                        <select name="supervisor_id" id="supervisorFilter" class="form-select select2" style="min-width: 200px;">
                            <option value="">All Supervisors</option>
                            @foreach($supervisorsList ?? [] as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ request('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive overflow-auto" style="max-height: 700px;">
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
                        aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">#</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Supervisor</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Email</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Date</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Check In</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Check Out</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Total Hours</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords ?? [] as $index => $record)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $index + 1 }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $record->check_in ? 'success' : 'secondary' }}">
                                                {{ substr($record->supervisor->name ?? 'S', 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-heading text-nowrap fw-medium">{{ $record->supervisor->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-heading text-nowrap">
                                        <small><i class="icon-base ti tabler-mail me-1 text-danger icon-22px"></i>{{ $record->supervisor->email ?? 'N/A' }}</small>
                                    </span>
                                </td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</span></td>
                                <td>
                                    @if($record->check_in)
                                    <span class="badge bg-label-success text-nowrap">
                                        <i class="icon-base ti tabler-login me-1"></i>
                                        {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                    <span class="badge bg-label-info text-nowrap">
                                        <i class="icon-base ti tabler-logout me-1"></i>
                                        {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_in && $record->check_out)
                                    @php
                                    $checkIn = \Carbon\Carbon::parse($record->check_in);
                                    $checkOut = \Carbon\Carbon::parse($record->check_out);
                                    $hours = $checkOut->diffInHours($checkIn);
                                    $minutes = $checkOut->diffInMinutes($checkIn) % 60;
                                    @endphp
                                    <span class="badge bg-label-primary">{{ $hours }}h {{ $minutes }}m</span>
                                    @elseif($record->check_in)
                                    <span class="badge bg-label-warning">In Progress</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $statusClass = 'bg-label-secondary';
                                    $statusText = 'Absent';

                                    if($record->check_in && $record->check_out){
                                        $statusClass = 'bg-label-success';
                                        $statusText = 'Completed';
                                    }
                                    elseif($record->check_in){
                                        $statusClass = 'bg-label-warning';
                                        $statusText = 'Working';
                                    }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <p class="mb-0">No Attendance Records Found</p>
                                        <small class="text-muted">No records found for {{ request('date', date('Y-m-d')) }}</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot></tfoot>
                    </table>
                </div>
            </div>

            @if(isset($attendanceRecords) && $attendanceRecords->total() > 0)
            <div class="row mx-3 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-info" aria-live="polite">
                        Showing {{ $attendanceRecords->firstItem() ?? 0 }} to {{ $attendanceRecords->lastItem() ?? 0 }} of {{ $attendanceRecords->total() ?? 0 }} entries
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end mt-4 col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <div class="dt-paging">
                        <nav aria-label="pagination">
                            <ul class="pagination">
                                <li class="dt-paging-button page-item {{ $attendanceRecords->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $attendanceRecords->url(1) }}" aria-label="First">
                                        <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="dt-paging-button page-item {{ $attendanceRecords->onFirstPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $attendanceRecords->previousPageUrl() }}" aria-label="Previous">
                                        <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                @foreach ($attendanceRecords->getUrlRange(max(1, $attendanceRecords->currentPage() - 2), min($attendanceRecords->lastPage(), $attendanceRecords->currentPage() + 2)) as $page => $url)
                                <li class="dt-paging-button page-item {{ $page == $attendanceRecords->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $url }}">{{ $page }}</a>
                                </li>
                                @endforeach
                                <li class="dt-paging-button page-item {{ $attendanceRecords->currentPage() == $attendanceRecords->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $attendanceRecords->nextPageUrl() }}" aria-label="Next">
                                        <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                                <li class="dt-paging-button page-item {{ $attendanceRecords->currentPage() == $attendanceRecords->lastPage() ? 'disabled' : '' }}">
                                    <a class="page-link" style="border-radius: 5px;" href="{{ $attendanceRecords->url($attendanceRecords->lastPage()) }}" aria-label="Last">
                                        <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Initialize Flatpickr for date selection
    flatpickr('#attendanceDatePicker', {
        dateFormat: 'Y-m-d',
        maxDate: 'today',
        defaultDate: '{{ request('date', date('Y-m-d')) }}',
        onChange: function(selectedDates, dateStr) {
            document.getElementById('filterForm').submit();
        }
    });

    // Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('.card-datatable'),
            placeholder: 'Select Supervisor',
            allowClear: true,
            width: '100%'
        });
    });

    // Auto-submit when supervisor filter changes
    document.getElementById('supervisorFilter')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

@endsection