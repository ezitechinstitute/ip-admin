@extends('layouts/layoutMaster')

@section('title', 'Edit Task')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Edit Task</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('supervisor.tasks.updateDetails', $task->task_id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Task Title</label>
                    <input type="text" name="task_title" class="form-control" value="{{ $task->task_title }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Task Description</label>
                    <textarea name="task_description" class="form-control" rows="4" required>{{ $task->task_description }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="task_start" class="form-control" value="{{ $task->task_start }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="task_end" class="form-control" value="{{ $task->task_end }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Task Points / Total Marks</label>
                        <input type="number" name="task_points" class="form-control" value="{{ $task->task_points }}" required>
                    </div>
                </div>

                <div class="mb-3 col-md-4">
                    <label class="form-label">Status</label>
                    <select name="task_status" class="form-select" required>
                        <option value="Assigned" {{ $task->task_status == 'Assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="Ongoing" {{ $task->task_status == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="In Progress" {{ $task->task_status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ $task->task_status == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Rejected" {{ $task->task_status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="Expired" {{ $task->task_status == 'Expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
