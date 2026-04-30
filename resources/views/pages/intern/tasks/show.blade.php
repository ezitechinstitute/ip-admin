@extends('layouts/layoutMaster')

@section('title', 'Task Details')

@section('page-style')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .glass-card {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.5);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }

    .info-box {
        background: rgba(255,255,255,0.6);
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .info-box:hover {
        background: rgba(255,255,255,0.9);
        transform: translateX(5px);
    }

    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .badge-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .badge-submitted { background: rgba(59,130,246,0.15); color: #3b82f6; border: 1px solid rgba(59,130,246,0.3); }
    .badge-approved { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-rejected { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

    .btn-custom {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
    }

    .form-control-custom {
        border-radius: 50px;
        border: 1px solid rgba(0,0,0,0.1);
        padding: 0.6rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @media (max-width: 768px) {
        .glass-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Alert Messages --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
            <div>
                <strong class="d-block">⚠️ Portal Frozen</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
            <div>
                <strong class="d-block">Success!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">
        {{-- Main Content --}}
        <div class="col-lg-8">
            {{-- Task Details Card --}}
            <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.1s;">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-file-text-fill text-primary fs-3"></i>
                            <h4 class="fw-bold mb-0">{{ $task->task_title }}</h4>
                        </div>
                        @php
                            $statusMap = [
                                'pending' => 'Pending', 'Assigned' => 'Pending',
                                'submitted' => 'Submitted',
                                'approved' => 'Approved', 'Completed' => 'Approved',
                                'rejected' => 'Rejected', 'Rejected' => 'Rejected',
                            ];
                            $statusKey = strtolower($statusMap[$task->task_status] ?? 'Pending');
                        @endphp
                        <span class="badge-status badge-{{ $statusKey }}">
                            <i class="bi bi-{{ $statusKey == 'pending' ? 'hourglass-split' : ($statusKey == 'submitted' ? 'upload' : ($statusKey == 'approved' ? 'check-circle' : 'x-circle')) }} me-1"></i>
                            {{ ucfirst($statusKey) }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold mb-2"><i class="bi bi-card-text me-2 text-primary"></i>Description</h6>
                    <p class="text-muted mb-0">{{ $task->task_description ?? 'No description provided' }}</p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-calendar-event text-primary fs-3"></i>
                                <div>
                                    <small class="text-muted d-block">Deadline</small>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($task->task_end)->format('d M, Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($task->task_end)->diffForHumans() }}</small>
                                    @if(\Carbon\Carbon::parse($task->task_end)->isPast() && !in_array($task->task_status, ['Completed', 'approved']))
                                    <div class="mt-1"><span class="badge bg-danger bg-opacity-10 text-danger">Overdue</span></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <div class="d-flex align-items-center gap-3">
                                <i class="bi bi-star text-warning fs-3"></i>
                                <div>
                                    <small class="text-muted d-block">Points / Score</small>
                                    <div class="fw-semibold">{{ $task->task_points ?? 'Not specified' }}</div>
                                    @isset($task->grade)
                                    <small class="text-success">Grade: {{ $task->grade }}%</small>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Feedback Card --}}
            @if(in_array($task->task_status, ['approved', 'Completed', 'rejected', 'Rejected']) && ($task->grade || $task->review))
            <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-chat-dots-fill text-primary fs-3"></i>
                    <h5 class="fw-bold mb-0">Supervisor Feedback</h5>
                </div>
                
                @if($task->grade)
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="info-box flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-muted">Your Score</span>
                                <span class="fw-bold fs-2 text-primary">{{ $task->grade }}%</span>
                            </div>
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $task->grade }}%; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($task->review)
                <div class="info-box">
                    <div class="d-flex gap-3">
                        <i class="bi bi-quote text-muted fs-4"></i>
                        <div>
                            <small class="text-muted d-block">Remarks</small>
                            <p class="mb-0">{{ $task->review }}</p>
                        </div>
                    </div>
                </div>
                @endif

                @if(in_array($task->task_status, ['rejected', 'Rejected']))
                <div class="alert alert-warning mt-3 rounded-4">
                    <i class="bi bi-arrow-repeat me-2"></i>
                    Task needs revision. Please update your submission.
                </div>
                @endif
            </div>
            @endif

            {{-- Submission Card --}}
            @if(isset($task->task_git_url) && $task->task_git_url || isset($task->task_live_url) && $task->task_live_url || isset($task->task_screenshot) && $task->task_screenshot || isset($task->submit_description) && $task->submit_description)
            <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.3s;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-upload text-primary fs-3"></i>
                    <h5 class="fw-bold mb-0">Your Submission</h5>
                </div>

                <div class="row g-3">
                    @if($task->task_git_url)
                    <div class="col-md-6">
                        <div class="info-box">
                            <small class="text-muted d-block">GitHub Repository</small>
                            <a href="{{ $task->task_git_url }}" target="_blank" class="text-primary fw-medium text-decoration-none">
                                <i class="bi bi-github me-1"></i> View Repository
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($task->task_live_url)
                    <div class="col-md-6">
                        <div class="info-box">
                            <small class="text-muted d-block">Live Project</small>
                            <a href="{{ $task->task_live_url }}" target="_blank" class="text-primary fw-medium text-decoration-none">
                                <i class="bi bi-globe me-1"></i> View Live Site
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($task->task_screenshot)
                    <div class="col-12">
                        <div class="info-box">
                            <small class="text-muted d-block">Screenshot</small>
                            <a href="{{ asset($task->task_screenshot) }}" target="_blank" class="btn btn-outline-primary btn-custom mt-1">
                                <i class="bi bi-image me-1"></i> View Screenshot
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($task->submit_description)
                    <div class="col-12">
                        <div class="info-box">
                            <small class="text-muted d-block">Submission Notes</small>
                            <p class="mb-0">{{ $task->submit_description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar - Submission Form --}}
        <div class="col-lg-4">
            @if($canResubmit)
            <div class="glass-card p-4 animate-card sticky-top" style="top: 20px; animation-delay: 0.4s;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-send-fill text-primary fs-3"></i>
                    <h5 class="fw-bold mb-0">Submit Task</h5>
                </div>

                <form action="{{ route('intern.portal.tasks.submit', $task->task_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">GitHub URL</label>
                        <input type="url" name="task_git_url" class="form-control form-control-custom"
                               placeholder="https://github.com/username/repo"
                               value="{{ old('task_git_url', $task->task_git_url) }}">
                        <small class="text-muted">Optional</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Live URL</label>
                        <input type="url" name="task_live_url" class="form-control form-control-custom"
                               placeholder="https://your-project.com"
                               value="{{ old('task_live_url', $task->task_live_url) }}">
                        <small class="text-muted">Optional</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Screenshot</label>
                        <input type="file" name="task_screenshot" class="form-control form-control-custom" accept="image/*">
                        <small class="text-muted">Max 2MB. JPG, PNG, GIF</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Submission Notes</label>
                        <textarea name="submit_description" class="form-control" rows="4"
                                  style="border-radius: 1rem; border: 1px solid rgba(0,0,0,0.1);"
                                  placeholder="Any additional notes for the supervisor...">{{ old('submit_description', $task->submit_description) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-custom">
                        <i class="bi bi-cloud-upload me-1"></i> Submit Task
                    </button>
                </form>
            </div>
            @elseif($isApproved)
            <div class="glass-card p-4 text-center animate-card" style="animation-delay: 0.4s;">
                <i class="bi bi-check-circle-fill text-success fs-1 mb-3"></i>
                <h5 class="fw-bold">Task Completed!</h5>
                <p class="text-muted mb-0">This task has been approved. Great work!</p>
                <div class="mt-3">
                    <i class="bi bi-trophy-fill text-warning fs-4"></i>
                    <span class="fw-semibold text-success">+{{ $task->grade ?? $task->task_points ?? 0 }} points earned</span>
                </div>
            </div>
            @elseif($isSubmitted)
            <div class="glass-card p-4 text-center animate-card" style="animation-delay: 0.4s;">
                <i class="bi bi-clock-history text-warning fs-1 mb-3"></i>
                <h5 class="fw-bold">Awaiting Review</h5>
                <p class="text-muted mb-0">Your submission is pending supervisor review.</p>
                <div class="mt-3">
                    <div class="spinner-border text-warning" style="width: 1.5rem; height: 1.5rem;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-4 animate-card" style="animation-delay: 0.5s;">
        <a href="{{ url('/intern/tasks') }}" class="btn btn-secondary btn-custom">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>
    </div>
</div>
@endsection