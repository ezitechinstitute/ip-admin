@extends('layouts/layoutMaster')

@section('title', 'My Tasks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Modern Statistics Cards --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['icon'=>'ti-tasks','count'=>$stats['total'],'label'=>'Total Tasks','color'=>'primary'],
                ['icon'=>'ti-clock','count'=>$stats['pending'],'label'=>'Pending','color'=>'warning'],
                ['icon'=>'ti-send','count'=>$stats['submitted'],'label'=>'Submitted','color'=>'info'],
                ['icon'=>'ti-check-circle','count'=>$stats['approved'],'label'=>'Approved','color'=>'success'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="col-md-3 col-6">
            <div class="card shadow-sm rounded-4 hover-scale">
                <div class="card-body text-center py-4">
                    <i class="ti {{ $card['icon'] }} fs-1 text-{{ $card['color'] }} mb-2"></i>
                    <h3 class="mb-1 fw-bold">{{ $card['count'] ?? 0 }}</h3>
                    <small class="text-muted">{{ $card['label'] }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Modern Tasks Table --}}
    <div class="card shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 fw-bold">All Tasks</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover modern-table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task Title</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $task->task_title }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($task->task_description ?? 'No description', 50) }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ Carbon\Carbon::parse($task->task_end)->isPast() ? 'danger' : 'info' }}">
                                    {{ Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}
                                </span>
                                @if(Carbon\Carbon::parse($task->task_end)->isPast() && $task->task_status != 'approved')
                                    <br><small class="text-danger">Overdue</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'submitted' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'completed' => 'success',
                                    ];
                                    $statusIcons = [
                                        'pending' => 'clock',
                                        'submitted' => 'send',
                                        'approved' => 'check-circle',
                                        'rejected' => 'x-circle',
                                        'completed' => 'check',
                                    ];
                                    $color = $statusColors[$task->task_status] ?? 'secondary';
                                    $icon = $statusIcons[$task->task_status] ?? 'circle';
                                @endphp
                                <span class="badge rounded-pill bg-{{ $color }}">
                                    <i class="ti ti-{{ $icon }} me-1"></i>{{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                            <td>
                                @if($task->grade)
                                    <span class="badge rounded-pill bg-primary">{{ $task->grade }}%</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('intern.tasks.show', $task->task_id) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                                    <i class="ti ti-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="ti ti-tasks-off ti-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No tasks assigned yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 d-flex justify-content-end">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
/* Modern Cards */
.card.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card.hover-scale:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}

/* Modern Table */
.modern-table th {
    font-weight: 600;
    color: #495057;
    letter-spacing: 0.5px;
}
.modern-table tbody tr {
    transition: background-color 0.3s, transform 0.2s;
    border-radius: 8px;
}
.modern-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
}

/* Badges */
.badge {
    font-size: 0.85rem;
    font-weight: 600;
}

/* Buttons */
.btn-outline-primary {
    border-radius: 20px;
    padding: 0.35rem 0.75rem;
    transition: all 0.3s ease;
}
.btn-outline-primary:hover {
    background-color: #f0f0f0;
    transform: translateY(-1px);
}

/* Pagination */
.pagination {
    border-radius: 20px;
    overflow: hidden;
}
.pagination .page-item .page-link {
    border-radius: 50% !important;
    margin: 0 3px;
    padding: 0.4rem 0.7rem;
    border: 1px solid #dee2e6;
    color: #495057;
}
.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}
.pagination .page-item .page-link:hover {
    background-color: #e9ecef;
}
</style>
@endsection