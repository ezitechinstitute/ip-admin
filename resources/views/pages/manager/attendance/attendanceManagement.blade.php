@extends('layouts/layoutMaster')

@section('title', 'Attendance Management')

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
    <h4 class="mt-6 mb-1">Attendance Management</h4>
    <p class="text-muted">Manage intern and supervisor attendance records, and supervisor leave requests</p>
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

<!-- Attendance Tabs Navigation -->
<div class="card mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a href="{{ route('manager.attendance.manage', array_merge(request()->query(), ['tab' => 'intern'])) }}" 
               class="nav-link {{ $tab === 'intern' ? 'active' : '' }}">
                <i class="icon-base ti tabler-user-check me-2"></i> Intern Attendance
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('manager.attendance.manage', array_merge(request()->query(), ['tab' => 'supervisor'])) }}" 
               class="nav-link {{ $tab === 'supervisor' ? 'active' : '' }}">
                <i class="icon-base ti tabler-users me-2"></i> Supervisor Attendance
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('manager.attendance.manage', array_merge(request()->query(), ['tab' => 'leave'])) }}" 
               class="nav-link {{ $tab === 'leave' ? 'active' : '' }}">
                <i class="icon-base ti tabler-calendar-event me-2"></i> Supervisor Leave Requests
            </a>
        </li>
    </ul>
</div>

