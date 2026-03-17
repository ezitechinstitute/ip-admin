@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Task Details</h4>
      <p class="text-muted mb-0">{{ $task->title }}</p>
    </div>
    <a href="{{ route('manager.tasks.index') }}" class="btn btn-secondary">
      <i class="ti ti-arrow-left me-1"></i>Back to Tasks
    </a>
  </div>
</div>

<div class="row">
  {{-- Task Details Column --}}
  <div class="col-lg-6">
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
            <th>Supervisor:</th>
            <td>{{ $task->supervisor->name ?? 'N/A' }}</td>
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

    @if($task->grade)
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-star me-2"></i>Grading Information
        </h6>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="35%">Grade:</th>
            <td>
              <span class="badge bg-{{ $task->status == 'approved' ? 'success' : 'danger' }} fs-6">
                {{ $task->grade }}/{{ $task->points }}
              </span>
            </td>
          </tr>
          @if($task->supervisor_remarks)
          <tr>
            <th>Remarks:</th>
            <td>{{ $task->supervisor_remarks }}</td>
          </tr>
          @endif
        </table>
      </div>
    </div>
    @endif
  </div>

  {{-- Submission Details Column --}}
  <div class="col-lg-6">
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
          <div class="p-3 bg-light rounded">
            {{ $task->submission_notes }}
          </div>
        </div>
        @else
        <div class="text-center py-4">
          <i class="ti ti-cloud-upload ti-2x text-muted mb-2"></i>
          <p class="text-muted">No submission notes provided</p>
        </div>
        @endif

        @if($task->submitted_at)
        <div class="alert alert-info mt-3">
          <i class="ti ti-clock me-2"></i>
          Submitted on {{ \Carbon\Carbon::parse($task->submitted_at)->format('d M Y, h:i A') }}
        </div>
        @endif

        <div class="alert alert-secondary mt-3">
          <i class="ti ti-info-circle me-2"></i>
          <strong>Note:</strong> This is a view-only page. Only supervisors can modify tasks.
        </div>
      </div>
    </div>
  </div>
</div>
@endsection