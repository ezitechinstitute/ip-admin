@extends('layouts/layoutMaster')

@section('title', 'Edit Project Task')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="mb-4">Edit Project Task</h4>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('supervisor.projects.tasks.update', [$project->project_id, $task->task_id]) }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Task Title</label>
            <input type="text" name="task_title" class="form-control" value="{{ $task->task_title }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Milestone Title (Optional)</label>
            <input type="text" name="milestone_title" class="form-control" value="{{ $task->milestone_title }}" placeholder="e.g. Phase 1: Setup">
          </div>

          <div class="col-md-3">
            <label class="form-label">Start Date</label>
            <input type="date" name="t_start_date" class="form-control" value="{{ $task->t_start_date }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">End Date</label>
            <input type="date" name="t_end_date" class="form-control" value="{{ $task->t_end_date }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Task Days</label>
            <input type="number" name="task_days" class="form-control" value="{{ $task->task_days }}" required>
          </div>

          <div class="col-md-3">
            <label class="form-label">Task Duration</label>
            <input type="number" name="task_duration" class="form-control" value="{{ $task->task_duration }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Task Marks</label>
            <input type="number" step="0.01" name="task_mark" class="form-control" value="{{ $task->task_mark }}" required>
          </div>

          <div class="col-md-6">
            <label class="form-label">Task Status</label>
            <select name="task_status" class="form-control" required>
              <option value="Ongoing" {{ $task->task_status == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
              <option value="Completed" {{ $task->task_status == 'Completed' ? 'selected' : '' }}>Completed</option>
              <option value="Pending" {{ $task->task_status == 'Pending' ? 'selected' : '' }}>Pending</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required>{{ $task->description }}</textarea>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Task</button>
            <a href="{{ route('supervisor.projects.tasks', $project->project_id) }}" class="btn btn-secondary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
