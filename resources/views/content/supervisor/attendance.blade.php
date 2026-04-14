@extends('layouts/layoutMaster')

@section('title', 'Attendance & Leave Tracking')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="mb-4">Attendance & Leave Tracking</h4>

    <div class="row">
        {{-- ================================================= --}}
        {{-- LEFT COLUMN: The Daily Attendance Table             --}}
        {{-- ================================================= --}}
        <div class="col-lg-8 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daily Attendance List</h5>
                    <span class="badge bg-label-primary">Filtered by your assigned tech</span>
                </div>

                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Intern</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Shift Hours</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendance as $row)
                                <tr>
                                    {{-- Intern Name, ETI, and Technology --}}
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold">{{ $row->intern_name ?? 'N/A' }}</span>
                                            <small class="text-muted">{{ $row->eti_id }} • {{ $row->int_technology ?? 'N/A' }}</small>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td>
                                        {{ \Carbon\Carbon::parse($row->start_shift)->format('M d, Y') }}
                                    </td>

                                    {{-- Status (Fixed to handle strings properly) --}}
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
                                        <span class="badge bg-label-{{ $badgeColor }}">
                                            {{ ucfirst($row->status) }}
                                        </span>
                                    </td>

                                    {{-- Shift Hours --}}
                                    <td>
                                        @if($row->start_shift)
                                            <small><strong>In:</strong> {{ \Carbon\Carbon::parse($row->start_shift)->format('h:i A') }}</small><br>
                                            <small><strong>Out:</strong> {{ $row->end_shift ? \Carbon\Carbon::parse($row->end_shift)->format('h:i A') : '--:--' }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Duration --}}
                                    <td>
                                        <span class="fw-semibold">{{ $row->duration ? $row->duration . ' hrs' : '0 hrs' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="ti tabler-calendar-off display-6 mb-2"></i><br>
                                        No attendance records found for your interns.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- RIGHT COLUMN: Tracking & Notifications              --}}
        {{-- ================================================= --}}
        <div class="col-lg-4 col-md-12">
            
            {{-- 1. Absence Tracking KPI --}}
            <div class="card bg-danger text-white mb-4 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title text-white mb-1">Absences Today</h5>
                        <p class="card-text text-white-50 mb-0">Interns marked absent</p>
                    </div>
                    <div class="display-4 text-white fw-bold">
                        {{ $absentCount }}
                    </div>
                </div>
            </div>

            {{-- 2. Leave Notifications --}}
            <div class="card shadow-sm">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Leave Notifications</h5>
                    <span class="badge bg-label-warning">{{ $recentLeaves->count() }} Pending</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentLeaves as $leave)
                            <li class="list-group-item p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-bold">{{ $leave->intern_name }}</h6>
                                        <p class="mb-1 text-muted small">
                                            Requested a leave. Awaiting manager approval.
                                        </p>
                                        <small class="text-primary fw-semibold">
                                            Submitted: {{ \Carbon\Carbon::parse($leave->created_at)->diffForHumans() }}
                                        </small>
                                    </div>
                                    <i class="ti tabler-bell-ringing text-warning mt-1"></i>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item p-4 text-center text-muted">
                                <i class="ti tabler-check display-6 mb-2 text-success opacity-50"></i><br>
                                All clear! No pending leave requests from your interns.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection