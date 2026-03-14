@extends('layouts/layoutMaster')

@section('title', 'Supervisor Details')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Supervisor Details: {{ $supervisor->name }}</h4>
        <a href="{{ route('manager.supervisor') }}" class="btn btn-secondary">Back to list</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Total Interns</h6><h3>{{ $totalInterns }}</h3></div></div>
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Avg Review Time (hrs)</h6><h3>{{ $averageReviewTimeHours }}</h3></div></div>
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Pending Reviews</h6><h3>{{ $pendingReviews }}</h3></div></div>
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Completion Rate</h6><h3>{{ $completionRate }}%</h3></div></div>
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Commission</h6><h3>{{ $supervisor->comission ?? 'N/A' }}</h3></div></div>
        <div class="col-md-2"><div class="card p-3 shadow-sm"><h6>Status</h6><h3>{{ $supervisor->status ? 'Active' : 'Frozen' }}</h3></div></div>
    </div>

    <div class="mb-4">
        <h5>Actions</h5>
        <form method="POST" action="{{ route('manager.supervisor.toggleFreeze', $supervisor->manager_id) }}" class="d-inline">
            @csrf @method('PATCH')
            <button type="submit" class="btn {{ $supervisor->status ? 'btn-warning' : 'btn-success' }} me-2">
                {{ $supervisor->status ? 'Freeze Supervisor' : 'Unfreeze Supervisor' }}
            </button>
        </form>
    </div>

    <div class="card mb-4 p-3">
        <h5>Reassign Intern</h5>
        <form method="POST" action="{{ route('manager.supervisor.reassignIntern', $supervisor->manager_id) }}">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Intern Assignment ID</label>
                    <input type="number" name="assignment_id" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">New Supervisor</label>
                    <select class="form-select" name="new_supervisor_id" required>
                        <option value="">Select Supervisor</option>
                        @foreach (\App\Models\ManagersAccount::where('loginas', 'Supervisor')->get() as $sup)
                            <option value="{{ $sup->manager_id }}" {{ $sup->manager_id == $supervisor->manager_id ? 'selected' : '' }}>
                                {{ $sup->name }} (ID {{ $sup->manager_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">Reassign Intern</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card mb-4 p-3">
        <h5>Activity Log</h5>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th>Assigned On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activityLog as $log)
                    <tr>
                        <td>{{ $log->mapping_id }}</td>
                        <td>{{ $log->project->project_title ?? 'N/A' }}</td>
                        <td>{{ $log->assigned_date }}</td>
                        <td>{{ $log->status ? 'Active' : 'Inactive' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">No activity log entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
