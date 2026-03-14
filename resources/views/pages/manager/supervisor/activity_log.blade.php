@extends('layouts/layoutMaster')

@section('title', 'Supervisor Activity Log')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Activity Log for {{ $supervisor->name }}</h4>
        <a href="{{ route('manager.supervisor.show', $supervisor->manager_id) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card p-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Project</th>
                    <th>Assigned By</th>
                    <th>Assigned Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activityLog as $log)
                    <tr>
                        <td>{{ $log->mapping_id }}</td>
                        <td>{{ $log->project->project_title ?? 'N/A' }}</td>
                        <td>{{ $log->supervisor->name ?? 'N/A' }}</td>
                        <td>{{ $log->assigned_date }}</td>
                        <td>{{ $log->status ? 'Active' : 'Inactive' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No log entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
