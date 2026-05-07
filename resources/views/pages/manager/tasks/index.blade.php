@extends('layouts/layoutMaster')

@section('title', 'Tasks Overview')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="icon-base ti tabler-tasks icon-xl text-primary mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small class="text-muted">Total Tasks</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="icon-base ti tabler-clock icon-xl text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="icon-base ti tabler-send icon-xl text-info mb-2"></i>
                    <h3 class="mb-0">{{ $stats['submitted'] }}</h3>
                    <small class="text-muted">Submitted</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="icon-base ti tabler-circle-check icon-xl text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                    <small class="text-muted">Approved</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    </select>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ url('/manager/tasks') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tasks Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Tasks Overview</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
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
                        @forelse($tasks as $task)
                        <tr>
                            <td>{{ $loop->iteration + ($tasks->firstItem() - 1) }}</td>
                            <td>
    <div class="fw-semibold">{{ $task->title }}</div>
</td>
                            <td>{{ $task->intern_name }}<br><small class="text-muted">{{ $task->eti_id }}</small></td>
                            <td>{{ $task->supervisor_name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status != 'approved' ? 'danger' : 'info' }}">
                                    {{ \Carbon\Carbon::parse($task->deadline)->format('d M, Y') }}
                                </span>
                            </td>
                            <td>{{ $task->points ?? '—' }}</td>
                            <td>
                                @php
                                    $statusColors = ['pending' => 'warning','submitted' => 'info','approved' => 'success','rejected' => 'danger'];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }}">
                                    {{ ucfirst($task->status) }}
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
                                <a href="{{ url('/manager/tasks/' . $task->id) }}" class="btn btn-sm btn-primary">
                                    <i class="icon-base ti tabler-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="icon-base ti tabler-clipboard-x icon-xl text-muted mb-3"></i>
                                <p class="text-muted mb-0">No tasks found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ✅ PAGINATION --}}
        <div class="card-footer">
            <div class="row mx-0 justify-content-between align-items-center">
                <div class="col-md-auto me-auto">
                    <small class="text-muted">
                        Showing {{ $tasks->firstItem() ?? 0 }} to {{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() ?? 0 }} entries
                    </small>
                </div>
                <div class="col-md-auto ms-auto">
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->url(1) }}">
                                    <i class="icon-base ti tabler-chevrons-left icon-18px"></i>
                                </a>
                            </li>
                            <li class="page-item {{ $tasks->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->previousPageUrl() }}">
                                    <i class="icon-base ti tabler-chevron-left icon-18px"></i>
                                </a>
                            </li>
                            @foreach ($tasks->getUrlRange(max(1, $tasks->currentPage() - 2), min($tasks->lastPage(), $tasks->currentPage() + 2)) as $page => $url)
                            <li class="page-item {{ $page == $tasks->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                            @endforeach
                            <li class="page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->nextPageUrl() }}">
                                    <i class="icon-base ti tabler-chevron-right icon-18px"></i>
                                </a>
                            </li>
                            <li class="page-item {{ $tasks->currentPage() == $tasks->lastPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $tasks->url($tasks->lastPage()) }}">
                                    <i class="icon-base ti tabler-chevrons-right icon-18px"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection