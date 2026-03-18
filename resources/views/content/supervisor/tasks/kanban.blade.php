@extends('layouts/layoutMaster')

@section('title', 'Task Kanban')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Task Kanban Board</h4>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">Create Task</a>
    </div>

    <div class="kanban-wrapper">
        <div class="d-flex flex-nowrap g-3" style="padding-bottom: 20px;">
            @php
                $columns = [
                    'Assigned' => 'bg-primary',
                    'In Progress' => 'bg-info',
                    'Pending' => 'bg-warning',
                    'Completed' => 'bg-success',
                    'Rejected' => 'bg-danger',
                    'Expired' => 'bg-secondary'
                ];
            @endphp

            @foreach($columns as $status => $color)
                <div class="kanban-column" style="width: 280px; min-width: 280px; margin-right: 1.5rem;">
                    <div class="card h-100 shadow-none border">
                        <div class="card-header d-flex justify-content-between align-items-center py-2 {{ $color }}">
                            <h6 class="mb-0 {{ $status == 'Pending' ? 'text-dark' : 'text-white' }} fw-bold">{{ $status }}</h6>
                            @php $count = $tasks->where('task_status', $status)->count(); @endphp
                            <span class="badge {{ $status == 'Pending' ? 'bg-dark text-white' : 'bg-white text-dark' }} rounded-pill">{{ $count }}</span>
                        </div>
                        <div class="card-body p-2 bg-light bg-opacity-10" style="min-height: 500px;">
                            @foreach($tasks->where('task_status', $status) as $task)
                                <div class="card mb-2 shadow-sm border-0 kanban-task-card">
                                    <div class="card-body p-3">
                                        <h6 class="card-title mb-2 fw-bold" style="white-space: normal; line-height: 1.4;">{{ $task->task_title }}</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-secondary small">{{ $task->intern_name }}</span>
                                            <small class="text-danger fw-semibold">{{ \Carbon\Carbon::parse($task->task_end)->format('d M') }}</small>
                                        </div>
                                        <div class="d-flex justify-content-end gap-1 border-top pt-2">
                                            <a href="{{ route('supervisor.tasks.review', $task->task_id) }}" class="btn btn-xs btn-primary py-1 px-2">Review</a>
                                            <a href="{{ route('supervisor.tasks.edit', $task->task_id) }}" class="btn btn-xs btn-outline-secondary py-1 px-2">Edit</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
.kanban-wrapper {
    overflow-x: auto;
    padding-bottom: 20px;
}
.kanban-wrapper::-webkit-scrollbar {
    height: 8px;
}
.kanban-wrapper::-webkit-scrollbar-thumb {
    background: #dcdfe6;
    border-radius: 4px;
}
.kanban-task-card {
    transition: all 0.2s ease;
    border: 1px solid transparent !important;
}
.kanban-task-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    border-color: rgba(0,0,0,0.05) !important;
}
.text-xs {
    font-size: 0.7rem;
}
</style>
@endsection
