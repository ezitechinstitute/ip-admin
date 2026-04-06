@extends('layouts/layoutMaster')

@section('title', 'My Projects')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-briefcase fs-1 text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total'] ?? 0 }}</h3>
                    <small class="text-muted">Total Projects</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-clock fs-1 text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stats['ongoing'] ?? 0 }}</h3>
                    <small class="text-muted">Ongoing</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-send fs-1 text-info mb-2"></i>
                    <h3 class="mb-0">{{ $stats['submitted'] ?? 0 }}</h3>
                    <small class="text-muted">Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-check-circle fs-1 text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['approved'] ?? 0 }}</h3>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">My Projects</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project Title</th>
                            <th>Technology</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $project->title }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 40) }}</small>
                            </td>
                            <td>{{ $project->tech_stack ?? 'Not specified' }}</td>
                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'ongoing' => 'warning',
                                        'submitted' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$project->pstatus] ?? 'secondary' }}">
                                    {{ ucfirst($project->pstatus) }}
                                </span>
                            </td>
                            <td>
                                <div class="progress" style="height: 6px; width: 100px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $project->progress ?? 0 }}%"></div>
                                </div>
                                <small>{{ $project->progress ?? 0 }}%</small>
                            </td>
                            <td>
                                <a href="{{ route('intern.projects.show', $project->project_id) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="ti ti-briefcase-off ti-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No projects assigned yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $projects->links() }}
        </div>
    </div>
</div>
@endsection