@extends('layouts/layoutMaster')

@section('title', 'Project Details')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Glass Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    /* Info Box */
    .info-box {
        background: rgba(255, 255, 255, 0.6);
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .info-box:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(5px);
        border-color: #3b82f6;
    }

    /* Badges */
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-ongoing { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .badge-submitted { background: rgba(139,92,246,0.15); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.3); }
    .badge-approved { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-rejected { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

    /* Task Table */
    .modern-table {
        width: 100%;
        margin-bottom: 0;
    }

    .modern-table thead th {
        background: rgba(255, 255, 255, 0.9);
        padding: 1rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .modern-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .modern-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.5);
        transform: scale(1.01);
    }

    .modern-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Buttons */
    .btn-custom {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
    }

    /* Animations */
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
        .info-box {
            margin-bottom: 0.5rem;
        }
        .modern-table thead th,
        .modern-table tbody td {
            padding: 0.5rem;
            font-size: 0.7rem;
        }
        .btn-custom {
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Back Button --}}
    <div class="mb-4 animate-card" style="animation-delay: 0.1s;">
        <a href="{{ route('intern.projects') }}" class="btn btn-secondary btn-custom">
            <i class="bi bi-arrow-left me-1"></i> Back to Projects
        </a>
    </div>

    {{-- Project Overview Card --}}
    <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.2s;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-folder-fill text-primary fs-3"></i>
                    <h3 class="fw-bold mb-0">{{ $project->title }}</h3>
                </div>
                @php
                    $statusClass = match(strtolower($project->pstatus)) {
                        'ongoing' => 'ongoing',
                        'submitted' => 'submitted',
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                        default => 'secondary'
                    };
                    $statusIcon = match(strtolower($project->pstatus)) {
                        'ongoing' => 'hourglass-split',
                        'submitted' => 'upload',
                        'approved' => 'check-circle',
                        'rejected' => 'x-circle',
                        default => 'circle'
                    };
                @endphp
                <span class="badge-custom badge-{{ $statusClass }}">
                    <i class="bi bi-{{ $statusIcon }} me-1"></i>
                    {{ ucfirst($project->pstatus) }}
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Technology -->
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-code-slash text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">Technology Stack</small>
                            <span class="fw-semibold">{{ $project->tech_stack ?? 'Not specified' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supervisor -->
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-person-circle text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">Supervisor</small>
                            <span class="fw-semibold">{{ $supervisorName }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Start Date -->
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-check text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">Start Date</small>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($project->start_date)->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- End Date -->
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-x text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">End Date</small>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($project->end_date)->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Duration Progress -->
            @php
                $startDate = \Carbon\Carbon::parse($project->start_date);
                $endDate = \Carbon\Carbon::parse($project->end_date);
                $totalDays = $startDate->diffInDays($endDate);
                $elapsedDays = min($totalDays, max(0, $startDate->diffInDays(now())));
                $durationProgress = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100) : 0;
            @endphp
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-clock-history text-primary fs-3"></i>
                            <div class="flex-grow-1">
                                <small class="text-muted d-block">Duration Progress</small>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $durationProgress }}%; border-radius: 10px;"></div>
                                </div>
                                <small class="text-muted">{{ $durationProgress }}% completed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status -->
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-pie-chart text-primary fs-3"></i>
                        <div>
                            <small class="text-muted d-block">Project Status</small>
                            <span class="badge-custom badge-{{ $statusClass }} mt-1">
                                <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                {{ ucfirst($project->pstatus) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="col-12">
                <div class="info-box">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-card-text text-primary fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Project Description</small>
                            <p class="mb-0">{{ $project->description ?? 'No description provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Instructions - FIXED: Check if property exists -->
            @if(isset($project->instructions) && $project->instructions)
            <div class="col-12">
                <div class="info-box">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle text-primary fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Instructions</small>
                            <p class="mb-0">{{ $project->instructions }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Project Tasks Section --}}
    @if($tasks->count() > 0)
    <div class="glass-card p-4 animate-card" style="animation-delay: 0.3s;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-list-check text-primary fs-3"></i>
            <h5 class="fw-bold mb-0">Project Tasks ({{ $tasks->count() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th><i class="bi bi-file-text me-1"></i> Task Title</th>
                        <th><i class="bi bi-calendar-event me-1"></i> Deadline</th>
                        <th><i class="bi bi-pie-chart me-1"></i> Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    @php
                        $taskStatusClass = $task->task_status == 'approved' ? 'success' : 'warning';
                        $taskStatusIcon = $task->task_status == 'approved' ? 'check-circle' : 'hourglass-split';
                        $deadline = \Carbon\Carbon::parse($task->task_end);
                        $isOverdue = $deadline->isPast() && $task->task_status != 'approved';
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $task->task_title }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span>{{ $deadline->format('d M, Y') }}</span>
                                @if($isOverdue)
                                <small class="text-danger">Overdue</small>
                                @endif
                            </div>
</td>
                        <td>
                            <span class="badge-custom badge-{{ $taskStatusClass }}">
                                <i class="bi bi-{{ $taskStatusIcon }} me-1"></i>
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

    {{-- No Tasks Message --}}
    @if($tasks->count() == 0)
    <div class="glass-card p-4 text-center animate-card" style="animation-delay: 0.3s;">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <p class="mt-2 text-muted mb-0">No tasks assigned for this project yet</p>
    </div>
    @endif

</div>
@endsection