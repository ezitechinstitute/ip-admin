@extends('layouts/layoutMaster')

@section('title', 'Leave Request')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Apply for Leave</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('intern.leave.request') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Leave Type</label>
                            <select name="leave_type" class="form-select" required>
                                <option value="sick">Sick Leave</option>
                                <option value="casual">Casual Leave</option>
                                <option value="emergency">Emergency Leave</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-1"></i> Submit Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Leave History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                      <thead class="table-light">
    <tr>
        <th>From Date</th>
        <th>To Date</th>
        <th>Duration</th>
        <th>Reason</th>
        <th>Status</th>
    </tr>
</thead>
<tbody>
    @forelse($leaves as $leave)
    <tr>
        <td>{{ \Carbon\Carbon::parse($leave->from_date)->format('d M, Y') }}</td>
        <td>{{ \Carbon\Carbon::parse($leave->to_date)->format('d M, Y') }}</td>
        <td>{{ $leave->days ?? 0 }} days</td>
        <td>{{ \Illuminate\Support\Str::limit($leave->reason, 50) }}</td>
        <td>
            @php
                $statusText = $leave->leave_status == 1 ? 'Approved' : 'Pending';
                $statusColor = $leave->leave_status == 1 ? 'success' : 'warning';
            @endphp
            <span class="badge bg-{{ $statusColor }}">
                {{ $statusText }}
            </span>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="text-center py-5">
            <i class="ti ti-calendar-off ti-3x text-muted mb-3"></i>
            <p class="text-muted mb-0">No leave requests found</p>
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
@endsection