<!-- ==================== INTERN ATTENDANCE TAB ==================== -->
@if($tab === 'intern')
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
                            <input type="hidden" name="tab" value="intern">
                            <input type="hidden" name="intern_date" value="{{ request('intern_date') }}">
                            <input type="hidden" name="intern_id" value="{{ request('intern_id') }}">
                            <input type="hidden" name="intern_status" value="{{ request('intern_status') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('manager.attendance.manage') }}" id="filterForm" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="intern">
                        <input type="text" id="internDatePicker" name="intern_date" class="form-control" style="width: 150px;" placeholder="Select Date" value="{{ request('intern_date') }}">
                        
                        <select name="intern_id" id="internFilter" class="form-select select2" style="min-width: 200px;">
                            <option value="">All Interns</option>
                            @foreach($internsList ?? [] as $intern)
                            <option value="{{ $intern->eti_id }}" {{ request('intern_id') == $intern->eti_id ? 'selected' : '' }}>
                                {{ $intern->name }} ({{ $intern->technology ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>

                        <select name="intern_status" id="statusFilter" class="form-select" style="min-width: 150px;">
                            <option value="">All Status</option>
                            <option value="1" {{ request('intern_status') == '1' ? 'selected' : '' }}>Present</option>
                            <option value="0" {{ request('intern_status') == '0' ? 'selected' : '' }}>Absent</option>
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive overflow-auto" style="max-height: 700px;">
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="text-nowrap">#</th>
                                <th class="text-nowrap">Intern Name</th>
                                <th class="text-nowrap">Technology</th>
                                <th class="text-nowrap">Date</th>
                                <th class="text-nowrap">Start Shift</th>
                                <th class="text-nowrap">End Shift</th>
                                <th class="text-nowrap">Duration (hrs)</th>
                                <th class="text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internAttendance ?? [] as $index => $record)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $internAttendance->firstItem() + $index }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $record->status ? 'success' : 'secondary' }}">
                                                {{ substr($record->name ?? 'I', 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-heading text-nowrap fw-medium">{{ $record->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-label-info">{{ $record->technology ?? 'N/A' }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->start_shift)->format('d M Y') }}</span></td>
                                <td>
                                    @if($record->start_shift)
                                    <span class="badge bg-label-success text-nowrap">
                                        {{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->end_shift)
                                    <span class="badge bg-label-info text-nowrap">
                                        {{ \Carbon\Carbon::parse($record->end_shift)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->duration)
                                    <span class="badge bg-label-primary">{{ number_format($record->duration / 60, 1) }}</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $record->status == 1 ? 'bg-label-success' : 'bg-label-secondary' }}">
                                        {{ $record->status == 1 ? 'Present' : 'Absent' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="mb-0">No Attendance Records Found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(isset($internAttendance) && $internAttendance->total() > 0)
            <div class="row m-3">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_info">
                        Showing <strong>{{ $internAttendance->firstItem() }}</strong> to <strong>{{ $internAttendance->lastItem() }}</strong> of <strong>{{ $internAttendance->total() }}</strong>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_paginate">
                        {{ $internAttendance->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- ==================== SUPERVISOR ATTENDANCE TAB ==================== -->
@if($tab === 'supervisor')
<div class="card">
    <div class="card-datatable">
        <div id="DataTables_Table_1_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
            <div class="row m-3 my-0 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-length mb-md-6 mb-0 d-flex items-center mt-5">
                        <form id="perPageForm" method="GET">
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([15, 25, 50, 100] as $val)
                                <option value="{{ $val }}" {{ request('per_page',15)==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="tab" value="supervisor">
                            <input type="hidden" name="supervisor_date" value="{{ request('supervisor_date') }}">
                            <input type="hidden" name="supervisor_id" value="{{ request('supervisor_id') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('manager.attendance.manage') }}" id="filterForm" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="supervisor">
                        <input type="text" id="supervisorDatePicker" name="supervisor_date" class="form-control" style="width: 150px;" placeholder="Select Date" value="{{ request('supervisor_date') }}">
                        
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
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_1" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="text-nowrap">#</th>
                                <th class="text-nowrap">Supervisor</th>
                                <th class="text-nowrap">Date</th>
                                <th class="text-nowrap">Check In</th>
                                <th class="text-nowrap">Check Out</th>
                                <th class="text-nowrap">Total Hours</th>
                                <th class="text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supervisorAttendance ?? [] as $index => $record)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $supervisorAttendance->firstItem() + $index }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-{{ $record->check_in ? 'success' : 'secondary' }}">S</span>
                                        </div>
                                        <span class="text-heading text-nowrap fw-medium">{{ $record->supervisor_name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</span></td>
                                <td>
                                    @if($record->check_in)
                                    <span class="badge bg-label-success text-nowrap">
                                        {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->check_out)
                                    <span class="badge bg-label-info text-nowrap">
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
                                <td colspan="7" class="text-center py-4">
                                    <p class="mb-0">No Attendance Records Found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(isset($supervisorAttendance) && $supervisorAttendance->total() > 0)
            <div class="row m-3">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_info">
                        Showing <strong>{{ $supervisorAttendance->firstItem() }}</strong> to <strong>{{ $supervisorAttendance->lastItem() }}</strong> of <strong>{{ $supervisorAttendance->total() }}</strong>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_paginate">
                        {{ $supervisorAttendance->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- ==================== SUPERVISOR LEAVE REQUESTS TAB ==================== -->
@if($tab === 'leave')
<div class="card">
    <div class="card-datatable">
        <div id="DataTables_Table_2_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
            <div class="row m-3 my-0 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-length mb-md-6 mb-0 d-flex items-center mt-5">
                        <form id="perPageForm" method="GET">
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                @foreach([15, 25, 50, 100] as $val)
                                <option value="{{ $val }}" {{ request('per_page',15)==$val ? 'selected' : '' }}>{{ $val }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="tab" value="leave">
                            <input type="hidden" name="leave_status" value="{{ request('leave_status') }}">
                            <input type="hidden" name="leave_supervisor_id" value="{{ request('leave_supervisor_id') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('manager.attendance.manage') }}" id="filterForm" class="d-flex gap-2">
                        <input type="hidden" name="tab" value="leave">
                        
                        <select name="leave_supervisor_id" id="leaveSupervisorFilter" class="form-select select2" style="min-width: 200px;">
                            <option value="">All Supervisors</option>
                            @foreach($supervisorsList ?? [] as $supervisor)
                            <option value="{{ $supervisor->id }}" {{ request('leave_supervisor_id') == $supervisor->id ? 'selected' : '' }}>
                                {{ $supervisor->name }}
                            </option>
                            @endforeach
                        </select>

                        <select name="leave_status" id="leaveStatusFilter" class="form-select" style="min-width: 150px;">
                            <option value="">All Status</option>
                            <option value="0" {{ request('leave_status') == '0' ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ request('leave_status') == '1' ? 'selected' : '' }}>Approved</option>
                            <option value="2" {{ request('leave_status') == '2' ? 'selected' : '' }}>Rejected</option>
                        </select>

                        <button type="submit" class="btn btn-primary">
                            <i class="icon-base ti tabler-filter me-1"></i> Filter
                        </button>
                    </form>
                </div>
            </div>

            <div class="justify-content-between dt-layout-table">
                <div class="table-responsive overflow-auto" style="max-height: 700px;">
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_2" style="width: 100%;">
                        <thead class="border-top sticky-top bg-card">
                            <tr>
                                <th class="text-nowrap">#</th>
                                <th class="text-nowrap">Supervisor</th>
                                <th class="text-nowrap">From Date</th>
                                <th class="text-nowrap">To Date</th>
                                <th class="text-nowrap">Days</th>
                                <th class="text-nowrap">Reason</th>
                                <th class="text-nowrap">Status</th>
                                <th class="text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($supervisorLeaves ?? [] as $index => $record)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $supervisorLeaves->firstItem() + $index }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-info">{{ substr($record->name ?? 'S', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="text-heading text-nowrap fw-medium">{{ $record->name ?? 'Unknown' }}</span><br>
                                            <small class="text-muted">{{ $record->email ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->from_date)->format('d M Y') }}</span></td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->to_date)->format('d M Y') }}</span></td>
                                <td><span class="badge bg-label-primary">{{ $record->days }} days</span></td>
                                <td><small>{{ Str::limit($record->reason, 40) }}</small></td>
                                <td>
                                    @php
                                    $statusClass = 'bg-label-secondary';
                                    $statusText = 'Pending';
                                    if($record->leave_status == 1){
                                        $statusClass = 'bg-label-success';
                                        $statusText = 'Approved';
                                    }
                                    elseif($record->leave_status == 2){
                                        $statusClass = 'bg-label-danger';
                                        $statusText = 'Rejected';
                                    }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>
                                    @if($record->leave_status == 0)
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal" onclick="setLeaveId({{ $record->leave_id }}, 'approve')">
                                        <i class="icon-base ti tabler-check"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" onclick="setLeaveId({{ $record->leave_id }}, 'reject')">
                                        <i class="icon-base ti tabler-x"></i>
                                    </button>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="mb-0">No Leave Requests Found</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(isset($supervisorLeaves) && $supervisorLeaves->total() > 0)
            <div class="row m-3">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_info">
                        Showing <strong>{{ $supervisorLeaves->firstItem() }}</strong> to <strong>{{ $supervisorLeaves->lastItem() }}</strong> of <strong>{{ $supervisorLeaves->total() }}</strong>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_paginate">
                        {{ $supervisorLeaves->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date pickers
    if(document.getElementById('internDatePicker')) {
        flatpickr("#internDatePicker", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('intern_date') }}"
        });
    }

    if(document.getElementById('supervisorDatePicker')) {
        flatpickr("#supervisorDatePicker", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('supervisor_date') }}"
        });
    }

    // Initialize select2
    if($('.select2').length) {
        $('.select2').select2({
            placeholder: 'Select an option',
            allowClear: true
        });
    }
});

function setLeaveId(leaveId, action) {
    // Set leave ID for approve/reject modals
    document.getElementById('leaveIdInput').value = leaveId;
    document.getElementById('actionInput').value = action;
}
</script>
@endsection

@endsection
