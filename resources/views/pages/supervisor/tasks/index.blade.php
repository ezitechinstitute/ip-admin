@extends('layouts/layoutMaster')

@section('title', 'Task Management')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Task Management</h4>
      <p class="text-muted mb-0">Create and manage intern tasks</p>
    </div>
    <div>
      <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Create New Task
      </a>
    </div>
  </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-4 mb-6">
  <div class="col-xl-3 col-md-6">
    <div class="card border-start border-4 border-primary shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-primary bg-opacity-10 rounded p-3">
            <i class="ti ti-list ti-lg text-primary"></i>
          </div>
          <div>
            <span class="text-muted d-block">Total Tasks</span>
            <h3 class="mb-0 fw-bold">{{ $tasks->total() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-start border-4 border-warning shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-warning bg-opacity-10 rounded p-3">
            <i class="ti ti-clock ti-lg text-warning"></i>
          </div>
          <div>
            <span class="text-muted d-block">Pending</span>
            <h3 class="mb-0 fw-bold">{{ $tasks->where('status', 'pending')->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-start border-4 border-info shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-info bg-opacity-10 rounded p-3">
            <i class="ti ti-upload ti-lg text-info"></i>
          </div>
          <div>
            <span class="text-muted d-block">Submitted</span>
            <h3 class="mb-0 fw-bold">{{ $tasks->where('status', 'submitted')->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-3 col-md-6">
    <div class="card border-start border-4 border-danger shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-danger bg-opacity-10 rounded p-3">
            <i class="ti ti-alert-triangle ti-lg text-danger"></i>
          </div>
          <div>
            <span class="text-muted d-block">Overdue</span>
            <h3 class="mb-0 fw-bold">{{ $tasks->filter(function($task) { 
                return $task->deadline < now() && in_array($task->status, ['pending', 'submitted']); 
            })->count() }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('supervisor.tasks.index') }}" id="filterForm">
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">From Date</label>
          <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">To Date</label>
          <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Intern</label>
          <select name="intern_id" class="form-select">
            <option value="">All Interns</option>
            @foreach($interns as $intern)
            <option value="{{ $intern->int_id }}" {{ request('intern_id') == $intern->int_id ? 'selected' : '' }}>
              {{ $intern->name }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
          <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary">Reset</a>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Tasks Table --}}
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="bg-light">
          <tr>
            <th>#</th>
            <th>Task Title</th>
            <th>Intern</th>
            <th>Deadline</th>
            <th>Points</th>
            <th>Status</th>
            <th>Submission</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $index => $task)
          @php
            $statusClass = match($task->status) {
              'pending' => 'warning',
              'submitted' => 'info',
              'approved' => 'success',
              'rejected' => 'danger',
              'expired' => 'secondary',
              default => 'secondary'
            };
            
            $isOverdue = $task->deadline < now() && in_array($task->status, ['pending', 'submitted']);
          @endphp
          <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
            <td>{{ $tasks->firstItem() + $index }}</td>
            <td>
              <div class="fw-semibold">{{ $task->title }}</div>
              {{-- FIXED: Replaced Str::limit with PHP substr --}}
              <small class="text-muted">{{ strlen($task->description) > 50 ? substr($task->description, 0, 50) . '...' : $task->description }}</small>
            </td>
            <td>
              <div>{{ $task->intern->name ?? 'N/A' }}</div>
              <small class="text-muted">{{ $task->intern->email ?? '' }}</small>
            </td>
            <td>
              <div>{{ \Carbon\Carbon::parse($task->deadline)->format('d M, Y') }}</div>
              @if($isOverdue)
              <span class="badge bg-danger mt-1">Overdue</span>
              @endif
            </td>
            <td><span class="badge bg-primary">{{ $task->points }}</span></td>
            <td>
              <span class="badge bg-{{ $statusClass }} text-white px-3 py-2">
                {{ ucfirst($task->status) }}
              </span>
            </td>
            <td>
              @if($task->status == 'submitted')
              <button class="btn btn-sm btn-info" onclick="viewSubmission({{ $task->id }})">
                <i class="ti ti-eye"></i> View
              </button>
              @elseif($task->status == 'approved')
              <span class="badge bg-success">Grade: {{ $task->grade }}/{{ $task->points }}</span>
              @elseif($task->status == 'rejected')
              <span class="badge bg-danger">Grade: {{ $task->grade }}/{{ $task->points }}</span>
              @else
              <span class="text-muted">Not submitted</span>
              @endif
            </td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('supervisor.tasks.show', $task->id) }}" 
                   class="btn btn-sm btn-outline-info" 
                   title="View">
                  <i class="ti ti-eye"></i>
                </a>
                @if(in_array($task->status, ['pending', 'submitted']))
                <a href="{{ route('supervisor.tasks.edit', $task->id) }}" 
                   class="btn btn-sm btn-outline-warning" 
                   title="Edit">
                  <i class="ti ti-edit"></i>
                </a>
                <button onclick="deleteTask({{ $task->id }})" 
                        class="btn btn-sm btn-outline-danger" 
                        title="Delete">
                  <i class="ti ti-trash"></i>
                </button>
                @endif
                @if($task->status == 'submitted')
                <button onclick="gradeTask({{ $task->id }})" 
                        class="btn btn-sm btn-outline-success" 
                        title="Grade">
                  <i class="ti ti-star"></i>
                </button>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center py-5">
              <i class="ti ti-list ti-3x text-muted mb-3"></i>
              <h6>No tasks found</h6>
              <p class="text-muted mb-3">Create your first task to get started</p>
              <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Create Task
              </a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="row mt-4 justify-content-between align-items-center">
      <div class="col-md-auto">
        Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() ?? 0 }} entries
      </div>
      <div class="col-md-auto">
        {{ $tasks->appends(request()->query())->links() }}
      </div>
    </div>
  </div>
</div>

{{-- Grade Modal --}}
<div class="modal fade" id="gradeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="" id="gradeForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Grade Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Grade (Points)</label>
            <input type="number" name="grade" id="grade_input" class="form-control" 
                   min="0" max="100" required>
            <small class="text-muted">Max points: <span id="max_points">100</span></small>
          </div>
          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="supervisor_remarks" class="form-control" rows="3" 
                      placeholder="Add feedback for the intern"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit Grade</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function deleteTask(id) {
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: `/supervisor/tasks/${id}`,
        type: 'DELETE',
        data: {
          _token: '{{ csrf_token() }}'
        },
        success: function(response) {
          Swal.fire('Deleted!', 'Task has been deleted.', 'success')
            .then(() => window.location.reload());
        },
        error: function(xhr) {
          Swal.fire('Error!', 'Failed to delete task.', 'error');
        }
      });
    }
  });
}

function gradeTask(id) {
  // Get task points via AJAX
  $.get(`/supervisor/tasks/${id}`, function(task) {
    $('#max_points').text(task.points);
    $('#grade_input').attr('max', task.points);
    $('#gradeForm').attr('action', `/supervisor/tasks/${id}/grade`);
    $('#gradeModal').modal('show');
  });
}

function viewSubmission(id) {
  $.get(`/supervisor/tasks/${id}`, function(task) {
    Swal.fire({
      title: 'Task Submission',
      html: `
        <div style="text-align: left">
          <p><strong>Task:</strong> ${task.title}</p>
          <p><strong>Submission Notes:</strong><br>${task.submission_notes || 'No notes'}</p>
          ${task.submitted_at ? `<p><strong>Submitted:</strong> ${new Date(task.submitted_at).toLocaleString()}</p>` : ''}
        </div>
      `,
      icon: 'info',
      confirmButtonText: 'OK'
    });
  });
}
</script>
@endpush