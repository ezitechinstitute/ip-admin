@extends('layouts/layoutMaster')

@section('title', 'My Tasks')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Tasks</h5>
        </div>
        <div class="card-body">
            @if(isset($tasks) && $tasks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Task Title</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->task_title }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $task->task_status == 'approved' ? 'success' : ($task->task_status == 'pending' ? 'warning' : 'info') }}">
                                    {{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ url('/intern/tasks/' . $task->task_id) }}" class="btn btn-sm btn-primary">
                                    View Details
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $tasks->links() }}
            @else
            <div class="text-center py-5">
                <i class="ti ti-tasks-off ti-3x text-muted mb-3"></i>
                <p>No tasks assigned yet</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection