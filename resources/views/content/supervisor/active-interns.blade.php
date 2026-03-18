@extends('layouts/layoutMaster')

@section('title', 'Active Interns')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Active Interns</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Active Intern List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Avatar</th>
                            <th>Intern Identity</th>
                            <th>Technology & Type</th>
                            <th>Task Progress</th>
                            <th>Start Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $index => $intern)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="avatar avatar-sm">
                                        <img src="{{ asset('assets/img/avatars/' . (($index % 5) + 1) . '.png') }}" alt="Avatar" class="rounded-circle">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $intern->name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $intern->email ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-nowrap">{{ $intern->int_technology ?? 'N/A' }}</span>
                                        <span class="badge bg-label-info w-px-75">{{ $intern->internship_type ?? 'Remote' }}</span>
                                    </div>
                                </td>
                                <td style="min-width: 150px;">
                                    <div class="d-flex flex-column">
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $intern->progress }}%;" aria-valuenow="{{ $intern->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ $intern->completed_tasks }}/{{ $intern->total_tasks }} Tasks ({{ $intern->progress }}%)</small>
                                    </div>
                                </td>
                                <td>{{ $intern->start_date ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-sm btn-label-primary">
                                       <i class="ti tabler-eye me-1"></i> View Profile
                                    </a>
                                </td>
                            </tr>
@empty
                            <tr>
                                <td colspan="7" class="text-center">No active interns found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection