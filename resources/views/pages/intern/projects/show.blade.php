@extends('layouts/layoutMaster')

@section('title', 'Project Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Project Overview Card -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h3 class="mb-0 fw-bold">{{ $project->title }}</h3>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <!-- Project Information -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 shadow-sm">
                        <h5 class="fw-semibold mb-3">Project Information</h5>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Technology</div>
                            <div class="col-7">{{ $project->tech_stack ?? 'Not specified' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Supervisor</div>
                            <div class="col-7">{{ $supervisorName }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">Start Date</div>
                            <div class="col-7">{{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-muted">End Date</div>
                            <div class="col-7">{{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-5 text-muted">Status</div>
                            <div class="col-7">
                                @php
                                    $statusColors = [
                                        'ongoing' => 'warning',
                                        'submitted' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge rounded-pill bg-{{ $statusColors[$project->pstatus] ?? 'secondary' }} px-3 py-2">
                                    {{ ucfirst($project->pstatus) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Project Description -->
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 shadow-sm h-100">
                        <h5 class="fw-semibold mb-3">Project Description</h5>
                        <p class="text-muted">{{ $project->description ?? 'No description provided' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Tasks -->
    @if($tasks->count() > 0)
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">Project Tasks</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle modern-table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task Title</th>
                            <th>Deadline</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td class="fw-semibold">{{ $task->task_title }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 bg-{{ $task->task_status == 'approved' ? 'success' : 'warning' }}">
                                    {{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Back Button -->
    <div class="mb-5">
        <a href="{{ route('intern.projects') }}" class="btn btn-primary d-flex align-items-center">
            Back to Projects
        </a>
    </div>
</div>

<style>
/* Modern table styles */
.modern-table th {
    font-weight: 600;
    color: #495057;
    letter-spacing: 0.5px;
}

.modern-table tbody tr {
    transition: background-color 0.3s, transform 0.2s;
}

.modern-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.badge {
    font-size: 0.85rem;
    font-weight: 600;
}

.card-body p {
    line-height: 1.6;
}

.btn-outline-secondary {
    border-radius: 20px;
    padding: 0.4rem 1rem;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background-color: #f0f0f0;
    transform: translateY(-2px);
}
</style>
@endsection