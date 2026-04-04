@extends('layouts/layoutMaster')

@section('title', 'Tasks Overview')

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
      <h4 class="mt-6 mb-1 fw-bold">Tasks Overview</h4>
      <p class="text-muted mb-0">View all tasks assigned to your interns</p>
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
            <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
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
            <h3 class="mb-0 fw-bold">{{ $stats['pending'] }}</h3>
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
            <h3 class="mb-0 fw-bold">{{ $stats['submitted'] }}</h3>
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
            <h3 class="mb-0 fw-bold">{{ $stats['overdue'] }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Filters --}}
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" action="{{ route('manager.tasks.index') }}" id="filterForm">
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
          <div class="row">
            <div class="col-md-8">
              <input type="search" name="search" class="form-control" 
                     placeholder="Search by task title or intern name" value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
              <button type="submit" class="btn btn-primary">Apply Filters</button>
              <a href="{{ route('manager.tasks.index') }}" class="btn btn-secondary">Reset</a>
              <button type="button" class="btn btn-outline-primary" onclick="exportTasks()">
                <i class="ti ti-download me-1"></i>Export
              </button>
            </div>
          </div>
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
            <th>Supervisor</th>
            <th>Deadline</th>
            <th>Points</th>
            <th>Status</th>
            <th>Grade</th>
            <th>Action</th>
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
<small class="text-muted">{{ strlen($task->description) > 50 ? substr($task->description, 0, 50) . '...' : $task->description }}</small>            </td>
            <td>
              <div>{{ $task->intern->name ?? 'N/A' }}</div>
              <small class="text-muted">{{ $task->intern->email ?? '' }}</small>
            </td>
            <td>{{ $task->supervisor->name ?? 'N/A' }}</td>
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
              @if($task->grade)
              <span class="badge bg-{{ $task->status == 'approved' ? 'success' : 'danger' }}">
                {{ $task->grade }}/{{ $task->points }}
              </span>
              @else
              <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              <a href="{{ route('manager.tasks.show', $task->id) }}" 
                 class="btn btn-sm btn-outline-info" 
                 title="View Details">
                <i class="ti ti-eye"></i> View
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center py-5">
              <i class="ti ti-list ti-3x text-muted mb-3"></i>
              <h6>No tasks found</h6>
              <p class="text-muted mb-3">No tasks assigned to your interns yet</p>
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
@endsection

@push('scripts')
<script>
function exportTasks() {
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);
  const params = new URLSearchParams(formData).toString();
  window.location.href = "{{ route('manager.tasks.export') }}?" + params;
}
</script>
@endpush