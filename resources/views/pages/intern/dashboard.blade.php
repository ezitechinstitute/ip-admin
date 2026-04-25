@extends('layouts/layoutMaster')

@section('title', 'Intern Dashboard')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.css" rel="stylesheet">
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

    .glass-card {
        background: var(--glass-bg) !important;
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--card-radius);
        box-shadow: var(--glass-shadow);
        transition: var(--transition-smooth);
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .dashboard-card {
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(8px);
        border-radius: var(--card-radius);
        border: 1px solid rgba(255,255,255,0.6);
        transition: var(--transition-smooth);
        height: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        background: rgba(255,255,255,0.9);
        box-shadow: 0 15px 35px rgba(0,0,0,0.05);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #3b82f6, #1e40af);
    }

    /* Alert Auto-hide - Keeps space in layout */
    .alert-auto-hide {
        transition: all 0.5s ease-in-out;
    }
    .alert-auto-hide.hide-alert {
        opacity: 0;
        visibility: hidden;
        margin: 0 !important;
        padding: 0 !important;
        height: 0;
        min-height: 0;
        overflow: hidden;
    }

    /* Priority Ring Container */
    .priority-ring-container {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
    }
    .priority-ring-svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }
    .priority-ring-bg {
        stroke: rgba(0,0,0,0.06);
        stroke-width: 8;
        fill: none;
        stroke-linecap: round;
    }
    .priority-ring-progress {
        stroke-width: 8;
        fill: none;
        stroke-linecap: round;
        transition: stroke-dashoffset 1.2s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        filter: drop-shadow(0 0 4px rgba(0,0,0,0.1));
    }
    .priority-ring-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }
    .priority-ring-percent {
        font-size: 1.5rem;
        font-weight: 800;
        color: #2c3e66;
    }
    .priority-ring-label {
        font-size: 0.6rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
    }

    /* Priority Activity Items */
    .priority-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        transition: var(--transition-smooth);
    }
    .priority-item:hover {
        transform: translateX(4px);
        background: rgba(0,0,0,0.02);
    }
    .priority-item-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        flex-shrink: 0;
    }
    .priority-item-count {
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .priority-item-label {
        font-size: 0.65rem;
        font-weight: 500;
        color: #6c86a3;
    }
    .priority-progress {
        width: 60px;
        height: 4px;
        background: rgba(0,0,0,0.1);
        border-radius: 4px;
        overflow: hidden;
    }
    .priority-progress-bar {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }
    .priority-task-title {
        font-size: 0.7rem;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }

    /* Stat Rings */
    .stat-ring {
        transform: rotate(-90deg);
    }
    .stat-ring-progress {
        transition: stroke-dashoffset 1s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }
    .stat-ring-center {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
    }

    .stat-card {
        text-align: center;
        padding: 1rem 0.5rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .stat-trend-up { color: #10b981; }
    .stat-trend-down { color: #ef4444; }

    /* Task Table */
    .task-table {
        width: 100%;
        margin-bottom: 0;
    }
    .task-table th {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c86a3;
        padding: 1rem 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .task-table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .task-table tbody tr:last-child td {
        border-bottom: none;
    }

    .priority-badge, .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 40px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* Notifications */
    .notifications-container {
        max-height: 380px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .notifications-container::-webkit-scrollbar {
        width: 4px;
    }
    .notifications-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .notifications-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    .notification-item {
        transition: var(--transition-smooth);
        border-radius: 0.75rem;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border: 1px solid transparent;
    }
    .notification-item.unread {
        background: rgba(59,130,246,0.08);
        border-left: 3px solid #3b82f6;
    }
    .notification-item:hover {
        background: rgba(0,0,0,0.02);
        transform: translateX(2px);
    }

    /* Timeline */
    .timeline-vertical {
        position: relative;
        padding-left: 2rem;
    }
    .timeline-vertical::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0.5rem;
        bottom: 0.5rem;
        width: 2px;
        background: linear-gradient(to bottom, #e2e8f0, #cbd5e1);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-icon {
        position: absolute;
        left: -2rem;
        top: 0;
        width: 2rem;
        height: 2rem;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .badge.bg-label-primary {
        background-color: rgba(59,130,246,0.12);
        color: #3b82f6;
    }
    .badge.bg-label-success {
        background-color: rgba(16,185,129,0.12);
        color: #10b981;
    }
    .badge.bg-label-warning {
        background-color: rgba(245,158,11,0.12);
        color: #f59e0b;
    }
    .badge.bg-label-danger {
        background-color: rgba(239,68,68,0.12);
        color: #ef4444;
    }

    @media (max-width: 768px) {
        .priority-ring-container { width: 100px; height: 100px; }
        .priority-ring-percent { font-size: 1rem; }
        .priority-item { flex-direction: column; text-align: center; }
        .priority-item-icon { margin: 0 auto; }
        .priority-progress { width: 100%; margin-top: 8px; }
        .task-table th, .task-table td { padding: 0.5rem; font-size: 0.7rem; }
    }
</style>
@endsection

@section('content')

@php
$allowedColors = ['primary', 'success', 'warning', 'danger', 'info', 'secondary'];
@endphp

<div id="dashboard-data"
     data-progress-percent="{{ $dashboard->progressPercent ?? 0 }}"
     data-task-percent="{{ $dashboard->taskProgressPercent ?? 0 }}"
     data-project-percent="{{ $dashboard->projectProgressPercent ?? 0 }}"
     data-completion-rate="{{ $dashboard->completionRate ?? 0 }}"
     data-avg-score="{{ $dashboard->averageScore ?? 0 }}"
     data-project-completion="{{ $dashboard->projectCompletionRate ?? 0 }}"
     data-chart-task='@json($dashboard->chartTaskCompletion ?? [])'
     data-chart-labels='@json($dashboard->chartWeekLabels ?? [])'
     data-chart-performance='@json($dashboard->chartPerformanceTrend ?? [])'
></div>

{{-- Freeze Warning Banner with Auto-hide - Keeps layout space --}}
<div id="freezeAlertWrapper">
    @if($dashboard->freezeWarning ?? false)
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center glass-card mb-4 border-0 alert-auto-hide" role="alert" id="freezeAlert">
        <i class="bi bi-lock-fill me-2 fs-5"></i>
        <span>{{ $dashboard->freezeWarning }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>

{{-- ======================= HERO SECTION ======================= --}}
<div class="row g-6 mb-6">

{{-- COLUMN 1: Welcome Card (Only removed top "Needs attention") --}}
<div class="col-xl-5">
    <div class="card h-100 overflow-hidden" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: none;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div style="position: relative; z-index: 1; flex: 1;">
                    @php
                        $userName = $dashboard->internName ?? 'Intern';
                        $userInitials = strtoupper(substr($userName, 0, 2));
                        $avatarColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                        $avatarColor = $avatarColors[abs(crc32($userName)) % count($avatarColors)];
                    @endphp
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width: 52px; height: 52px; background: {{ $avatarColor }}; color: white; font-weight: 600; font-size: 1.2rem;">
                            {{ $userInitials }}
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b;">Welcome Back, {{ $userName }}! </h5>
                            <p class="mb-0 text-muted small">Your internship journey at ZITCH Learning</p>
                        </div>
                    </div>
                    
                    {{-- Motivational Quote - Different message (not "Needs attention") --}}
<div class="mb-3 p-2 rounded-3" style="background: rgba(43,154,130,0.08); border-left: 3px solid #2b9a82;">
    <p class="mb-0 small fw-semibold" style="color: #2b9a82;">
        <i class="bi bi-quote me-1"></i> 
        @php
            $progress = $dashboard->progressPercent ?? 0;
            if ($progress >= 75):
                echo "🏆 Excellent work! You're in the final stretch!";
            elseif ($progress >= 50):
                echo "📈 Great progress! You're halfway there! Keep going!";
            elseif ($progress >= 25):
                echo "💪 Good momentum! Every task completed builds your future!";
            else:
                echo "🚀 Ready to begin? Start your first task today!";
            endif;
        @endphp
    </p>
</div>
                    
                    {{-- Side by Side: Remaining Days + Status --}}
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3" style="background: #f1f5f9;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted small mb-0">Remaining Days</p>
                                        <h5 class="mb-0 fw-bold text-warning">{{ number_format($dashboard->remainingDays ?? 0, 0) }} <small class="fs-6">days</small></h5>
                                    </div>
                                    <i class="bi bi-calendar-check text-warning fs-4 opacity-50"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
    <div class="p-2 rounded-3 h-100 d-flex align-items-center justify-content-between" style="background: #fff3e0; border: 1px solid #ffe0b2;">
        <div>
            <p class="text-muted small mb-0">Status</p>
            <p class="mb-0 fw-semibold text-warning small">Needs attention</p>
        </div>
        <i class="bi bi-exclamation-triangle-fill text-warning fs-4 opacity-50"></i>
    </div>
</div>
                    </div>
                    
                    <div class="d-flex gap-2 mb-3">
                        @php
                            $statusColor = match($dashboard->internshipStatus ?? 'Active') {
                                'Frozen' => 'danger',
                                'Active' => 'success',
                                'Completed' => 'secondary',
                                default => 'primary'
                            };
                        @endphp
                        <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} rounded-pill px-3 py-2">
                            {{ $dashboard->internshipStatus ?? 'Active' }}
                        </span>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                            <i class="bi bi-star-fill me-1 small"></i> Intern
                        </span>
                    </div>
                    
                    <a href="{{ url('/intern/tasks') }}" class="btn btn-sm w-100 rounded-pill" style="background: #2b9a82; color: white; border: none; padding: 8px 16px;">
                        <i class="bi bi-arrow-right me-1"></i> Continue Learning
                    </a>
                </div>
                
                <div style="flex-shrink: 0; position: relative; z-index: 1;">
                    <svg width="80" height="80" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="50" cy="50" r="48" fill="rgba(43,154,130,0.08)" stroke="rgba(43,154,130,0.15)" stroke-width="1.5"/>
                        <path d="M50 30 L65 45 L50 60 L35 45 L50 30Z" fill="#2b9a82" fill-opacity="0.9" stroke="#2b9a82" stroke-width="1.5"/>
                        <circle cx="50" cy="45" r="3" fill="white"/>
                        <path d="M50 60 L50 75" stroke="#2b9a82" stroke-width="2" stroke-linecap="round"/>
                        <path d="M42 68 L50 75 L58 68" stroke="#2b9a82" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="50" cy="80" r="4" fill="#f59e0b"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Priority Activity Card (Column 2) - --}}
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Priority Activity</h5>
                @php $totalPending = ($dashboard->overdueTasksCount ?? 0) + ($dashboard->deadlineSoonCount ?? 0) + ($dashboard->pendingSubmissionsCount ?? 0); @endphp
                @if($totalPending > 0)
                <span class="badge bg-danger rounded-pill">{{ $totalPending }} pending</span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-5 text-center">
                        <div class="priority-ring-container">
                            <svg class="priority-ring-svg" viewBox="0 0 100 100">
                                <defs>
                                    <linearGradient id="ringBlue" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#3b82f6" /><stop offset="100%" stop-color="#1e40af" />
                                    </linearGradient>
                                    <linearGradient id="ringGreen" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#10b981" /><stop offset="100%" stop-color="#047857" />
                                    </linearGradient>
                                    <linearGradient id="ringOrange" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#f59e0b" /><stop offset="100%" stop-color="#b45309" />
                                    </linearGradient>
                                </defs>
                                <circle cx="50" cy="50" r="42" class="priority-ring-bg" />
                                <circle cx="50" cy="50" r="42" class="priority-ring-progress" stroke="url(#ringBlue)" 
                                        stroke-dasharray="263.89" stroke-dashoffset="263.89" id="priorityOuterRing" />
                                <circle cx="50" cy="50" r="34" class="priority-ring-bg" />
                                <circle cx="50" cy="50" r="34" class="priority-ring-progress" stroke="url(#ringGreen)" 
                                        stroke-dasharray="213.63" stroke-dashoffset="213.63" id="priorityMidRing" />
                                <circle cx="50" cy="50" r="26" class="priority-ring-bg" />
                                <circle cx="50" cy="50" r="26" class="priority-ring-progress" stroke="url(#ringOrange)" 
                                        stroke-dasharray="163.36" stroke-dashoffset="163.36" id="priorityInnerRing" />
                            </svg>
                            <div class="priority-ring-center">
                                <div class="priority-ring-percent" id="priorityRingPercent">{{ $dashboard->progressPercent ?? 0 }}%</div>
                                <div class="priority-ring-label">Completion</div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex justify-content-center gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-1">
                                <div class="rounded-circle" style="width: 8px; height: 8px; background: linear-gradient(135deg, #3b82f6, #1e40af);"></div>
                                <small class="text-muted">Internship</small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="rounded-circle" style="width: 8px; height: 8px; background: linear-gradient(135deg, #10b981, #047857);"></div>
                                <small class="text-muted">Tasks</small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <div class="rounded-circle" style="width: 8px; height: 8px; background: linear-gradient(135deg, #f59e0b, #b45309);"></div>
                                <small class="text-muted">Projects</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        @php
                            $hasOverdue = ($dashboard->overdueTasksCount ?? 0) > 0;
                            $hasDeadlineSoon = ($dashboard->deadlineSoonCount ?? 0) > 0;
                            $hasPending = ($dashboard->pendingSubmissionsCount ?? 0) > 0;
                        @endphp
                        
                        @if($hasOverdue || $hasDeadlineSoon || $hasPending)
                        <div class="d-flex flex-column gap-2">
                         {{-- Overdue Tasks --}}
@if($hasOverdue)
<div class="dashboard-card p-3 mb-3" style="border-left: 4px solid #ef4444;">
    <div class="d-flex align-items-start gap-3">
        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
             style="width: 48px; height: 48px; background: rgba(239,68,68,0.1);">
            <i class="bi bi-clock-history text-danger fs-4"></i>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="text-muted small">Priority</div>
                    <div class="fw-bold fs-5 text-danger">{{ $dashboard->overdueTasksCount ?? 0 }} Overdue Tasks</div>
                </div>
                <span class="badge bg-danger rounded-pill px-3 py-2">Urgent</span>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @foreach(($dashboard->overdueTaskTitles ?? []) as $index => $title)
                    @if($index < 2)
                    <div class="d-flex align-items-center gap-2 bg-light rounded-pill px-3 py-1">
                        <i class="bi bi-file-text text-danger small"></i>
                        <span class="small">{{ \Illuminate\Support\Str::limit($title, 20) }}</span>
                    </div>
                    @endif
                @endforeach
                @if($dashboard->overdueTasksCount > 2)
                <a href="{{ url('/intern/tasks?filter=overdue') }}" class="small text-danger text-decoration-none d-flex align-items-center gap-1">
                    +{{ $dashboard->overdueTasksCount - 2 }} more <i class="bi bi-arrow-right"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

                            @if($hasDeadlineSoon)
                            <div class="priority-item bg-warning bg-opacity-10 rounded-3">
                                <div class="priority-item-icon bg-warning bg-opacity-20">
                                    <i class="bi bi-calendar-exclamation text-warning fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <div class="priority-item-count text-warning">{{ $dashboard->deadlineSoonCount ?? 0 }}</div>
                                            <div class="priority-item-label">Due Soon</div>
                                        </div>
                                        <div class="priority-progress">
                                            <div class="priority-progress-bar bg-warning" style="width: {{ min(100, ($dashboard->deadlineSoonCount ?? 0) * 20) }}%"></div>
                                        </div>
                                        @if(($dashboard->deadlineSoonTitles ?? collect())->count() > 0)
                                        <div class="priority-task-title text-muted">
                                            <i class="bi bi-file-text me-1 small"></i>{{ $dashboard->deadlineSoonTitles->first() }}
                                            @if($dashboard->deadlineSoonCount > 1)
                                            <span class="badge bg-warning bg-opacity-15 text-warning ms-1">+{{ $dashboard->deadlineSoonCount - 1 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($hasPending)
                            <div class="priority-item bg-primary bg-opacity-10 rounded-3">
                                <div class="priority-item-icon bg-primary bg-opacity-20">
                                    <i class="bi bi-send text-primary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                                        <div>
                                            <div class="priority-item-count text-primary">{{ $dashboard->pendingSubmissionsCount ?? 0 }}</div>
                                            <div class="priority-item-label">Pending Review</div>
                                        </div>
                                        <div class="priority-progress">
                                            <div class="priority-progress-bar bg-primary" style="width: {{ min(100, ($dashboard->pendingSubmissionsCount ?? 0) * 20) }}%"></div>
                                        </div>
                                        @if(($dashboard->pendingSubmissionTitles ?? collect())->count() > 0)
                                        <div class="priority-task-title text-muted">
                                            <i class="bi bi-file-text me-1 small"></i>{{ $dashboard->pendingSubmissionTitles->first() }}
                                            @if($dashboard->pendingSubmissionsCount > 1)
                                            <span class="badge bg-primary bg-opacity-15 text-primary ms-1">+{{ $dashboard->pendingSubmissionsCount - 1 }}</span>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            <p class="mt-2 mb-0 text-muted">All caught up! No pending items.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================= 3 STAT CARDS ======================= --}}
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="dashboard-card p-3 text-center stat-card">
            <div class="position-relative d-inline-block mb-2">
                <svg width="80" height="80" viewBox="0 0 100 100" class="stat-ring">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="6" />
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#3b82f6" stroke-width="6"
                            stroke-dasharray="263.89" stroke-dashoffset="263.89"
                            stroke-linecap="round"
                            class="stat-ring-progress" data-percent="{{ $dashboard->completionRate ?? 0 }}" />
                </svg>
                <div class="stat-ring-center">{{ $dashboard->tasksCompleted ?? 0 }}/{{ $dashboard->totalTasks ?? 0 }}</div>
            </div>
            <div class="stat-number text-primary mt-2">{{ $dashboard->completionRate ?? 0 }}%</div>
            <div class="text-muted small fw-semibold">Tasks Completed</div>
            <div class="mt-2"><span class="stat-trend-up"><i class="bi bi-arrow-up-short"></i> +{{ $dashboard->completionRate ?? 0 }}%</span></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dashboard-card p-3 text-center stat-card">
            <div class="position-relative d-inline-block mb-2">
                <svg width="80" height="80" viewBox="0 0 100 100" class="stat-ring">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="6" />
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#10b981" stroke-width="6"
                            stroke-dasharray="263.89" stroke-dashoffset="263.89"
                            stroke-linecap="round"
                            class="stat-ring-progress" data-percent="{{ $dashboard->averageScore ?? 0 }}" />
                </svg>
                <div class="stat-ring-center">{{ $dashboard->averageScore ?? 0 }}%</div>
            </div>
            <div class="stat-number text-success">{{ $dashboard->averageScore ?? 0 }}%</div>
            <div class="text-muted small fw-semibold">Avg. Score</div>
            <div class="mt-2"><span class="stat-trend-up"><i class="bi bi-arrow-up-short"></i> +5%</span></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="dashboard-card p-3 text-center stat-card">
            <div class="position-relative d-inline-block mb-2">
                <svg width="80" height="80" viewBox="0 0 100 100" class="stat-ring">
                    <circle cx="50" cy="50" r="42" fill="none" stroke="rgba(0,0,0,0.05)" stroke-width="6" />
                    <circle cx="50" cy="50" r="42" fill="none" stroke="#f59e0b" stroke-width="6"
                            stroke-dasharray="263.89" stroke-dashoffset="263.89"
                            stroke-linecap="round"
                            class="stat-ring-progress" data-percent="{{ $dashboard->projectCompletionRate ?? 0 }}" />
                </svg>
                <div class="stat-ring-center">{{ $dashboard->activeProjects ?? 0 }}/{{ $dashboard->totalProjects ?? 0 }}</div>
            </div>
            <div class="stat-number text-warning">{{ $dashboard->activeProjects ?? 0 }}/{{ $dashboard->totalProjects ?? 0 }}</div>
            <div class="text-muted small fw-semibold">Active Projects</div>
            <div class="mt-2"><span class="stat-trend-up"><i class="bi bi-arrow-up-short"></i> +12%</span></div>
        </div>
    </div>
</div>

{{-- ======================= CHARTS ======================= --}}
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <div class="dashboard-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-week me-2"></i> Task Completion (Last 7 days)</h6>
                <span class="badge bg-success bg-opacity-10 text-success">+23% vs last week</span>
            </div>
            <div id="taskTrendChart" style="height: 280px;"></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dashboard-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-lightning-charge me-2"></i> Performance Trend</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">Consistent</span>
            </div>
            <div id="performanceTrendChart" style="height: 280px;"></div>
        </div>
    </div>
</div>

{{-- ======================= RECENT TASKS + NOTIFICATIONS ======================= --}}
<div class="row g-4 mb-5">
    {{-- Recent Tasks Table --}}
    <div class="col-lg-7">
        <div class="dashboard-card p-0 overflow-hidden h-100 d-flex flex-column">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-transparent">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-task me-2"></i> Recent Tasks</h6>
                <a href="{{ url('/intern/tasks') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View all <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table task-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Task</th>
                            <th>Deadline</th>
                            <th>Priority</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($dashboard->recentTasks ?? []) as $task)
                        @php
                            $priorityColor = in_array($task->priority_color ?? 'secondary', $allowedColors) ? $task->priority_color : 'secondary';
                            $statusColor = in_array($task->status_color ?? 'secondary', $allowedColors) ? $task->status_color : 'secondary';
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-text text-secondary"></i>
                                    <span class="fw-medium">{{ $task->task_title ?? '' }}</span>
                                </div>
                            </td>
                            <td><span class="small"><i class="bi bi-calendar3 me-1"></i> {{ $task->formatted_deadline ?? '-' }}</span></td>
                            <td>
                                <span class="priority-badge bg-{{ $priorityColor }} bg-opacity-10 text-{{ $priorityColor }}">
                                    <i class="bi bi-{{ $task->priority_icon ?? 'dash' }}-circle-fill me-1"></i>{{ $task->priority_label ?? 'Medium' }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }}">
                                    <i class="bi bi-{{ $task->status_icon ?? 'clock' }} me-1"></i>{{ $task->status_label ?? 'Pending' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>No tasks found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Notifications --}}
    <div class="col-lg-5">
        <div class="dashboard-card p-3 h-100 d-flex flex-column">
            <h6 class="fw-bold mb-3"><i class="bi bi-bell me-2"></i> Notifications</h6>
            <div class="notifications-container">
                @if(($dashboard->notifications ?? collect())->count())
                    @foreach($dashboard->notifications as $notif)
                    <div class="notification-item {{ $notif->is_read ?? false ? '' : 'unread' }}">
                        <div class="d-flex gap-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-envelope-paper text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $notif->title ?? 'Update' }}</div>
                                <div class="text-muted small">{{ $notif->message ?? 'New activity on your internship' }}</div>
                                <small class="text-muted">{{ $notif->time_ago ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash fs-1 text-muted"></i>
                    <p class="mt-2">No notifications yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================= TIMELINE (Milestone Cards Style) ======================= --}}
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                    <i class="bi bi-calendar-check text-primary fs-5"></i>
                </div>
                <h6 class="fw-bold mb-0">Internship Milestones</h6>
            </div>
            
            <div class="row g-4">
                @php
                    $timelineEvents = $dashboard->timeline ?? [];
                @endphp
                
                @foreach($timelineEvents as $event)
                @php
                    $eventStatus = $event->color ?? 'primary';
                    $progressPercent = match($eventStatus) {
                        'success' => 100,
                        'warning' => 50,
                        'danger' => 25,
                        default => 0
                    };
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="dashboard-card p-3 h-100">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 48px; height: 48px; background: {{ $event->bg_color ?? '#e3f2fd' }};">
                                <i class="bi {{ $event->icon ?? 'bi-flag' }} fs-4 text-primary"></i>
                            </div>
                            <span class="badge bg-{{ match($eventStatus) { 'success' => 'success', 'danger' => 'danger', 'warning' => 'warning', default => 'secondary' } }} bg-opacity-10 text-{{ match($eventStatus) { 'success' => 'success', 'danger' => 'danger', 'warning' => 'warning', default => 'secondary' } }} rounded-pill px-2 py-1">
                                {{ $event->date ?? '' }}
                            </span>
                        </div>
                        
                        <div class="fw-semibold fs-6 mb-1">{{ $event->title ?? '' }}</div>
                        <div class="small text-muted mb-3">{{ $event->description ?? '' }}</div>
                        
                        {{-- Progress Bar --}}
                        <div class="mt-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">Progress</small>
                                <small class="fw-semibold text-{{ match($eventStatus) { 'success' => 'success', 'danger' => 'danger', 'warning' => 'warning', default => 'secondary' } }}">{{ $progressPercent }}%</small>
                            </div>
                            <div class="progress rounded-pill" style="height: 6px; background: rgba(0,0,0,0.05);">
                                <div class="progress-bar bg-{{ match($eventStatus) { 'success' => 'success', 'danger' => 'danger', 'warning' => 'warning', default => 'secondary' } }} rounded-pill" style="width: {{ $progressPercent }}%;"></div>
                            </div>
                        </div>
                        
                        {{-- Status --}}
                        <div class="mt-3">
                            @if($eventStatus == 'success')
                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>
                            @elseif($eventStatus == 'danger')
                            <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-exclamation-circle-fill me-1"></i> Overdue</span>
                            @elseif($eventStatus == 'warning')
                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-fill me-1"></i> In Progress</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-hourglass-split me-1"></i> Upcoming</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if(count($timelineEvents) == 0)
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-calendar2-week fs-1 text-muted"></i>
                        <p class="mt-2 text-muted">No timeline events available</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataDiv = document.getElementById('dashboard-data');
        if (!dataDiv) return;

        const internshipPercent = parseFloat(dataDiv.dataset.progressPercent) || 0;
        const taskPercent = parseFloat(dataDiv.dataset.taskPercent) || 0;
        const projectPercent = parseFloat(dataDiv.dataset.projectPercent) || 0;

        let taskData = [], taskLabels = [], perfData = [];
        try {
            taskData = JSON.parse(dataDiv.dataset.chartTask || '[]');
            taskLabels = JSON.parse(dataDiv.dataset.chartLabels || '[]');
            perfData = JSON.parse(dataDiv.dataset.chartPerformance || '[]');
        } catch(e) { console.warn('Failed to parse chart data', e); }

        // Priority Card Rings
        const priorityOuterRing = document.getElementById('priorityOuterRing');
        const priorityMidRing = document.getElementById('priorityMidRing');
        const priorityInnerRing = document.getElementById('priorityInnerRing');
        const priorityRingPercent = document.getElementById('priorityRingPercent');

        const priCircOuter = 2 * Math.PI * 42;
        const priCircMid = 2 * Math.PI * 34;
        const priCircInner = 2 * Math.PI * 26;

        function setPriRingProgress(ring, percent, circumference) {
            if (!ring) return;
            const offset = circumference - (percent / 100) * circumference;
            ring.style.strokeDashoffset = circumference;
            void ring.offsetHeight;
            ring.style.strokeDashoffset = offset;
        }

        requestAnimationFrame(() => {
            setPriRingProgress(priorityOuterRing, internshipPercent, priCircOuter);
            setPriRingProgress(priorityMidRing, taskPercent, priCircMid);
            setPriRingProgress(priorityInnerRing, projectPercent, priCircInner);
        });

        if (priorityRingPercent) {
            let current = 0;
            const target = internshipPercent;
            const step = target / 50;
            const interval = setInterval(() => {
                current += step;
                if (current >= target) {
                    priorityRingPercent.innerText = Math.round(target) + '%';
                    clearInterval(interval);
                } else {
                    priorityRingPercent.innerText = Math.round(current) + '%';
                }
            }, 20);
        }

        // Stat Rings
        const statRings = document.querySelectorAll('.stat-ring-progress');
        const circumStat = 2 * Math.PI * 42;
        statRings.forEach(ring => {
            const percent = parseFloat(ring.getAttribute('data-percent')) || 0;
            const offset = circumStat - (percent / 100) * circumStat;
            ring.style.strokeDashoffset = circumStat;
            setTimeout(() => { ring.style.strokeDashoffset = offset; }, 100);
        });

        // Charts
        if (!taskData.length) {
            for (let i = 0; i < 7; i++) taskData.push(0);
        }
        if (!taskLabels.length) {
            for (let i = 6; i >= 0; i--) {
                let d = new Date();
                d.setDate(d.getDate() - i);
                taskLabels.push(d.toLocaleDateString('en-US', { weekday: 'short' }));
            }
        }

        new ApexCharts(document.querySelector("#taskTrendChart"), {
            series: [{ name: 'Completed Tasks', data: taskData, color: '#3b82f6' }],
            chart: { type: 'area', height: 280, toolbar: { show: false }, background: 'transparent', animations: { enabled: true } },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 0.2, opacityFrom: 0.5, opacityTo: 0.1 } },
            xaxis: { categories: taskLabels, labels: { style: { colors: '#6c86a3' } } },
            yaxis: { title: { text: 'Tasks' }, min: 0 },
            grid: { borderColor: '#eef2f5' }
        }).render();

        if (!perfData.length) perfData = [72, 68, 74, 79, 82, 85, 80];
        new ApexCharts(document.querySelector("#performanceTrendChart"), {
            series: [{ name: 'Avg. Score %', data: perfData, color: '#10b981' }],
            chart: { type: 'line', height: 280, toolbar: { show: false }, animations: { enabled: true } },
            stroke: { curve: 'smooth', width: 3 },
            markers: { size: 4 },
            xaxis: { categories: taskLabels.length ? taskLabels : ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Current'] },
            yaxis: { title: { text: 'Score (%)' }, min: 60, max: 100 },
            grid: { borderColor: '#eef2f5' },
            tooltip: { y: { formatter: (val) => val + '%' } }
        }).render();

        // Auto-hide alert - Keeps layout space by using visibility + height
        const alertElement = document.getElementById('freezeAlert');
        if (alertElement) {
            setTimeout(() => {
                alertElement.classList.add('hide-alert');
            }, 4500);
        }
    });
</script>
@endsection