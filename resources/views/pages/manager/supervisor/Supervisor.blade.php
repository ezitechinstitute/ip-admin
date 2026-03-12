@extends('layouts/layoutMaster')

@section('title', 'Leave & Attendance Management')

@section('vendor-style')
<link rel="stylesheet" href="path-to/datatables.bootstrap5.css">
<link rel="stylesheet" href="path-to/responsive.bootstrap5.css">
<link rel="stylesheet" href="path-to/buttons.bootstrap5.css">
<link rel="stylesheet" href="path-to/select2.css">
<link rel="stylesheet" href="path-to/form-validation.css">
<link rel="stylesheet" href="path-to/animate.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
<link rel="stylesheet" href="path-to/flatpickr.css">
<link rel="stylesheet" href="path-to/fullcalendar.css">
@endsection

@section('vendor-script')
<script src="path-to/moment.js"></script>
<script src="path-to/datatables-bootstrap5.js"></script>
<script src="path-to/select2.js"></script>
<script src="path-to/form-validation.js"></script>
<script src="path-to/cleave-zen.js"></script>
<script src="path-to/sweetalert2.js"></script>
<script src="path-to/flatpickr.js"></script>
<script src="path-to/fullcalendar.js"></script>
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-6">
        <h4 class="mt-6 mb-1">Leave & Attendance Management</h4>
        <p class="text-muted">Manage supervisor leave requests and view attendance records</p>
    </div>
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

