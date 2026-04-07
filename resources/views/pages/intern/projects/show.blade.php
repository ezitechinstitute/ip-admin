@extends('layouts/layoutMaster')

@section('title', 'Project Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $project->title }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Project Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Technology</th>
                            <td>{{ $project->tech_stack ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Supervisor</th>
                            <td>{{ $supervisorName }}</td>
                        </tr>
                        <tr>
                            <th>Start Date</th>
                            <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <th>End Date</th>
                            <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusColors = [
                                        'ongoing' => 'warning',
                                        'submitted' => 'info',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$project->pstatus] ?? 'secondary' }}">
                                    {{ ucfirst($project->pstatus) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Project Description</h6>
                    <p>{{ $project->description ?? 'No description provided' }}</p>
                </div>
            </div>
            
            @if($tasks->count() > 0)
            <div class="mt-4">
                <h6>Project Tasks</h6>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task Title</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $task)
                            <tr>
                                <td>{{ $task->task_title }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $task->task_status == 'approved' ? 'success' : 'warning' }}">
                                        {{ ucfirst($task->task_status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ route('intern.projects') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back to Projects
                </a>
            </div>
        </div>
    </div>
</div>
@endsection