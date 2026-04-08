@extends('layouts/layoutMaster')

@section('title', 'My Projects')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-4 mb-4">
    <!-- Total Projects -->
    <div class="col-md-3 col-6">
      <div class="card shadow-sm border-0 rounded-4 hover-scale">
        <div class="card-body text-center py-4">
          <div class="icon-wrapper bg-primary bg-opacity-15 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
            <i class="ti ti-briefcase fs-2 text-primary"></i>
          </div>
          <h2 class="fw-bold mb-1">{{ $stats['total'] ?? 0 }}</h2>
          <small class="text-muted text-uppercase">Total Projects</small>
        </div>
      </div>
    </div>

    <!-- Ongoing -->
    <div class="col-md-3 col-6">
      <div class="card shadow-sm border-0 rounded-4 hover-scale">
        <div class="card-body text-center py-4">
          <div class="icon-wrapper bg-warning bg-opacity-15 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
            <i class="ti ti-clock fs-2 text-warning"></i>
          </div>
          <h2 class="fw-bold mb-1">{{ $stats['ongoing'] ?? 0 }}</h2>
          <small class="text-muted text-uppercase">Ongoing</small>
        </div>
      </div>
    </div>

    <!-- Submitted -->
    <div class="col-md-3 col-6">
      <div class="card shadow-sm border-0 rounded-4 hover-scale">
        <div class="card-body text-center py-4">
          <div class="icon-wrapper bg-info bg-opacity-15 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
            <i class="ti ti-send fs-2 text-info"></i>
          </div>
          <h2 class="fw-bold mb-1">{{ $stats['submitted'] ?? 0 }}</h2>
          <small class="text-muted text-uppercase">Submitted</small>
        </div>
      </div>
    </div>

    <!-- Approved -->
    <div class="col-md-3 col-6">
      <div class="card shadow-sm border-0 rounded-4 hover-scale">
        <div class="card-body text-center py-4">
          <div class="icon-wrapper bg-success bg-opacity-15 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width:60px; height:60px;">
            <i class="ti ti-check-circle fs-2 text-success"></i>
          </div>
          <h2 class="fw-bold mb-1">{{ $stats['approved'] ?? 0 }}</h2>
          <small class="text-muted text-uppercase">Approved</small>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Smooth hover scale effect */
  .hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .hover-scale:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.08);
  }

  /* Icon wrapper for better visual pop */
  .icon-wrapper i {
    transition: transform 0.3s ease;
  }
  .hover-scale:hover .icon-wrapper i {
    transform: scale(1.2);
  }
</style>

  <div class="card shadow-sm rounded-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold">My Projects</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover modern-table">
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
                            <small class="text-muted">{{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 50) }}</small>
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
                            <span class="badge rounded-pill bg-{{ $statusColors[$project->pstatus] ?? 'secondary' }} px-3 py-2">
                                {{ ucfirst($project->pstatus) }}
                            </span>
                        </td>
                        <td>
                            <div class="progress rounded-pill" style="height: 8px; width: 100px;">
                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ $project->progress ?? 0 }}%"></div>
                            </div>
                            <small class="text-muted">{{ $project->progress ?? 0 }}%</small>
                        </td>
                        <td>
                            <a href="{{ route('intern.projects.show', $project->project_id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                <i class="ti ti-eye me-1"></i> View
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
    <div class="card-footer bg-white border-0">
        {{ $projects->links() }}
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

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.4s ease;
}

.btn-outline-primary {
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
}

.card-footer {
    display: flex;
    justify-content: flex-end;
}

.badge {
    font-size: 0.85rem;
    font-weight: 600;
}
</style>
</div>
@endsection