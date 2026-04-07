@extends('layouts/layoutMaster')

@section('title', 'My Tasks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-tasks fs-1 text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Tasks</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-clock fs-1 text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-send fs-1 text-info mb-2"></i>
                    <h3 class="mb-0">{{ $stats['submitted'] }}</h3>
                    <small class="text-muted">Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-check-circle fs-1 text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tasks Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Tasks</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                                <div class="fw-semibold">{{ $task->title }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($task->description ?? 'No description', 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-{{ isset($task->deadline) && \Carbon\Carbon::parse($task->deadline)->isPast() ? 'danger' : 'info' }}">
                                    {{ isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('d M, Y') : 'No deadline' }}
                                </span>
                                @if(isset($task->deadline) && \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'approved')
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
                                    $color = $statusColors[$task->status] ?? 'secondary';
                                    $icon = $statusIcons[$task->status] ?? 'circle';
                                @endphp
                                <span class="badge bg-{{ $color }}">
                                    <i class="ti ti-{{ $icon }} me-1"></i>{{ ucfirst($task->status) }}
                                </span>
                            </td>
                            <td>
                                @if($task->grade)
                                    <span class="badge bg-primary">{{ $task->grade }}%</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('intern.tasks.show', $task->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye"></i> View
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
        <div class="card-footer">
            {{ $tasks->links() }}
        </div>
    </div>
</div>
@endsection