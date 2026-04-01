@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $task->task_title }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $task->task_description ?? 'No description' }}</p>
            <p><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($task->task_status) }}</p>
            <a href="{{ route('intern.tasks') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection