@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $task->task_title }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Task Information</h6>
                    <table class="table table-bordered">
                        <tr><th>Task ID</th><td>{{ $task->task_id }}</td></tr>
                        <tr><th>Intern</th><td>{{ $task->intern_name }} ({{ $task->eti_id }})</td></tr>
                        <tr><th>Supervisor</th><td>{{ $task->supervisor_name ?? 'N/A' }}</td></tr>
                        <tr><th>Technology</th><td>{{ $task->technology ?? 'N/A' }}</td></tr>
                        <tr><th>Deadline</th><td>{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</td></tr>
                        <tr><th>Points</th><td>{{ $task->task_points ?? 'N/A' }}</td></tr>
                        <tr><th>Status</th>
                            <td>
                                <span class="badge bg-{{ $task->task_status == 'approved' ? 'success' : ($task->task_status == 'submitted' ? 'info' : 'warning') }}">
                                    {{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Description</h6>
                    <p>{!! $task->task_description ?? 'No description provided' !!}</p>                    
                    @if($task->task_git_url || $task->task_live_url || $task->task_screenshot)
                    <h6 class="mt-4">Submission Details</h6>
                    <table class="table table-bordered">
                        @if($task->task_git_url)
                        <tr><th>GitHub URL</th><td><a href="{{ $task->task_git_url }}" target="_blank">{{ $task->task_git_url }}</a></td></tr>
                        @endif
                        @if($task->task_live_url)
                        <tr><th>Live URL</th><td><a href="{{ $task->task_live_url }}" target="_blank">{{ $task->task_live_url }}</a></td></tr>
                        @endif
                        @if($task->submit_description)
                        <tr><th>Submission Notes</th><td>{{ $task->submit_description }}</td></tr>
                        @endif
                        @if($task->task_screenshot)
                        <tr><th>Screenshot</th><td><a href="{{ asset($task->task_screenshot) }}" target="_blank" class="btn btn-sm btn-primary">View Screenshot</a></td></tr>
                        @endif
                    </table>
                    @endif
                    
                    @if($task->review)
                    <h6 class="mt-4">Supervisor Feedback</h6>
                    <div class="alert alert-info">
                        <strong>Remarks:</strong> {{ $task->review }}<br>
                        @if($task->grade)
                        <strong>Grade:</strong> {{ $task->grade }}%
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ url('/manager/tasks') }}" class="btn btn-secondary">
                    <i class="icon-base ti tabler-arrow-left"></i> Back to Tasks
                </a>
            </div>
        </div>
    </div>
</div>
@endsection