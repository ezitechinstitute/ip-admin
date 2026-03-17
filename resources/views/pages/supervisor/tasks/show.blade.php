@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Task Details</h4>
      <p class="text-muted mb-0">{{ $task->title }}</p>
    </div>
    <div class="d-flex gap-2">
      @if(in_array($task->status, ['pending', 'submitted']))
      <a href="{{ route('supervisor.tasks.edit', $task->id) }}" class="btn btn-warning">
        <i class="ti ti-edit me-1"></i>Edit Task
      </a>
      @endif
      <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
  <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  {{-- Task Details Column --}}
  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-info-circle me-2"></i>Task Information
        </h6>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="35%">Task ID:</th>
            <td><span class="badge bg-label-primary fs-6">#{{ $task->id }}</span></td>
          </tr>
          <tr>
            <th>Title:</th>
            <td class="fw-semibold">{{ $task->title }}</td>
          </tr>
          <tr>
            <th>Description:</th>
            <td>{{ $task->description ?? 'No description provided' }}</td>
          </tr>
          <tr>
            <th>Assigned To:</th>
            <td>
              <div class="fw-semibold">{{ $task->intern->name }}</div>
              <small class="text-muted">{{ $task->intern->email }}</small>
            </td>
          </tr>
          <tr>
            <th>Deadline:</th>
            <td>
              <span class="{{ $task->deadline < now() && $task->status == 'pending' ? 'text-danger fw-bold' : '' }}">
                {{ \Carbon\Carbon::parse($task->deadline)->format('d M Y, h:i A') }}
              </span>
              @if($task->deadline < now() && $task->status == 'pending')
              <span class="badge bg-danger ms-2">Overdue</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>Points:</th>
            <td><span class="badge bg-primary">{{ $task->points }}</span></td>
          </tr>
          <tr>
            <th>Status:</th>
            <td>
              @php
                $statusClass = match($task->status) {
                  'pending' => 'warning',
                  'submitted' => 'info',
                  'approved' => 'success',
                  'rejected' => 'danger',
                  'expired' => 'secondary',
                  default => 'secondary'
                };
              @endphp
              <span class="badge bg-{{ $statusClass }} text-white px-3 py-2">
                {{ ucfirst($task->status) }}
              </span>
            </td>
          </tr>
          <tr>
            <th>Created At:</th>
            <td>{{ $task->created_at->format('d M Y, h:i A') }}</td>
          </tr>
          @if($task->submitted_at)
          <tr>
            <th>Submitted At:</th>
            <td>{{ \Carbon\Carbon::parse($task->submitted_at)->format('d M Y, h:i A') }}</td>
          </tr>
          @endif
          @if($task->reviewed_at)
          <tr>
            <th>Reviewed At:</th>
            <td>{{ \Carbon\Carbon::parse($task->reviewed_at)->format('d M Y, h:i A') }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>

    @if($task->status == 'submitted' || $task->status == 'approved' || $task->status == 'rejected')
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-edit me-2"></i>Grading Information
        </h6>
      </div>
      <div class="card-body">
        @if($task->status == 'submitted')
        <form method="POST" action="{{ route('supervisor.tasks.grade', $task->id) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">Grade (Points)</label>
            <input type="number" name="grade" class="form-control" 
                   min="0" max="{{ $task->points }}" required>
            <small class="text-muted">Max points: {{ $task->points }}</small>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Remarks</label>
            <textarea name="supervisor_remarks" class="form-control" rows="3" 
                      placeholder="Add feedback for the intern"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit Grade</button>
        </form>
        @else
        <table class="table table-borderless">
          <tr>
            <th>Grade:</th>
            <td>
              <span class="badge bg-{{ $task->status == 'approved' ? 'success' : 'danger' }} fs-6">
                {{ $task->grade }}/{{ $task->points }}
              </span>
            </td>
          </tr>
          <tr>
            <th>Remarks:</th>
            <td>{{ $task->supervisor_remarks ?? 'No remarks provided' }}</td>
          </tr>
        </table>
        @endif
      </div>
    </div>
    @endif
  </div>

  {{-- Submission Details Column --}}
  <div class="col-lg-7">
    @if($task->submission_notes || $task->status == 'submitted')
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-upload me-2"></i>Submission Details
        </h6>
      </div>
      <div class="card-body">
        @if($task->submission_notes)
        <div class="mb-4">
          <h6 class="fw-semibold">Submission Notes:</h6>
          <p class="p-3 bg-light rounded">{{ $task->submission_notes }}</p>
        </div>
        @endif
        
        @if($task->submitted_at)
        <div class="alert alert-info">
          <i class="ti ti-clock me-2"></i>
          Submitted on {{ \Carbon\Carbon::parse($task->submitted_at)->format('d M Y, h:i A') }}
        </div>
        @endif

        @if($task->status == 'submitted')
        <div class="alert alert-warning">
          <i class="ti ti-alert-triangle me-2"></i>
          This task is pending your review and grading.
        </div>
        @endif
      </div>
    </div>
    @else
    <div class="card">
      <div class="card-body text-center py-5">
        <i class="ti ti-cloud-upload ti-3x text-muted mb-3"></i>
        <h6>No submission yet</h6>
        <p class="text-muted">The intern hasn't submitted this task.</p>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection