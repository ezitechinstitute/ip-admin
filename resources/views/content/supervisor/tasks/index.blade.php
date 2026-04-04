@extends('layouts/layoutMaster')

@section('title', 'Intern Tasks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Intern Tasks</h4>
        <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">Create New Task</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Intern</th>
                            <th>Task Title</th>
                            <th>Status</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>{{ $task->intern_name }} ({{ $task->eti_id }})</td>
                                <td>{{ $task->task_title }}</td>
                                <td><span class="badge bg-label-info">{{ $task->task_status }}</span></td>
                                <td>{{ $task->task_start }}</td>
                                <td>{{ $task->task_end }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('supervisor.tasks.review', $task->task_id) }}" class="btn btn-sm btn-primary">Review</a>
                                        <a href="{{ route('supervisor.tasks.edit', $task->task_id) }}" class="btn btn-sm btn-label-secondary">Edit</a>
                                        <form action="{{ route('supervisor.tasks.delete', $task->task_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-label-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No tasks found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
