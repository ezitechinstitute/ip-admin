@extends('layouts/layoutMaster')

@section('title', 'Intern Attendance')

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
    <h4 class="mt-6 mb-1">Intern Attendance</h4>
    <p class="text-muted">View intern check-in and check-out records</p>
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
                            <input type="hidden" name="date" value="{{ request('date') }}">
                            <input type="hidden" name="intern_id" value="{{ request('intern_id') }}">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        </form>
                        <label for="dt-length-0"></label>
                    </div>
                </div>

                <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <form method="GET" action="{{ route('manager.attendance.interns') }}" id="filterForm" class="d-flex gap-2">
                        <input type="text" id="attendanceDatePicker" name="date" class="form-control" style="width: 150px;" placeholder="Select Date" value="{{ request('date') }}">
                        
                        <select name="intern_id" id="internFilter" class="form-select select2" style="min-width: 200px;">
                            <option value="">All Interns</option>
                            @foreach($internsList ?? [] as $intern)
                            <option value="{{ $intern->eti_id }}" {{ request('intern_id') == $intern->eti_id ? 'selected' : '' }}>
                                {{ $intern->name }} ({{ $intern->technology ?? 'N/A' }})
                            </option>
                            @endforeach
                        </select>

                        <select name="status" id="statusFilter" class="form-select" style="min-width: 150px;">
                            <option value="">All Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Present</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Absent</option>
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
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Intern Name</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Technology</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Date</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Start Shift</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">End Shift</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Duration (hrs)</th>
                                <th class="dt-orderable-asc dt-orderable-desc text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendanceRecords ?? [] as $index => $record)
                            <tr>
                                <td><span class="text-heading text-nowrap">{{ $attendanceRecords->firstItem() + $index }}</span></td>
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
                                <td>
                                    <span class="badge bg-label-info">{{ $record->technology ?? 'N/A' }}</span>
                                </td>
                                <td><span class="text-heading text-nowrap">{{ \Carbon\Carbon::parse($record->start_shift)->format('d M Y') }}</span></td>
                                <td>
                                    @if($record->start_shift)
                                    <span class="badge bg-label-success text-nowrap">
                                        <i class="icon-base ti tabler-login me-1"></i>
                                        {{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->end_shift)
                                    <span class="badge bg-label-info text-nowrap">
                                        <i class="icon-base ti tabler-logout me-1"></i>
                                        {{ \Carbon\Carbon::parse($record->end_shift)->format('h:i A') }}
                                    </span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->duration)
                                    <span class="badge bg-label-primary">{{ number_format($record->duration / 60, 1) }}h</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $statusClass = 'bg-label-secondary';
                                    $statusText = 'Absent';

                                    if($record->status == 1){
                                        $statusClass = 'bg-label-success';
                                        $statusText = 'Present';
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
                                        <small class="text-muted">No records match your filters</small>
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
            <div class="row m-3">
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">
                        Showing <strong>{{ $attendanceRecords->firstItem() }}</strong> to <strong>{{ $attendanceRecords->lastItem() }}</strong> of <strong>{{ $attendanceRecords->total() }}</strong> records
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                        {{ $attendanceRecords->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker
    if(document.getElementById('attendanceDatePicker')) {
        flatpickr("#attendanceDatePicker", {
            dateFormat: "Y-m-d",
            defaultDate: "{{ request('date') }}"
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
</script>
@endsection

@endsection
