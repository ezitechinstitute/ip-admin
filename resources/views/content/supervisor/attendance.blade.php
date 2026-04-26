@extends('layouts/layoutMaster')

@section('title', 'Attendance & Leave Tracking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Uniform Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Attendance & Leave Tracking</h4>
            <p class="text-muted mb-0">
                Management / <span class="fw-semibold text-primary">Attendance Logs</span>
            </p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <div class="badge bg-label-primary px-3 py-2">
                <i class="ti ti-users me-1"></i> {{ method_exists($attendance, 'total') ? $attendance->total() : $attendance->count() }} Total Records
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Attendance Table Section --}}
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                {{-- Big Data Search & Filter Header --}}
                <div class="card-header border-bottom">
                    <form method="GET" action="{{ url()->current() }}" class="row g-3">
                        {{-- Search Input --}}
                        <div class="col-md-7">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="ti ti-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by Name, ETI ID, or Tech..." 
                                       value="{{ request('search') }}">
                                @if(request('search'))
                                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-xs d-flex align-items-center px-2">
                                        <i class="ti ti-x ti-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- Date Filter --}}
                        <div class="col-md-5">
                            <div class="d-flex gap-2">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="ti ti-calendar-event text-primary"></i></span>
                                    <input type="date" name="date" class="form-control" 
                                           value="{{ request('date') === 'all' ? '' : request('date', now()->toDateString()) }}" 
                                           onchange="this.form.submit()">
                                </div>
                                @if(request('date') !== 'all')
                                    <a href="{{ request()->fullUrlWithQuery(['date' => 'all']) }}" class="btn btn-label-secondary text-nowrap">View All</a>
                                @else
                                    <a href="{{ url()->current() }}" class="btn btn-primary">Today</a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Intern Identity</th>
                                <th>Status</th>
                                <th>Shift Timeline</th>
                                <th class="text-center">Work Hrs</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($attendance as $row)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-dark">
                                                {{ strtoupper(substr($row->intern_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-heading small">{{ $row->intern_name ?? 'N/A' }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                {{ $row->eti_id }} • <span class="text-primary">{{ $row->int_technology ?? 'N/A' }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $badgeColor = match(strtolower($row->status)) {
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'late' => 'warning',
                                            'half-day' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-label-{{ $badgeColor }} px-2">
                                        {{ ucfirst($row->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($row->start_shift)
                                        <div class="d-flex flex-column small" style="line-height: 1.2;">
                                            <span class="text-success"><i class="ti ti-clock-play ti-xs me-1"></i>{{ \Carbon\Carbon::parse($row->start_shift)->format('h:i A') }}</span>
                                            <span class="text-danger"><i class="ti ti-clock-stop ti-xs me-1"></i>{{ $row->end_shift ? \Carbon\Carbon::parse($row->end_shift)->format('h:i A') : '--:--' }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">No data</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-heading">
                                    {{ $row->duration ?: 0 }} <small class="text-muted">hrs</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="avatar avatar-lg bg-label-secondary mx-auto mb-2">
                                        <span class="avatar-initial rounded-circle"><i class="ti ti-search-off"></i></span>
                                    </div>
                                    <h6 class="text-muted">No attendance records found.</h6>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                @if(method_exists($attendance, 'links'))
                <div class="card-footer border-top d-flex justify-content-between align-items-center py-3">
                    <small class="text-muted">
                        Showing {{ $attendance->firstItem() ?? 0 }} to {{ $attendance->lastItem() ?? 0 }} of {{ $attendance->total() }}
                    </small>
                    <div class="pagination-sm">
                        {{ $attendance->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Section --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column h-100 gap-4">
                
                {{-- Absences KPI --}}
                <div class="card border-0 bg-label-danger shadow-none">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div class="d-flex flex-column">
                            <h3 class="fw-bolder mb-0 text-danger">{{ $absentCount }}</h3>
                            <span class="fw-medium text-danger text-uppercase small">Absents Detected</span>
                            <small class="text-muted" style="font-size: 0.65rem;">
                                @if(request('date') === 'all') All Time @else Today @endif
                            </small>
                        </div>
                        <div class="avatar bg-white rounded p-2 text-danger">
                            <i class="ti ti-user-off ti-md"></i>
                        </div>
                    </div>
                </div>

                {{-- Leave Notifications Feed (Summary) --}}
                <div class="card flex-grow-1">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h6 class="mb-0 fw-bold">Recent Leave Requests</h6>
                        <span class="badge bg-warning rounded-pill">{{ $recentLeaves->count() }}</span>
                    </div>
                    <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @forelse($recentLeaves as $leave)
                            <li class="list-group-item border-0 px-4 py-3">
                                <div class="d-flex align-items-start">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-warning"><i class="ti ti-bell-ringing"></i></span>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-0 fw-bold small text-heading">{{ $leave->intern_name }}</h6>
                                            <small class="text-muted" style="font-size: 0.6rem;">
                                                {{ \Carbon\Carbon::parse($leave->created_at)->diffForHumans(null, true) }}
                                            </small>
                                        </div>
                                        <p class="mb-0 small text-muted mt-1">Status: Pending Review</p>
                                    </div>
                                </div>
                            </li>
                            @empty
                            <li class="py-5 text-center">
                                <p class="text-muted small mb-0">No pending requests.</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                    @if($recentLeaves->count() > 0)
                        <div class="card-footer border-top py-2 text-center">
                            <button type="button" 
                                    class="btn btn-link btn-sm fw-bold text-primary shadow-none" 
                                    data-bs-toggle="offcanvas" 
                                    data-bs-target="#offcanvasLeaveViewer">
                                <i class="ti ti-eye me-1"></i> View All Requests
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leave Viewer Sidebar (Read-Only) --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasLeaveViewer" style="width: 450px;">
    <div class="offcanvas-header border-bottom bg-light">
        <h5 class="offcanvas-title fw-bold">Leave Application Center</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <p class="text-muted small mb-4">View the justification and status for leave requests in your track.</p>

        <div class="vstack gap-3">
            @forelse($recentLeaves as $leave)
                <div class="card border shadow-none bg-label-secondary">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-white text-primary text-uppercase">
                                        {{ substr($leave->intern_name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $leave->intern_name }}</h6>
                                    <small class="text-muted">{{ $leave->eti_id }}</small>
                                </div>
                            </div>
                            <span class="badge bg-warning">Pending Review</span>
                        </div>
                        
                        <div class="bg-white p-3 rounded mt-2 border">
                            <p class="mb-1 small fw-bold">Reason for Leave:</p>
                            <p class="mb-0 small text-dark italic" style="font-style: italic;">"{{ $leave->reason ?? 'No reason specified.' }}"</p>
                        </div>
                        
                        <div class="mt-2 text-end">
                            <small class="text-muted" style="font-size: 0.7rem;">
                                <i class="ti ti-clock me-1"></i>Applied: {{ \Carbon\Carbon::parse($leave->created_at)->format('d M, Y | h:i A') }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ti ti-clipboard-check text-success display-5 mb-2"></i>
                    <p class="text-muted">No pending leave records found.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection