@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8">
            {{-- Task Details Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $task->title }}</h5>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'submitted' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusColors[$task->status] ?? 'secondary' }} p-2">
                        {{ ucfirst($task->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Description</h6>
                        <p>{{ $task->description ?? 'No description provided' }}</p>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Deadline</small>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="ti ti-calendar me-2 text-primary"></i>
                                    <span>{{ isset($task->deadline) ? \Carbon\Carbon::parse($task->deadline)->format('d M, Y') : 'No deadline' }}</span>
                                    @if(isset($task->deadline) && \Carbon\Carbon::parse($task->deadline)->isPast())
                                        <span class="badge bg-danger ms-2">Overdue</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block">Points</small>
                                <div class="d-flex align-items-center mt-1">
                                    <i class="ti ti-star me-2 text-warning"></i>
                                    <span>{{ $task->points ?? 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feedback Card (if task is reviewed) --}}
            @if(in_array($task->status, ['approved', 'rejected']) && ($task->grade || $task->supervisor_remarks))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-message-circle me-2 text-primary"></i>
                        Supervisor Feedback
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->grade)
                    <div class="mb-3">
                        <small class="text-muted d-block">Score</small>
                        <h3 class="mb-0 text-primary">{{ $task->grade }}%</h3>
                    </div>
                    @endif
                    
                    @if($task->supervisor_remarks)
                    <div class="mb-3">
                        <small class="text-muted d-block">Remarks</small>
                        <p class="mb-0">{{ $task->supervisor_remarks }}</p>
                    </div>
                    @endif
                    
                    @if($task->status == 'rejected')
                    <div class="alert alert-warning mt-3">
                        <i class="ti ti-alert-triangle me-2"></i>
                        Task needs revision. Please update your submission.
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Your Submission Card --}}
            @if($task->github_url || $task->live_url || $task->submission_notes)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-upload me-2 text-primary"></i>
                        Your Submission
                    </h5>
                </div>
                <div class="card-body">
                    @if($task->github_url)
                    <div class="mb-3">
                        <small class="text-muted d-block">GitHub Link</small>
                        <a href="{{ $task->github_url }}" target="_blank" class="text-primary">
                            <i class="ti ti-brand-github me-1"></i> {{ $task->github_url }}
                        </a>
                    </div>
                    @endif
                    
                    @if($task->live_url)
                    <div class="mb-3">
                        <small class="text-muted d-block">Live Project URL</small>
                        <a href="{{ $task->live_url }}" target="_blank" class="text-primary">
                            <i class="ti ti-world me-1"></i> {{ $task->live_url }}
                        </a>
                    </div>
                    @endif
                    
                    @if($task->submission_notes)
                    <div class="mb-3">
                        <small class="text-muted d-block">Submission Notes</small>
                        <p class="mb-0">{{ $task->submission_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar - Submission Form --}}
        <div class="col-lg-4">
            @if($canResubmit)
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-send me-2 text-primary"></i>
                        Submit Task
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('intern.tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">GitHub Repository URL</label>
                            <input type="url" name="github_url" class="form-control" 
                                   placeholder="https://github.com/username/repo"
                                   value="{{ old('github_url', $task->github_url) }}">
                            <small class="text-muted">Optional</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Live Project URL</label>
                            <input type="url" name="live_url" class="form-control" 
                                   placeholder="https://your-project.com"
                                   value="{{ old('live_url', $task->live_url) }}">
                            <small class="text-muted">Optional</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Submission Notes</label>
                            <textarea name="submission_notes" class="form-control" rows="4" 
                                      placeholder="Any additional notes for the supervisor...">{{ old('submission_notes', $task->submission_notes) }}</textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-1"></i> Submit Task
                        </button>
                    </form>
                </div>
            </div>
            @elseif($isApproved)
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-check-circle text-success fs-1 mb-3"></i>
                    <h5>Task Completed!</h5>
                    <p class="text-muted">This task has been approved. Great work!</p>
                </div>
            </div>
            @elseif($isSubmitted)
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-clock text-warning fs-1 mb-3"></i>
                    <h5>Awaiting Review</h5>
                    <p class="text-muted">Your submission is pending supervisor review.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ url('/intern/tasks') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>
</div>
@endsection