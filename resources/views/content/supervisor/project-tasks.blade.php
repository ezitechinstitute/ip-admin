@extends('layouts/layoutMaster')

@section('title', 'Project Tasks')
@php
use Illuminate\Support\Str;
@endphp
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Project Tasks: {{ $project->title ?? 'N/A' }}</h4>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-label-primary" data-bs-toggle="modal" data-bs-target="#loadCurriculumModal">
        <i class="ti tabler-download me-1"></i> Load Curriculum
      </button>
      <a href="{{ route('supervisor.projects') }}" class="btn btn-secondary">Back to Projects</a>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Create New Task</h5>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('supervisor.projects.tasks.store', $project->project_id) }}">
            @csrf
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Task Title</label>
                <input type="text" name="task_title" class="form-control" placeholder="Enter task title" required>
              </div>

              <div class="col-12">
                <label class="form-label">Milestone Title (Optional)</label>
                <input type="text" name="milestone_title" class="form-control" placeholder="e.g. Week 1: Basics">
              </div>

              <div class="col-6">
                <label class="form-label">Start Date</label>
                <input type="date" name="t_start_date" class="form-control" required>
              </div>

              <div class="col-6">
                <label class="form-label">End Date</label>
                <input type="date" name="t_end_date" class="form-control" required>
              </div>

              <div class="col-6">
                <label class="form-label">Days</label>
                <input type="number" name="task_days" class="form-control" value="1" required>
              </div>

              <div class="col-6">
                <label class="form-label">Marks</label>
                <input type="number" step="0.01" name="task_mark" class="form-control" required>
              </div>

              <input type="hidden" name="task_duration" value="1">
              <input type="hidden" name="task_status" value="Ongoing">

              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
              </div>

              <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">Create Task</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      @php
        $milestones = $tasks->groupBy('milestone_title');
      @endphp

      @forelse($milestones as $milestone => $milestoneTasks)
        <div class="card mb-4">
          <div class="card-header bg-label-secondary d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $milestone ?: 'Uncategorized Tasks' }}</h5>
            <span class="badge bg-primary">{{ $milestoneTasks->count() }} Tasks</span>
          </div>
          <div class="table-responsive text-nowrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Task Title</th>
                  <th>Deadline</th>
                  <th>Marks</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($milestoneTasks as $task)
                  <tr>
                    <td>
                      <div class="fw-bold">{{ $task->task_title }}</div>
                      <small class="text-muted">{{ Str::limit($task->description, 40) }}</small>
                    </td>
                    <td>{{ $task->t_end_date }}</td>
                    <td>{{ $task->task_mark }}</td>
                    <td>
                      <span class="badge bg-label-{{ $task->task_status == 'Completed' ? 'success' : ($task->task_status == 'Rejected' ? 'danger' : 'warning') }}">
                        {{ $task->task_status }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-2">
                        <a href="{{ route('supervisor.projects.tasks.edit', [$project->project_id, $task->task_id]) }}" class="btn btn-sm btn-icon btn-label-warning">
                          <i class="ti tabler-edit"></i>
                        </a>
                        <form action="{{ route('supervisor.projects.tasks.delete', [$project->project_id, $task->task_id]) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-icon btn-label-danger">
                            <i class="ti tabler-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @empty
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="ti tabler-clipboard-off mb-2 display-6 text-muted"></i>
            <h5>No tasks assigned yet</h5>
            <p class="text-muted">Create a task manually or load a curriculum to get started.</p>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>

<!-- Load Curriculum Modal -->
<div class="modal fade" id="loadCurriculumModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Load Curriculum Template</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('supervisor.projects.tasks.loadCurriculum', $project->project_id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <p>Select a technology to load the standard curriculum tasks into this project.</p>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Standard Curriculums</label>
              <select name="technology" class="form-select" required>
                <option value="">-- Select Technology --</option>
                <option value="Laravel">Laravel Development (Full Stack)</option>
                <option value="React">React.js Frontend Development</option>
                <option value="UI/UX">UI/UX Design Fundamentals</option>
                <option value="WordPress">WordPress Engine & SEO</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Set Start Date for All Tasks</label>
              <input type="date" name="base_start_date" class="form-control" required>
            </div>
            <div class="col-12 mt-3">
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <span class="alert-icon text-info me-2">
                  <i class="ti tabler-info-circle"></i>
                </span>
                Tasks will be automatically created following the curriculum milestones. Marks and descriptions will be pre-filled.
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-toggle="modal">Close</button>
          <button type="submit" class="btn btn-primary">Import Curriculum</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
