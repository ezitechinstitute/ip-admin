@extends('layouts/layoutMaster')

@section('title', 'My Attendance')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-calendar fs-1 text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total_days'] }}</h3>
                    <small class="text-muted">Total Days</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-check-circle fs-1 text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['present_days'] }}</h3>
                    <small class="text-muted">Present</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-x-circle fs-1 text-danger mb-2"></i>
                    <h3 class="mb-0">{{ $stats['absent_days'] }}</h3>
                    <small class="text-muted">Absent</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-chart-line fs-1 text-info mb-2"></i>
                    <h3 class="mb-0">{{ $stats['attendance_percentage'] }}%</h3>
                    <small class="text-muted">Attendance Rate</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Attendance History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance as $record)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('d M, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($record->start_shift)->format('h:i A') }}</td>
                            <td>{{ $record->end_shift ? \Carbon\Carbon::parse($record->end_shift)->format('h:i A') : '—' }}</td>
                            <td>{{ $record->duration ?? 0 }} hours</td>
                            <td>
                                <span class="badge bg-{{ $record->status == 1 ? 'success' : 'danger' }}">
                                    {{ $record->status == 1 ? 'Present' : 'Absent' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="ti ti-calendar-off ti-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No attendance records found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $attendance->links() }}
        </div>
    </div>
</div>
@endsection