<!-- Attendance Summary Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text mb-2">Present Today</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $presentToday ?? 0 }}</h4>
                            <small class="text-success">of {{ $totalSupervisors ?? 0 }}</small>
                        </div>
                        <small>Supervisors checked in</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="icon-base ti tabler-clock-check icon-28px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text mb-2">Pending Leaves</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $pendingLeaves ?? 0 }}</h4>
                            <small class="text-warning">awaiting</small>
                        </div>
                        <small>{{ $approvedLeaves ?? 0 }} approved this month</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="icon-base ti tabler-calendar-time icon-28px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text mb-2">On Leave Today</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $onLeaveToday ?? 0 }}</h4>
                            <small class="text-info">supervisors</small>
                        </div>
                        <small>{{ $upcomingLeaves ?? 0 }} upcoming</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-info rounded p-2">
                            <i class="icon-base ti tabler-user-off icon-28px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text mb-2">Avg. Work Hours</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $avgWorkHours ?? '7.5' }}</h4>
                            <small class="text-secondary">hours</small>
                        </div>
                        <small>this week</small>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-secondary rounded p-2">
                            <i class="icon-base ti tabler-chart-bar icon-28px"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="nav-tabs-top mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="leave-requests-tab" data-bs-toggle="tab" data-bs-target="#leave-requests" type="button" role="tab" aria-controls="leave-requests" aria-selected="true">
                <i class="icon-base ti tabler-calendar me-1"></i> Leave Requests
                @if(($pendingLeaves ?? 0) > 0)
                <span class="badge bg-danger ms-1">{{ $pendingLeaves }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button" role="tab" aria-controls="attendance" aria-selected="false">
                <i class="icon-base ti tabler-clock me-1"></i> Attendance Overview
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar" type="button" role="tab" aria-controls="calendar" aria-selected="false">
                <i class="icon-base ti tabler-calendar-week me-1"></i> Calendar View
            </button>
        </li>
    </ul>

    <div class="tab-content border-0 p-0 pt-4">
        <!-- Leave Requests Tab -->
        <div class="tab-pane fade show active" id="leave-requests" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Supervisor Leave Requests</h5>
                </div>
                <div class="card-datatable">
                    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
                        <div class="row m-3 my-0 justify-content-between">
                            <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                                <div class="dt-length mb-md-6 mb-0 d-flex items-center mt-5">
                                    <form id="perPageForm" method="GET">
                                        <input type="hidden" name="tab" value="leave-requests">
                                        <select name="per_page" class="form-select" onchange="this.form.submit()">
                                            @foreach([15, 25, 50, 100] as $val)
                                            <option value="{{ $val }}" {{ request('per_page',15)==$val ? 'selected' : '' }}>{{ $val }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="search" value="{{ request('search') }}">
                                        <input type="hidden" name="status" value="{{ request('status') }}">
                                        <input type="hidden" name="supervisor_id" value="{{ request('supervisor_id') }}">
                                    </form>
                                    <label for="dt-length-0"></label>
                                </div>
                            </div>

                            <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                                <form method="GET" action="{{ route('manager.leave.requests') }}" id="filterForm" class="d-flex gap-2">
                                    <input type="hidden" name="tab" value="leave-requests">
                                    <input type="search" name="search" id="searchInput" class="form-control"
                                        placeholder="Search by Name, Email or ID" value="{{ request('search') }}">
                                    <style>
                                        input[type="search"]::-webkit-search-cancel-button,
                                        input[type="search"]::-webkit-search-decoration {
                                            -webkit-appearance: none;
                                            appearance: none;
                                        }
                                    </style>

                                    <select name="supervisor_id" id="supervisorFilter" class="form-select select2" style="min-width: 180px;">
                                        <option value="">All Supervisors</option>
                                        @foreach($supervisors ?? [] as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ request('supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <select name="status" id="statusFilter" class="form-select text-capitalize" style="min-width: 130px;">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status')=='approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status')=='rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="justify-content-between dt-layout-table">
                            <div class="table-responsive overflow-auto" style="max-height: 700px;">
                                <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
                                    aria-describedby="DataTables_Table_0_info" style="width: 100%;">
                                    <thead class="border-top sticky-top bg-card">
                                        <tr>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Leave ID</th>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Supervisor</th>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Contact</th>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">From Date</th>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">To Date</th>
                                            <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Days</th>
                                            <th class="dt-orderable-none">Reason</th>
                                            <th class="dt-orderable-none">Status</th>
                                            <th class="dt-orderable-none">Requested On</th>
                                            <th class="dt-orderable-none">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($leaves ?? [] as $leave)
                                        <tr>
                                            <td><span class="text-heading text-nowrap fw-medium">#{{ $leave->leave_id }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-primary">
                                                            {{ substr($leave->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span class="text-heading text-nowrap">{{ $leave->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-heading text-nowrap">
                                                    <small><i class="icon-base ti tabler-mail me-1 text-danger icon-18px"></i>{{ $leave->email }}</small>
                                                </span>
                                            </td>
                                            <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</span></td>
                                            <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</span></td>
                                            <td><span class="badge bg-label-info">{{ $leave->days }} days</span></td>
                                            <td>
                                                <span class="text-heading text-wrap" style="max-width: 200px; display: inline-block;">
                                                    {{ Str::limit($leave->reason, 50) }}
                                                </span>
                                            </td>
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
                                                <span class="text-heading text-nowrap">
                                                    {{ $leave->created_at ? \Carbon\Carbon::parse($leave->created_at)->format('d M Y') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="javascript:;"
                                                        class="btn btn-text-secondary rounded-pill waves-effect btn-icon dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-base ti tabler-dots-vertical icon-22px"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end m-0">
                                                        @if($leave->leave_status === null || $leave->leave_status === 0)
                                                        <a href="{{ route('manager.leave.approve', $leave->leave_id) }}"
                                                            class="dropdown-item text-success"
                                                            onclick="event.preventDefault(); confirmAction('approve', '{{ $leave->leave_id }}')">
                                                            <i class="icon-base ti tabler-check me-1"></i> Approve
                                                        </a>
                                                        <a href="{{ route('manager.leave.reject', $leave->leave_id) }}"
                                                            class="dropdown-item text-danger"
                                                            onclick="event.preventDefault(); confirmAction('reject', '{{ $leave->leave_id }}')">
                                                            <i class="icon-base ti tabler-x me-1"></i> Reject
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        @endif
                                                        <a href="javascript:;" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#viewLeaveModal{{ $leave->leave_id }}">
                                                            <i class="icon-base ti tabler-eye me-1"></i> View Details
                                                        </a>
                                                    </div>
                                                </div>

                                                <!-- View Leave Modal -->
                                                <div class="modal fade" id="viewLeaveModal{{ $leave->leave_id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Leave Request Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Supervisor</label>
                                                                    <p>{{ $leave->name }}</p>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-6">
                                                                        <label class="form-label fw-bold">From Date</label>
                                                                        <p>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M Y') }}</p>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label class="form-label fw-bold">To Date</label>
                                                                        <p>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M Y') }}</p>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Total Days</label>
                                                                    <p>{{ $leave->days }} days</p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Reason</label>
                                                                    <p>{{ $leave->reason }}</p>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold">Status</label>
                                                                    <p><span class="badge {{ $statusClass }}">{{ $statusText }}</span></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="icon-base ti tabler-calendar-off icon-48px text-muted mb-2"></i>
                                                    <p class="mb-0">No Leave Requests Found</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if(isset($leaves) && $leaves->total() > 0)
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
                                                <a class="page-link" href="{{ $leaves->url(1) }}" aria-label="First">
                                                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                                                </a>
                                            </li>
                                            <li class="dt-paging-button page-item {{ $leaves->onFirstPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $leaves->previousPageUrl() }}" aria-label="Previous">
                                                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                                                </a>
                                            </li>
                                            @foreach ($leaves->getUrlRange(max(1, $leaves->currentPage() - 2), min($leaves->lastPage(), $leaves->currentPage() + 2)) as $page => $url)
                                            <li class="dt-paging-button page-item {{ $page == $leaves->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                            @endforeach
                                            <li class="dt-paging-button page-item {{ $leaves->currentPage() == $leaves->lastPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $leaves->nextPageUrl() }}" aria-label="Next">
                                                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                                                </a>
                                            </li>
                                            <li class="dt-paging-button page-item {{ $leaves->currentPage() == $leaves->lastPage() ? 'disabled' : '' }}">
                                                <a class="page-link" href="{{ $leaves->url($leaves->lastPage()) }}" aria-label="Last">
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
        </div>

        <!-- Attendance Overview Tab -->
        <div class="tab-pane fade" id="attendance" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Supervisor Attendance Records</h5>
                        <div class="d-flex gap-2">
                            <input type="text" id="attendanceDatePicker" class="form-control" style="width: 200px;" placeholder="Select Date" value="{{ request('date', date('Y-m-d')) }}">
                            <button class="btn btn-primary" onclick="exportAttendance()">
                                <i class="icon-base ti tabler-download me-1"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-datatable">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Supervisor</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Hours</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords ?? [] as $record)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $record->check_in ? 'success' : 'secondary' }}">
                                                {{ substr($record->supervisor_name ?? 'S', 0, 1) }}
                                            </span>
                                        </div>
                                        <span>{{ $record->supervisor_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td>{{ $record->supervisor_email ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                                <td>
                                    @if($record->check_in)
                                    <span class="badge bg-label-success">{{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                    <span class="badge bg-label-info">{{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}</span>
                                    @else
                                    <span class="text-muted">-</span>
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
                                    <span>{{ $hours }}h {{ $minutes }}m</span>
                                    @elseif($record->check_in)
                                    <span class="text-warning">In Progress</span>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_in && $record->check_out)
                                    <span class="badge bg-label-success">Completed</span>
                                    @elseif($record->check_in)
                                    <span class="badge bg-label-warning">Working</span>
                                    @else
                                    <span class="badge bg-label-secondary">Absent</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="icon-base ti tabler-clock-off icon-48px text-muted mb-2"></i>
                                        <p class="mb-0">No attendance records found for this date</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Calendar View Tab -->
        <div class="tab-pane fade" id="calendar" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Leave & Attendance Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-submit filters
    let timer;
    document.getElementById('searchInput')?.addEventListener('keyup', function() {
        clearTimeout(timer);
        timer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });

    document.getElementById('statusFilter')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    document.getElementById('supervisorFilter')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // Initialize Select2
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#leave-requests')
        });
    });

    // Date picker for attendance
    flatpickr('#attendanceDatePicker', {
        dateFormat: 'Y-m-d',
        defaultDate: '{{ request('date', date('Y-m-d')) }}',
        onChange: function(selectedDates, dateStr) {
            window.location.href = '{{ route("manager.leave.requests") }}?tab=attendance&date=' + dateStr;
        }
    });

    // Initialize Calendar
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    // Leave events
                    @foreach($leaves ?? [] as $leave)
                    {
                        title: '{{ $leave->name }} - Leave',
                        start: '{{ $leave->from_date }}',
                        end: '{{ \Carbon\Carbon::parse($leave->to_date)->addDay()->format('Y-m-d') }}',
                        color: '{{ $leave->leave_status === 1 ? "#28a745" : ($leave->leave_status === 0 ? "#dc3545" : "#ffc107") }}',
                        textColor: '#ffffff',
                        description: '{{ addslashes($leave->reason) }}'
                    },
                    @endforeach
                ],
                eventClick: function(info) {
                    Swal.fire({
                        title: info.event.title,
                        text: info.event.extendedProps.description || 'No description',
                        icon: 'info',
                        confirmButtonText: 'Close'
                    });
                }
            });
            calendar.render();
        }
    });

    // Confirm action
    function confirmAction(action, leaveId) {
        const actionText = action.charAt(0).toUpperCase() + action.slice(1);
        Swal.fire({
            title: `Are you sure?`,
            text: `Do you want to ${action} this leave request?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${action} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = action === 'approve' 
                    ? '{{ route("manager.leave.approve", "") }}/' + leaveId
                    : '{{ route("manager.leave.reject", "") }}/' + leaveId;
            }
        });
    }

    // Export attendance
    function exportAttendance() {
        const date = document.getElementById('attendanceDatePicker').value;
        window.location.href = '{{ route("manager.attendance.export") }}?date=' + date;
    }
</script>
@endsection