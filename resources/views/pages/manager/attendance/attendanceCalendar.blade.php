@extends('layouts/layoutMaster')

@section('title', 'Attendance Calendar')

@section('vendor-style')
<link rel="stylesheet" href="path-to/datatables.bootstrap5.css">
<link rel="stylesheet" href="path-to/fullcalendar.css">
<link rel="stylesheet" href="path-to/flatpickr.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
<link rel="stylesheet" href="path-to/select2.css">
@endsection

@section('vendor-script')
<script src="path-to/moment.js"></script>
<script src="path-to/fullcalendar.js"></script>
<script src="path-to/flatpickr.js"></script>
<script src="path-to/sweetalert2.js"></script>
<script src="path-to/select2.js"></script>
@endsection

@section('content')
<div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Attendance Calendar</h4>
    <p class="text-muted">View supervisor and intern attendance records based on your assigned technologies</p>
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

<!-- Technology Info Badge -->
{{-- <div class="mb-4">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="icon-base ti tabler-device-analytics icon-28px text-primary me-3"></i>
                <div>
                    <h6 class="mb-1">Your Assigned Technologies</h6>
                    <div>
                        @foreach($managerTechs ?? [] as $tech)
                        <span class="badge bg-label-primary me-1">{{ $tech }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<!-- Date Range Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('manager.attendance-calendar') }}" id="dateFilterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label class="form-label">Date Range</label>
                            <select name="date_filter" id="dateFilter" class="form-select">
                                <option value="today" {{ ($dateFilter ?? 'today') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="yesterday" {{ ($dateFilter ?? '') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                                <option value="this_week" {{ ($dateFilter ?? '') == 'this_week' ? 'selected' : '' }}>This Week</option>
                                <option value="last_week" {{ ($dateFilter ?? '') == 'last_week' ? 'selected' : '' }}>Last Week</option>
                                <option value="this_month" {{ ($dateFilter ?? '') == 'this_month' ? 'selected' : '' }}>This Month</option>
                                <option value="last_month" {{ ($dateFilter ?? '') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                <option value="custom" {{ ($dateFilter ?? '') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0 custom-date {{ ($dateFilter ?? '') == 'custom' ? '' : 'd-none' }}">
                            <label class="form-label">Start Date</label>
                            <input type="text" name="start_date" id="startDate" class="form-control flatpickr-input" value="{{ $startDate ?? '' }}" placeholder="Select start date">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0 custom-date {{ ($dateFilter ?? '') == 'custom' ? '' : 'd-none' }}">
                            <label class="form-label">End Date</label>
                            <input type="text" name="end_date" id="endDate" class="form-control flatpickr-input" value="{{ $endDate ?? '' }}" placeholder="Select end date">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="icon-base ti tabler-filter me-1"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Info -->
{{-- <div class="mb-4">
    <div class="card bg-label-light">
        <div class="card-body py-3">
            <div class="d-flex align-items-center">
                <i class="icon-base ti tabler-calendar-stats icon-22px text-primary me-2"></i>
                <span>Showing attendance for: <strong>{{ $dateLabel ?? 'Today' }}</strong></span>
                @if(isset($startDate) && isset($endDate) && $startDate != $endDate)
                <span class="ms-2 text-muted">({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }})</span>
                @elseif(isset($startDate))
                <span class="ms-2 text-muted">({{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }})</span>
                @endif
            </div>
        </div>
    </div>
</div> --}}

<!-- Tabs Navigation -->
<div class="nav-tabs-top mb-4">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="supervisor-tab" data-bs-toggle="tab" data-bs-target="#supervisor" type="button" role="tab" aria-selected="true">
                <i class="icon-base ti tabler-users me-1"></i> Supervisor Attendance
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="intern-tab" data-bs-toggle="tab" data-bs-target="#intern" type="button" role="tab" aria-selected="false">
                <i class="icon-base ti tabler-user me-1"></i> Intern Attendance
            </button>
        </li>
    </ul>

    <div class="tab-content border-0 p-0 pt-4">
        <!-- Supervisor Attendance Tab -->
        <div class="tab-pane fade show active" id="supervisor" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title mb-0">Supervisor Attendance Calendar</h5>
                        <div class="d-flex gap-2">
                            <select id="supervisorFilter" class="form-select select2" style="min-width: 200px;">
                                <option value="">All Supervisors</option>
                                @foreach($supervisors ?? [] as $supervisor)
                                <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary" onclick="refreshSupervisorCalendar()">
                                <i class="icon-base ti tabler-refresh me-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="supervisorCalendar"></div>
                </div>
            </div>
            
            <!-- Supervisor Attendance List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Supervisor Attendance ({{ $supervisorAttendance->count() }} records)</h5>
                </div>
                <div class="card-datatable">
                    <div class="table-responsive">
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
                                @forelse($supervisorAttendance ?? [] as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2">
                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                    {{ substr($record->supervisor_name ?? 'S', 0, 1) }}
                                                </span>
                                            </div>
                                            <span class="fw-medium">{{ $record->supervisor_name ?? 'Unknown' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $record->supervisor_email ?? 'N/A' }}</td>
                                    <td><span class="fw-medium">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</span></td>
                                    <td>
                                        @if($record->check_in)
                                        <span class="badge bg-label-success">{{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}</span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->check_out)
                                        <span class="badge bg-label-info">{{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}</span>
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
                                            <p class="mb-0">No supervisor attendance records found for selected period</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Intern Attendance Tab -->
        <div class="tab-pane fade" id="intern" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title mb-0">Intern Attendance Calendar</h5>
                        <div class="d-flex gap-2">
                            <select id="internFilter" class="form-select select2" style="min-width: 200px;">
                                <option value="">All Interns</option>
                                @foreach($interns ?? [] as $intern)
                                <option value="{{ $intern->id }}">{{ $intern->name }} ({{ $intern->technology }})</option>
                                @endforeach
                            </select>
                            <select id="technologyFilter" class="form-select select2" style="min-width: 180px;">
                                <option value="">All Technologies</option>
                                @foreach($managerTechs ?? [] as $tech)
                                <option value="{{ $tech }}">{{ $tech }}</option>
                                @endforeach
                            </select>
                            <select id="internStatusFilter" class="form-select" style="min-width: 150px;">
                                <option value="">All Status</option>
                                <option value="1">Present</option>
                                <option value="0">Absent</option>
                            </select>
                            <button class="btn btn-primary" onclick="refreshInternCalendar()">
                                <i class="icon-base ti tabler-refresh me-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="internCalendar"></div>
                </div>
            </div>
            
            <!-- Intern Attendance List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Intern Attendance ({{ $internAttendance->count() }} records)</h5>
                </div>
                <div class="card-datatable">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Technology</th>
                                    <th>Date</th>
                                    <th>Start Shift</th>
                                    <th>End Shift</th>
                                    <th>Duration (min)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($internAttendance ?? [] as $record)
                                <tr>
                                    <td><span class="fw-medium">{{ $record->intern_id ?? $record->eti_id }}</span></td>
                                    <td>{{ $record->name ?? 'N/A' }}</td>
                                    <td>{{ $record->email }}</td>
                                    <td><span class="badge bg-label-info">{{ $record->technology ?? 'N/A' }}</span></td>
                                    <td><span class="fw-medium">{{ \Carbon\Carbon::parse($record->start_shift)->format('d M Y') }}</span></td>
                                    <td>
                                        @if($record->start_shift)
                                        <span class="badge bg-label-success">{{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}</span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->end_shift)
                                        <span class="badge bg-label-info">{{ \Carbon\Carbon::parse($record->end_shift)->format('h:i A') }}</span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->duration ?? 0 }} min</td>
                                    <td>
                                        @if($record->status == 1)
                                        <span class="badge bg-label-success">Present</span>
                                        @else
                                        <span class="badge bg-label-danger">Absent</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="icon-base ti tabler-clock-off icon-48px text-muted mb-2"></i>
                                            <p class="mb-0">No intern attendance records found for selected period</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Supervisor Calendar
    document.addEventListener('DOMContentLoaded', function() {
        initSupervisorCalendar();
        initInternCalendar();
        
        // Initialize Select2
        $('.select2').select2({
            dropdownParent: $('.tab-content'),
            placeholder: 'Select...',
            allowClear: true,
            width: '100%'
        });

        // Initialize date pickers
        flatpickr('#startDate', {
            dateFormat: 'Y-m-d',
            maxDate: 'today'
        });

        flatpickr('#endDate', {
            dateFormat: 'Y-m-d',
            maxDate: 'today'
        });
    });

    // Show/hide custom date fields
    document.getElementById('dateFilter')?.addEventListener('change', function() {
        const customDateFields = document.querySelectorAll('.custom-date');
        if (this.value === 'custom') {
            customDateFields.forEach(field => field.classList.remove('d-none'));
        } else {
            customDateFields.forEach(field => field.classList.add('d-none'));
        }
    });

    function initSupervisorCalendar() {
        const calendarEl = document.getElementById('supervisorCalendar');
        if (!calendarEl) return;

        const supervisorCalendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 500,
            events: @json($supervisorCalendarEvents ?? []),
            eventClick: function(info) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-start">
                            <p><strong>Date:</strong> ${info.event.start.toLocaleDateString()}</p>
                            <p><strong>Check In:</strong> ${info.event.extendedProps.check_in || '—'}</p>
                            <p><strong>Check Out:</strong> ${info.event.extendedProps.check_out || '—'}</p>
                            <p><strong>Hours:</strong> ${info.event.extendedProps.hours || '0'}h ${info.event.extendedProps.minutes || '0'}m</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            }
        });
        
        supervisorCalendar.render();
        window.supervisorCalendar = supervisorCalendar;
    }

    function initInternCalendar() {
        const calendarEl = document.getElementById('internCalendar');
        if (!calendarEl) return;

        const internCalendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            height: 500,
            events: @json($internCalendarEvents ?? []),
            eventClick: function(info) {
                Swal.fire({
                    title: info.event.title,
                    html: `
                        <div class="text-start">
                            <p><strong>Name:</strong> ${info.event.title}</p>
                            <p><strong>Date:</strong> ${info.event.start.toLocaleDateString()}</p>
                            <p><strong>Technology:</strong> ${info.event.extendedProps.technology || 'N/A'}</p>
                            <p><strong>Start Shift:</strong> ${info.event.extendedProps.start_shift || '—'}</p>
                            <p><strong>End Shift:</strong> ${info.event.extendedProps.end_shift || '—'}</p>
                            <p><strong>Duration:</strong> ${info.event.extendedProps.duration || 0} minutes</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Close'
                });
            }
        });
        
        internCalendar.render();
        window.internCalendar = internCalendar;
    }

    // Filter functions
    document.getElementById('supervisorFilter')?.addEventListener('change', function() {
        refreshSupervisorCalendar();
    });

    document.getElementById('internFilter')?.addEventListener('change', function() {
        refreshInternCalendar();
    });

    document.getElementById('technologyFilter')?.addEventListener('change', function() {
        refreshInternCalendar();
    });

    document.getElementById('internStatusFilter')?.addEventListener('change', function() {
        refreshInternCalendar();
    });

    function refreshSupervisorCalendar() {
        const supervisorId = document.getElementById('supervisorFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || 'today';
        const startDate = document.getElementById('startDate')?.value || '';
        const endDate = document.getElementById('endDate')?.value || '';
        
        let url = `{{ route("manager.attendance-calendar") }}?date_filter=${dateFilter}&supervisor_id=${supervisorId}`;
        if (startDate) url += `&start_date=${startDate}`;
        if (endDate) url += `&end_date=${endDate}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (window.supervisorCalendar) {
                    window.supervisorCalendar.removeAllEvents();
                    window.supervisorCalendar.addEventSource(data.supervisorCalendarEvents || []);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function refreshInternCalendar() {
        const internId = document.getElementById('internFilter')?.value || '';
        const technology = document.getElementById('technologyFilter')?.value || '';
        const status = document.getElementById('internStatusFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || 'today';
        const startDate = document.getElementById('startDate')?.value || '';
        const endDate = document.getElementById('endDate')?.value || '';
        
        let url = `{{ route("manager.attendance-calendar") }}?date_filter=${dateFilter}`;
        if (internId) url += `&intern_id=${internId}`;
        if (technology) url += `&technology=${technology}`;
        if (status) url += `&status=${status}`;
        if (startDate) url += `&start_date=${startDate}`;
        if (endDate) url += `&end_date=${endDate}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (window.internCalendar) {
                    window.internCalendar.removeAllEvents();
                    window.internCalendar.addEventSource(data.internCalendarEvents || []);
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<style>
    /* Calendar customization */
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 4px;
    }
    
    .fc-event-main {
        padding: 2px 4px;
    }
    
    /* Avatar styles */
    .avatar-sm {
        width: 32px;
        height: 32px;
    }
    
    .avatar-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 600;
        font-size: 14px;
    }
    
    /* Badge styles */
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .bg-label-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
        color: #28a745 !important;
    }
    
    .bg-label-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
        color: #ffc107 !important;
    }
    
    .bg-label-info {
        background-color: rgba(23, 162, 184, 0.1) !important;
        color: #17a2b8 !important;
    }
    
    .bg-label-primary {
        background-color: rgba(13, 110, 253, 0.1) !important;
        color: #0d6efd !important;
    }
    
    .bg-label-secondary {
        background-color: rgba(108, 117, 125, 0.1) !important;
        color: #6c757d !important;
    }
    
    .bg-label-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545 !important;
    }
    
    /* Select2 customization */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    
    /* Alert animations */
    .alert.hide {
        opacity: 0;
        transition: opacity 0.5s;
    }
    
    /* Table responsive */
    .table-responsive {
        overflow-x: auto;
    }
</style>
@endsection