@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ✅ PORTAL FREEZE ERROR MESSAGE --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-3 me-3"></i>
            <div>
                <strong class="d-block">⚠️ Portal Frozen</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- ✅ SUCCESS MESSAGE --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-check-circle fs-3 me-3"></i>
            <div>
                <strong class="d-block">Success!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Task Details Card --}}
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 mb-4 hover-scale">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0">
                    <h5 class="mb-0 fw-bold">{{ $task->task_title }}</h5>
                    @php
                        $statusColors = [
                            'pending' => 'warning',
                            'submitted' => 'info',
                            'approved' => 'success',
                            'rejected' => 'danger',
                        ];
                    @endphp
                    <span class="badge rounded-pill bg-{{ $statusColors[$task->task_status] ?? 'secondary' }} px-3 py-2">
                        {{ ucfirst($task->task_status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold">Description</h6>
                        <p class="text-muted">{{ $task->task_description ?? 'No description provided' }}</p>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 d-flex align-items-center bg-light">
                                <i class="ti ti-calendar fs-3 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Deadline</small>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</span>
                                        @if(\Carbon\Carbon::parse($task->task_end)->isPast())
                                        <span class="badge rounded-pill bg-danger ms-2">Overdue</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-4 p-3 d-flex align-items-center bg-light">
                                <i class="ti ti-star fs-3 text-warning me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Points</small>
                                    <span class="fw-medium">{{ $task->task_points ?? 'Not specified' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feedback Card --}}
            @if(in_array($task->task_status, ['approved', 'rejected']) && ($task->grade || $task->review))
            <div class="card shadow-sm rounded-4 mb-4 hover-scale">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-message-circle me-2 text-primary"></i> Supervisor Feedback</h5>
                </div>
                <div class="card-body">
                    @if($task->grade)
                    <div class="mb-3 d-flex align-items-center">
                        <i class="ti ti-star fs-3 text-primary me-2"></i>
                        <div>
                            <small class="text-muted d-block">Score</small>
                            <h3 class="mb-0 text-primary">{{ $task->grade }}%</h3>
                        </div>
                    </div>
                    @endif

                    @if($task->review)
                    <div class="mb-3">
                        <small class="text-muted d-block">Remarks</small>
                        <p class="mb-0">{{ $task->review }}</p>
                    </div>
                    @endif

                    @if($task->task_status == 'rejected')
                    <div class="alert alert-warning d-flex align-items-center mt-3 rounded-4">
                        <i class="ti ti-alert-triangle me-2 fs-4"></i>
                        <span>Task needs revision. Please update your submission.</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Submission Card --}}
            @if($task->task_git_url || $task->task_live_url || $task->task_screenshot || $task->submit_description)
            <div class="card shadow-sm rounded-4 mb-4 hover-scale">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-upload me-2 text-primary"></i> Your Submission</h5>
                </div>
                <div class="card-body">
                    @if($task->task_git_url)
                    <div class="mb-3">
                        <small class="text-muted d-block">GitHub Link</small>
                        <a href="{{ $task->task_git_url }}" target="_blank" class="text-primary fw-medium">
                            <i class="ti ti-brand-github me-1"></i> {{ $task->task_git_url }}
                        </a>
                    </div>
                    @endif

                    @if($task->task_live_url)
                    <div class="mb-3">
                        <small class="text-muted d-block">Live Project URL</small>
                        <a href="{{ $task->task_live_url }}" target="_blank" class="text-primary fw-medium">
                            <i class="ti ti-world me-1"></i> {{ $task->task_live_url }}
                        </a>
                    </div>
                    @endif

                    @if($task->task_screenshot)
                    <div class="mb-3">
                        <small class="text-muted d-block">Screenshot</small>
                        <a href="{{ asset($task->task_screenshot) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="ti ti-photo me-1"></i> View Screenshot
                        </a>
                    </div>
                    @endif

                    @if($task->submit_description)
                    <div class="mb-3">
                        <small class="text-muted d-block">Submission Notes</small>
                        <p class="mb-0">{{ $task->submit_description }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar Submission Form --}}
        <div class="col-lg-4">
            @if($canResubmit)
            <div class="card shadow-sm rounded-4 hover-scale">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold"><i class="ti ti-send me-2 text-primary"></i> Submit Task</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('intern.tasks.submit', $task->task_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-medium">GitHub Repository URL</label>
                            <input type="url" name="task_git_url" class="form-control rounded-pill"
                                   placeholder="https://github.com/username/repo"
                                   value="{{ old('task_git_url', $task->task_git_url) }}">
                            <small class="text-muted">Optional</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Live Project URL</label>
                            <input type="url" name="task_live_url" class="form-control rounded-pill"
                                   placeholder="https://your-project.com"
                                   value="{{ old('task_live_url', $task->task_live_url) }}">
                            <small class="text-muted">Optional</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Screenshot / Image</label>
                            <input type="file" name="task_screenshot" class="form-control rounded-pill" accept="image/*">
                            <small class="text-muted">Max 2MB. JPG, PNG, GIF</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-medium">Submission Notes</label>
                            <textarea name="submit_description" class="form-control rounded-4" rows="4"
                                      placeholder="Any additional notes for the supervisor...">{{ old('submit_description', $task->submit_description) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="ti ti-send me-1"></i> Submit Task
                        </button>
                    </form>
                </div>
            </div>
            @elseif($isApproved)
            <div class="card shadow-sm rounded-4 text-center hover-scale p-4">
                <i class="ti ti-check-circle text-success fs-1 mb-3"></i>
                <h5>Task Completed!</h5>
                <p class="text-muted">This task has been approved. Great work!</p>
            </div>
            @elseif($isSubmitted)
            <div class="card shadow-sm rounded-4 text-center hover-scale p-4">
                <i class="ti ti-clock text-warning fs-1 mb-3"></i>
                <h5>Awaiting Review</h5>
                <p class="text-muted">Your submission is pending supervisor review.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ url('/intern/tasks') }}" class="btn btn-secondary rounded-pill">
            <i class="ti ti-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>
</div>

<style>
/* Card Hover Effect */
.card.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card.hover-scale:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

/* Rounded Pill Inputs and Buttons */
.form-control.rounded-pill, .btn.rounded-pill, textarea.rounded-4 {
    border-radius: 50px;
}

/* Card Inner Shadows for Subtle Depth */
.card-body {
    padding: 1.5rem;
}

/* Sidebar Cards Padding */
.col-lg-4 .card-body {
    padding: 1.25rem;
}
</style>
@endsection