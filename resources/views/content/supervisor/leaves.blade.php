@extends('layouts/layoutMaster')

@section('title', 'My Leaves')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 text-heading">Personal Leave Management</h4>
        <button class="btn btn-primary btn-sm"><i class="ti tabler-plus me-1"></i> Apply Leave</button>
    </div> --}}

    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <h5 class="mb-0">Leave History</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Leave Type</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Applied Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $index => $leave)
                    <tr>
                        <td class="ps-4">{{ $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge rounded bg-label-secondary p-2 me-3"><i class="ti tabler-calendar-user ti-sm"></i></div>
                                <span class="fw-bold text-heading">{{ $leave->leave_type }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="small fw-bold text-primary">{{ $leave->from_date }} <i class="ti tabler-arrow-narrow-right text-muted mx-1"></i> {{ $leave->to_date }}</span>
                                <small class="text-muted">Supervisor ID: {{ $leave->supervisor_id }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Pending' ? 'warning' : 'secondary') }} rounded-pill">
                                {{ $leave->status }}
                            </span>
                        </td>
                        <td class="text-end pe-4 small text-muted">
                            {{ \Carbon\Carbon::parse($leave->created_at ?? now())->format('M d, Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <img src="{{ asset('assets/img/illustrations/page-misc-error.png') }}" width="80" class="mb-3 opacity-50"><br>
                            <span class="text-muted">No leave records found in your account.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection