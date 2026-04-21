@extends('layouts/layoutMaster')

@section('title', 'My Tasks')

@section('content')
<div class="container-xxl py-4">

    {{-- ===== SMART STATS CARDS ===== --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['icon'=>'bi-list-task','count'=>$stats['total'],'label'=>'Total Tasks','color'=>'primary'],
                ['icon'=>'bi-hourglass-split','count'=>$stats['pending'],'label'=>'Pending','color'=>'warning'],
                ['icon'=>'bi-upload','count'=>$stats['submitted'],'label'=>'Submitted','color'=>'info'],
                ['icon'=>'bi-check-circle','count'=>$stats['approved'],'label'=>'Approved','color'=>'success'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 text-center p-3 hover-card">
                <i class="bi {{ $card['icon'] }} fs-2 text-{{ $card['color'] }}"></i>
                <h4 class="fw-bold mt-2">{{ $card['count'] ?? 0 }}</h4>
                <small class="text-muted">{{ $card['label'] }}</small>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ===== TASK TABLE ===== --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Your Tasks</h5>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0 modern-table">
                <thead class="table-light">
                    <tr>
                        <th>Task</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($tasks as $task)
                    @php
                        $deadline = \Carbon\Carbon::parse($task->task_end);
                        $isOverdue = $deadline->isPast() && $task->task_status !== 'approved';
                        $isUrgent = $deadline->diffInDays(now()) <= 2 && !$isOverdue;

                        $statusColors = [
                            'pending' => 'warning',
                            'submitted' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];

                        $statusIcons = [
                            'pending' => 'hourglass-split',
                            'submitted' => 'upload',
                            'approved' => 'check-circle',
                            'rejected' => 'x-circle',
                        ];
                    @endphp

                    <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                        
                        {{-- TASK INFO --}}
                        <td>
                            <div class="fw-semibold">{{ $task->task_title }}</div>
                            <small class="text-muted">
                                {{ \Illuminate\Support\Str::limit($task->task_description ?? 'No description', 60) }}
                            </small>

                            {{-- Urgency Tag --}}
                            @if($isOverdue)
                                <div><span class="badge bg-danger mt-1">Overdue</span></div>
                            @elseif($isUrgent)
                                <div><span class="badge bg-warning text-dark mt-1">Due Soon</span></div>
                            @endif
                        </td>

                        {{-- DEADLINE --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $deadline->format('d M Y') }}
                            </div>
                            <small class="text-muted">
                                {{ $deadline->diffForHumans() }}
                            </small>
                        </td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge bg-{{ $statusColors[$task->task_status] ?? 'secondary' }}">
                                <i class="bi bi-{{ $statusIcons[$task->task_status] ?? 'circle' }} me-1"></i>
                                {{ ucfirst($task->task_status) }}
                            </span>
                        </td>

                        {{-- SCORE --}}
                        <td>
                            @if($task->grade)
                                <span class="badge bg-primary">{{ $task->grade }}%</span>
                            @else
                                <span class="text-muted">Not graded</span>
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="text-end">
                            <a href="{{ route('intern.tasks.show', $task->task_id) }}" 
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-eye"></i>
                            </a>

                            @if($task->task_status == 'pending' || $task->task_status == 'rejected')
                            <a href="{{ route('intern.tasks.submit', $task->task_id) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="bi bi-upload"></i>
                            </a>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            {{-- <i class="bi bi-inbox fs-1 text-muted"></i> --}}
                            <p class="mt-2 text-muted">No tasks available</p>
                            <small class="text-muted">You're all caught up. Wait for new assignments.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="card-footer  border-0 text-end">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- ===== STYLES ===== --}}
<style>
.hover-card {
    transition: 0.3s;
}
.hover-card:hover {
    transform: translateY(-5px);
}

.modern-table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.8rem;
    font-weight: 600;
}

.btn {
    border-radius: 20px;
    transition: 0.2s;
}
.btn:hover {
    transform: translateY(-1px);
}

.table-danger {
    background-color: #ffe5e5 !important;
}
</style>

@endsection