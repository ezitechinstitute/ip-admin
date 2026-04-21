@extends('layouts/layoutMaster')

@section('title', 'My Projects')

@section('content')
<div class="container-xxl py-4">

    <!-- ===== Stats Cards ===== -->
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['title' => 'Total Projects', 'count' => $stats['total'] ?? 0, 'icon' => 'bi-briefcase', 'color' => 'primary'],
                ['title' => 'Ongoing', 'count' => $stats['ongoing'] ?? 0, 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
                ['title' => 'Submitted', 'count' => $stats['submitted'] ?? 0, 'icon' => 'bi-upload', 'color' => 'info'],
                ['title' => 'Approved', 'count' => $stats['approved'] ?? 0, 'icon' => 'bi-check-circle', 'color' => 'success'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 hover-card">
                <div class="mb-2">
                    <i class="bi {{ $card['icon'] }} fs-2 text-{{ $card['color'] }}"></i>
                </div>
                <h4 class="fw-bold">{{ $card['count'] }}</h4>
                <small class="text-muted">{{ $card['title'] }}</small>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ===== Projects Table ===== -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header  border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Assigned Projects</h5>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0 table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Project</th>
                        <th>Technology</th>
                        <th>Supervisor</th>
                        <th>Timeline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($projects as $project)
                    <tr>
                        <!-- Project Title + Short Description -->
                        <td>
                            <div class="fw-semibold">{{ $project->title }}</div>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($project->description ?? 'No description', 60) }}
                            </small>
                        </td>

                        <!-- Technology -->
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $project->tech_stack ?? 'N/A' }}
                            </span>
                        </td>

                        <!-- Supervisor -->
                        <td>
                            <i class="bi bi-person-circle me-1"></i>
                            {{ $project->supervisor->name ?? 'Not Assigned' }}
                        </td>

                        <!-- Timeline -->
                        <td>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}
                                <br> to <br>
                                {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                            </small>
                        </td>

                        <!-- Status -->
                        @php
                            $statusColors = [
                                'ongoing' => 'warning',
                                'submitted' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            ];
                        @endphp

                        <td>
                            <span class="badge bg-{{ $statusColors[$project->pstatus] ?? 'secondary' }}">
                                {{ ucfirst($project->pstatus) }}
                            </span>
                        </td>

                        <!-- Action -->
                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#projectModal{{ $project->project_id }}">
                                <i class="bi bi-eye"></i> View
                            </button>
                        </td>
                    </tr>

                    <!-- ===== Modal (Instructions + Full Details) ===== -->
                    <div class="modal fade" id="projectModal{{ $project->project_id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content rounded-4">

                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">{{ $project->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <p><strong>Technology:</strong> {{ $project->tech_stack }}</p>
                                    <p><strong>Supervisor:</strong> {{ $project->supervisor->name ?? 'N/A' }}</p>
                                    <p><strong>Duration:</strong>
                                        {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}
                                        -
                                        {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                    </p>

                                    <hr>

                                    <h6 class="fw-bold">Project Instructions</h6>
                                    <p class="text-muted">
                                        {{ $project->instructions ?? 'No instructions provided' }}
                                    </p>
                                </div>

                                <div class="modal-footer border-0">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                </div>

                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            {{-- <i class="bi bi-folder-x fs-1 text-muted"></i> --}}
                            <p class="text-muted mt-2">No projects assigned</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer  border-0 text-end">
            {{ $projects->links() }}
        </div>
    </div>

</div>

<!-- ===== Styles ===== -->
<style>
.hover-card {
    transition: 0.3s;
}
.hover-card:hover {
    transform: translateY(-5px);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.8rem;
}
</style>

@endsection