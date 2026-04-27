@extends('layouts/layoutMaster')

@section('title', 'Task Dashboard')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        --primary-gradient: linear-gradient(135deg, #3b82f6, #1e40af);
        --success-gradient: linear-gradient(135deg, #10b981, #047857);
        --warning-gradient: linear-gradient(135deg, #f59e0b, #b45309);
        --danger-gradient: linear-gradient(135deg, #ef4444, #b91c1c);
        --info-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .premium-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .premium-card:hover::before {
        left: 100%;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    .stat-card-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .stat-card-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-gradient);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .stat-card-premium:hover::after {
        transform: scaleX(1);
    }

    .stat-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px -15px rgba(0, 0, 0, 0.2);
    }

    .stat-icon-premium {
        width: 60px;
        height: 60px;
        background: var(--stat-bg);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-value-premium {
        font-size: 2.2rem;
        font-weight: 800;
        background: var(--stat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    .kanban-column {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        padding: 1rem;
        min-height: 500px;
        transition: all 0.3s ease;
    }

    .kanban-column:hover {
        background: rgba(255, 255, 255, 0.7);
    }

    .kanban-header {
        padding: 0.75rem;
        border-radius: 0.75rem;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: 700;
    }

    .kanban-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
        cursor: grab;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .kanban-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        cursor: grab;
    }

    .view-toggle {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(8px);
        border-radius: 50px;
        padding: 0.25rem;
        display: inline-flex;
        gap: 0.25rem;
    }

    .view-btn {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: none;
        background: transparent;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .view-btn.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 4px 12px rgba(59,130,246,0.3);
    }

    .quick-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-stat-item {
        flex: 1;
        min-width: 100px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .quick-stat-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    .timeline-view {
        position: relative;
    }

    .timeline-year-header {
        margin: 1rem 0;
    }

    .timeline-year-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0,0,0,0.1), transparent);
    }

    .timeline-year-badge {
        background: rgba(59,130,246,0.1);
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .timeline-month-badge {
        position: absolute;
        left: -40px;
        top: 20px;
        background: white;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 0.7rem;
        font-weight: 600;
        color: #3b82f6;
        border: 1px solid rgba(59,130,246,0.2);
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        white-space: nowrap;
    }

    .timeline-item {
        position: relative;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        margin-bottom: 1rem;
        margin-left: 30px;
        transition: all 0.3s ease;
        border-left: 4px solid;
        cursor: pointer;
    }

    .timeline-item:hover {
        transform: translateX(8px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.1);
    }

    .timeline-badge {
        position: absolute;
        left: -12px;
        top: 20px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 3px solid;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .timeline-description {
        transition: all 0.3s ease;
    }

    #timelineContainer {
        scroll-behavior: smooth;
        max-height: 550px;
        overflow-y: auto;
        padding-right: 10px;
    }

    #timelineContainer::-webkit-scrollbar {
        width: 6px;
    }

    #timelineContainer::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }

    #timelineContainer::-webkit-scrollbar-thumb {
        background: rgba(59,130,246,0.3);
        border-radius: 10px;
    }

    #timelineContainer::-webkit-scrollbar-thumb:hover {
        background: rgba(59,130,246,0.5);
    }

    .scroll-top-btn {
        position: sticky;
        bottom: 20px;
        left: calc(100% - 60px);
        background: rgba(59,130,246,0.9);
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.3s ease;
        color: white;
        box-shadow: 0 4px 15px rgba(59,130,246,0.3);
        margin-top: 10px;
        float: right;
    }

    .scroll-top-btn:hover {
        opacity: 1;
        transform: translateY(-3px);
    }

    .alert-success-custom {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border: none;
        border-radius: 1rem;
    }

    .alert-danger-custom {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: none;
        border-radius: 1rem;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.6s ease-out forwards;
    }

    .table-danger {
        background: linear-gradient(90deg, rgba(239,68,68,0.08), transparent);
    }

    .task-row {
        transition: all 0.3s ease;
    }

    .task-row:hover {
        background: rgba(59,130,246,0.03);
    }

    .btn-group .btn {
        transition: all 0.2s ease;
    }

    .btn-group .btn:hover {
        transform: translateY(-2px);
    }

    #taskSearch {
        padding-right: 2rem;
    }

    .btn-loading {
        opacity: 0.7;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .kanban-column {
            margin-bottom: 1rem;
        }
        .quick-stats {
            flex-wrap: wrap;
        }
        .quick-stat-item {
            min-width: calc(50% - 0.5rem);
        }
        .timeline-month-badge {
            position: relative;
            left: 0;
            top: 0;
            margin-bottom: 8px;
            display: inline-block;
        }
        .timeline-item {
            margin-left: 0;
        }
        .view-toggle {
            width: 100%;
            justify-content: center;
        }
    }
  
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
    <div class="alert alert-success-custom alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
            <div>
                <strong class="d-block text-success">Success!</strong>
                <span class="text-success">{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger-custom alert-dismissible fade show mb-4 rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
            <div>
                <strong class="d-block text-danger">Error!</strong>
                <span class="text-danger">{{ session('error') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Welcome Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-rocket-takeoff-fill text-primary me-2"></i>Task Dashboard
            </h4>
            <p class="text-muted small mb-0">Track, manage, and ace your assignments</p>
        </div>
        <div class="d-flex gap-2">
            <div class="view-toggle">
                <button class="view-btn active" data-view="table">
                    <i class="bi bi-table me-1"></i> Table
                </button>
                <button class="view-btn" data-view="kanban">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Kanban
                </button>
                <button class="view-btn" data-view="timeline">
                    <i class="bi bi-clock-history me-1"></i> Timeline
                </button>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['icon'=>'bi-list-task','count'=>$stats['total'],'label'=>'Total Tasks','gradient'=>'linear-gradient(135deg, #3b82f6, #1e40af)','bg'=>'rgba(59,130,246,0.1)','trend'=>'+12%'],
                ['icon'=>'bi-hourglass-split','count'=>$stats['pending'],'label'=>'Pending','gradient'=>'linear-gradient(135deg, #f59e0b, #b45309)','bg'=>'rgba(245,158,11,0.1)','trend'=>'+5%'],
                ['icon'=>'bi-upload','count'=>$stats['submitted'],'label'=>'Submitted','gradient'=>'linear-gradient(135deg, #8b5cf6, #6d28d9)','bg'=>'rgba(139,92,246,0.1)','trend'=>'+8%'],
                ['icon'=>'bi-check-circle','count'=>$stats['approved'],'label'=>'Approved','gradient'=>'linear-gradient(135deg, #10b981, #047857)','bg'=>'rgba(16,185,129,0.1)','trend'=>'+15%'],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-3 col-6 animate-card" style="animation-delay: {{ 0.1 + ($loop->index * 0.05) }}s;">
            <div class="stat-card-premium" style="--stat-gradient: {{ $card['gradient'] }}; --stat-bg: {{ $card['bg'] }}">
                <div class="stat-icon-premium" style="background: {{ $card['bg'] }}">
                    <i class="bi {{ $card['icon'] }} fs-2" style="background: {{ $card['gradient'] }}; -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                <div class="stat-value-premium">{{ $card['count'] ?? 0 }}</div>
                <div class="stat-label-premium text-muted small mt-1">{{ $card['label'] }}</div>
                <div class="mt-2">
                    <small class="text-success"><i class="bi bi-arrow-up-short"></i>{{ $card['trend'] }} vs last month</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Quick Stats --}}
    <div class="quick-stats mb-4 animate-card" style="animation-delay: 0.3s;">
        <div class="quick-stat-item">
            <i class="bi bi-calendar-check text-primary fs-4"></i>
            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('F j, Y') }}</div>
            <small class="text-muted">Current Date</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-star-fill text-warning fs-4"></i>
            <div class="fw-bold">{{ $stats['approved'] ?? 0 }}/{{ $stats['total'] ?? 0 }}</div>
            <small class="text-muted">Completion Rate</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-trophy-fill text-success fs-4"></i>
            <div class="fw-bold">{{ $stats['approved'] ?? 0 }}</div>
            <small class="text-muted">Achievements</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-clock-fill text-info fs-4"></i>
            <div class="fw-bold">{{ $stats['pending'] ?? 0 }}</div>
            <small class="text-muted">In Progress</small>
        </div>
    </div>

    {{-- ==================== TABLE VIEW (uses $tasks - paginated) ==================== --}}
    <div id="tableView" class="view-content">
        <div class="premium-card">
            <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>All Tasks</h5>
                    <p class="text-muted small mb-0">Complete list of your assigned tasks</p>
                </div>
                <div class="input-group" style="width: 250px;">
                    <input type="text" id="taskSearch" class="form-control form-control-sm rounded-pill" placeholder="Search tasks...">
                    <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3"></i>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="taskTable">
                    <thead class="bg-light">
                        <tr>
                            <th><i class="bi bi-file-text me-1"></i> Task</th>
                            <th><i class="bi bi-calendar-event me-1"></i> Deadline</th>
                            <th><i class="bi bi-pie-chart me-1"></i> Status</th>
                            <th><i class="bi bi-star me-1"></i> Score</th>
<th class="text-end"><i class="bi bi-gear me-1"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                        @php
                            $deadline = \Carbon\Carbon::parse($task->task_end);
                            $isOverdue = $deadline->isPast() && !in_array($task->task_status, ['Completed', 'approved']);
                            $isUrgent = $deadline->diffInDays(now()) <= 2 && !$isOverdue && !in_array($task->task_status, ['Completed', 'approved']);
                            $daysLeft = max(0, $deadline->diffInDays(now()));
                            $progressWidth = match($task->task_status) {
                                'submitted' => 75,
                                'approved', 'Completed' => 100,
                                default => 25,
                            };
                            $statusColor = match($task->task_status) {
                                'pending', 'Assigned' => 'warning',
                                'submitted' => 'info',
                                'approved', 'Completed' => 'success',
                                'rejected', 'Rejected' => 'danger',
                                default => 'secondary'
                            };
                            $statusIcon = match($task->task_status) {
                                'pending', 'Assigned' => 'hourglass-split',
                                'submitted' => 'upload',
                                'approved', 'Completed' => 'check-circle',
                                'rejected', 'Rejected' => 'x-circle',
                                default => 'circle'
                            };
                            
                            $score = $task->grade ?? null;
                            if (!$score && isset($task->task_obt_points) && isset($task->task_points) && $task->task_points > 0) {
                                $score = round(($task->task_obt_points / $task->task_points) * 100);
                            }
                            if (!$score && isset($task->task_grade)) {
                                $score = $task->task_grade;
                            }
                            $hasScore = $score && $score > 0;
                        @endphp
                        <tr class="task-row {{ $isOverdue ? 'table-danger' : '' }}" data-title="{{ strtolower($task->task_title) }}" data-status="{{ $task->task_status }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                                        <i class="bi bi-file-text text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $task->task_title }}</div>
                                        <small class="text-muted">{{ \Illuminate\Support\Str::limit($task->task_description ?? 'No description', 40) }}</small>
                                        @if($isOverdue)
                                            <div><span class="badge bg-danger bg-opacity-10 text-danger mt-1"><i class="bi bi-alarm me-1"></i>Overdue</span></div>
                                        @elseif($isUrgent)
                                            <div><span class="badge bg-warning bg-opacity-10 text-warning mt-1"><i class="bi bi-hourglass-split me-1"></i>Due in {{ $daysLeft }} days</span></div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $deadline->format('d M Y') }}</div>
                                <small class="text-muted">{{ $deadline->diffForHumans() }}</small>
                                <div class="progress mt-1" style="height: 3px; width: 80px;">
                                    <div class="progress-bar bg-{{ $isOverdue ? 'danger' : ($isUrgent ? 'warning' : 'success') }}" style="width: {{ max(0, 100 - ($daysLeft / 30 * 100)) }}%"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} px-3 py-2">
                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ ucfirst($task->task_status) }}
                                </span>
                                <div class="progress mt-2" style="height: 4px; width: 80px;">
                                    <div class="progress-bar bg-{{ $statusColor }}" style="width: {{ $progressWidth }}%"></div>
                                </div>
                            </td>
                            <td>
                                @if($hasScore)
                                    <div class="fw-bold text-primary">{{ $score }}%</div>
                                    <small class="text-muted">Grade</small>
                                @else
                                    <span class="text-muted">—</span>
                    @endif
<td class="text-end">
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('intern.tasks.show', $task->task_id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
            <i class="bi bi-eye"></i> View
        </a>
        @if(in_array($task->task_status, ['pending', 'Assigned', 'Rejected', 'rejected']))
        <form action="{{ route('intern.tasks.submit', $task->task_id) }}" method="POST" style="display: inline-block;" class="submit-task-form">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary rounded-pill submit-btn">
                <i class="bi bi-cloud-upload"></i> Submit
            </button>
        </form>
        @endif
    </div>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">✨ No tasks available. You're all caught up!</p>
</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tasks->hasPages())
            <div class="p-3 border-top bg-transparent">
                {{ $tasks->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>

    {{-- ==================== KANBAN VIEW (uses $allTasks - all tasks) ==================== --}}
    <div id="kanbanView" class="view-content" style="display: none;">
        <div class="row g-4">
            @php
                $columns = [
                    ['status' => 'pending', 'title' => '📋 To Do', 'color' => '#f59e0b', 'icon' => 'hourglass-split'],
                    ['status' => 'submitted', 'title' => '📤 In Review', 'color' => '#8b5cf6', 'icon' => 'upload'],
                    ['status' => 'approved', 'title' => '✅ Completed', 'color' => '#10b981', 'icon' => 'check-circle'],
                    ['status' => 'rejected', 'title' => '🔄 Revisions', 'color' => '#ef4444', 'icon' => 'arrow-repeat'],
                ];
            @endphp

            @foreach($columns as $column)
            <div class="col-md-3">
                <div class="kanban-column">
                    <div class="kanban-header" style="background: {{ $column['color'] }}20; border-left: 3px solid {{ $column['color'] }}">
                        <i class="bi bi-{{ $column['icon'] }}" style="color: {{ $column['color'] }}"></i>
                        {{ $column['title'] }}
                        <span class="badge bg-secondary bg-opacity-10 ms-2">{{ $allTasks->where('task_status', $column['status'])->count() }}</span>
                    </div>
                    <div class="kanban-tasks">
                        @foreach($allTasks as $task)
                            @if($task->task_status == $column['status'] || ($column['status'] == 'pending' && in_array($task->task_status, ['pending', 'Assigned'])) || ($column['status'] == 'approved' && in_array($task->task_status, ['approved', 'Completed'])))
                            <div class="kanban-card" draggable="true">
                                <div class="fw-semibold mb-1">{{ $task->task_title }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($task->task_end)->diffForHumans() }}</small>
                                @php $score = $task->grade ?? $task->task_grade ?? null; @endphp
                                @if($score)
                                <div class="mt-1"><span class="badge bg-primary bg-opacity-10 text-primary">⭐ {{ $score }}%</span></div>
                                @endif
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('intern.tasks.show', $task->task_id) }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                    @if(in_array($task->task_status, ['pending', 'Assigned', 'Rejected', 'rejected']))
                                    <form action="{{ route('intern.tasks.submit', $task->task_id) }}" method="POST" style="display: inline-block;" class="submit-task-form">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill submit-btn">
                                            <i class="bi bi-cloud-upload"></i> Submit
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ==================== TIMELINE VIEW (uses $allTasks - all tasks) ==================== --}}
    <div id="timelineView" class="view-content" style="display: none;">
        <div class="premium-card p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Task Timeline</h5>
                    <p class="text-muted small mb-0">Chronological view of your task journey</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <select id="timelineFilter" class="form-select form-select-sm rounded-pill" style="width: auto;">
                        <option value="all">📋 All Tasks</option>
                        <option value="pending">⏳ Pending</option>
                        <option value="submitted">📤 Submitted</option>
                        <option value="approved">✅ Approved</option>
                        <option value="rejected">🔄 Rejected</option>
                    </select>
                    <select id="timelineSort" class="form-select form-select-sm rounded-pill" style="width: auto;">
                        <option value="asc">📅 Earliest First</option>
                        <option value="desc">📅 Latest First</option>
                    </select>
                    <button id="timelineExpandAll" class="btn btn-sm btn-outline-primary rounded-pill">
                        <i class="bi bi-arrows-expand"></i> Expand All
                    </button>
                </div>
            </div>
            
            <div class="row g-2 mb-4">
                <div class="col-3">
                    <div class="text-center p-2 rounded-3" style="background: rgba(59,130,246,0.1)">
                        <small class="text-muted">Total Tasks</small>
                        <div class="fw-bold" id="timelineTotalCount">{{ $allTasks->count() }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="text-center p-2 rounded-3" style="background: rgba(16,185,129,0.1)">
                        <small class="text-muted">Completed</small>
                        <div class="fw-bold text-success" id="timelineCompletedCount">{{ $allTasks->whereIn('task_status', ['approved', 'Completed'])->count() }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="text-center p-2 rounded-3" style="background: rgba(245,158,11,0.1)">
                        <small class="text-muted">In Progress</small>
                        <div class="fw-bold text-warning" id="timelinePendingCount">{{ $allTasks->whereIn('task_status', ['pending', 'Assigned'])->count() }}</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="text-center p-2 rounded-3" style="background: rgba(239,68,68,0.1)">
                        <small class="text-muted">Overdue</small>
                        <div class="fw-bold text-danger" id="timelineOverdueCount">
                            @php
                                $overdue = $allTasks->filter(function($task) {
                                    return \Carbon\Carbon::parse($task->task_end)->isPast() && !in_array($task->task_status, ['approved', 'Completed']);
                                })->count();
                            @endphp
                            {{ $overdue }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="timelineContainer">
                <div class="timeline-view" id="timelineItems">
                    @php
                        $sortedTasks = $allTasks->sortBy('task_end');
                        $lastYear = null;
                    @endphp
                    
                    @foreach($sortedTasks as $task)
                    @php
                        $deadline = \Carbon\Carbon::parse($task->task_end);
                        $currentYear = $deadline->format('Y');
                        $currentMonth = $deadline->format('F');
                        $isPast = $deadline->isPast();
                        $showYearHeader = ($lastYear !== $currentYear);
                        $statusColor = match($task->task_status) {
                            'approved', 'Completed' => '#10b981',
                            'submitted' => '#8b5cf6',
                            'rejected', 'Rejected' => '#ef4444',
                            default => '#f59e0b'
                        };
                        $taskStatus = match($task->task_status) {
                            'approved', 'Completed' => 'approved',
                            'submitted' => 'submitted',
                            'rejected', 'Rejected' => 'rejected',
                            default => 'pending'
                        };
                        $score = $task->grade ?? $task->task_grade ?? null;
                    @endphp
                    
                    @if($showYearHeader)
                        <div class="timeline-year-header mt-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="timeline-year-line flex-grow-1"></div>
                                <span class="timeline-year-badge px-3 py-1 rounded-pill">{{ $currentYear }}</span>
                                <div class="timeline-year-line flex-grow-1"></div>
                            </div>
                        </div>
                        @php $lastYear = $currentYear; @endphp
                    @endif
                    
                    <div class="timeline-item task-timeline-item" data-task-status="{{ $taskStatus }}" data-task-date="{{ $deadline->timestamp }}" style="border-left-color: {{ $statusColor }}">
                        <div class="timeline-badge" style="border-color: {{ $statusColor }}; background: {{ $statusColor }}20"></div>
                        <div class="timeline-month-badge">
                            <i class="bi bi-calendar3"></i> {{ $currentMonth }}
                        </div>
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <i class="bi bi-file-text" style="color: {{ $statusColor }}"></i>
                                    <span class="fw-semibold">{{ $task->task_title }}</span>
                                    <span class="badge bg-{{ $isPast ? 'danger' : 'success' }} bg-opacity-10 text-{{ $isPast ? 'danger' : 'success' }} rounded-pill">
                                        <i class="bi bi-{{ $isPast ? 'calendar-x' : 'calendar-check' }} me-1"></i>
                                        {{ $deadline->format('d M Y') }}
                                    </span>
                                    @if($score)
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                        <i class="bi bi-star-fill me-1"></i>{{ $score }}%
                                    </span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-2 timeline-description" data-full-text="{{ $task->task_description ?? 'No description' }}">
                                    {{ \Illuminate\Support\Str::limit($task->task_description ?? 'No description', 100) }}
                                    @if(strlen($task->task_description ?? '') > 100)
                                    <a href="javascript:void(0)" class="expand-desc text-primary text-decoration-none small">...read more</a>
                                    @endif
                                </p>
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-hourglass-split text-muted small"></i>
                                        <small class="text-muted">{{ $deadline->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-pie-chart text-muted small"></i>
                                        <small class="text-muted">{{ ucfirst($task->task_status) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('intern.tasks.show', $task->task_id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="bi bi-arrow-right"></i> Details
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <button class="scroll-top-btn" id="scrollToTopBtn" style="display: none;">
                <i class="bi bi-arrow-up fs-5"></i>
            </button>
        </div>
    </div>

</div>

<script>
// LOADING STATE FOR SUBMIT BUTTONS
document.querySelectorAll('.submit-task-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.submit-btn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Submitting...';
            submitBtn.classList.add('btn-loading');
        }
    });
});

// VIEW TOGGLE
document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const view = this.dataset.view;
        document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tableView').style.display = view === 'table' ? 'block' : 'none';
        document.getElementById('kanbanView').style.display = view === 'kanban' ? 'block' : 'none';
        document.getElementById('timelineView').style.display = view === 'timeline' ? 'block' : 'none';
        if (view === 'timeline') {
            setTimeout(initializeTimeline, 100);
        }
    });
});

// TABLE SEARCH
document.getElementById('taskSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#taskTable tbody tr');
    rows.forEach(row => {
        const title = row.dataset?.title || '';
        row.style.display = title.includes(searchTerm) ? '' : 'none';
    });
});

// KANBAN DRAG & DROP
let draggedItem = null;
document.querySelectorAll('.kanban-card').forEach(card => {
    card.setAttribute('draggable', 'true');
    card.addEventListener('dragstart', function(e) {
        draggedItem = this;
        this.style.opacity = '0.5';
    });
    card.addEventListener('dragend', function(e) {
        this.style.opacity = '';
    });
});

document.querySelectorAll('.kanban-column .kanban-tasks').forEach(container => {
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    container.addEventListener('drop', function(e) {
        e.preventDefault();
        if (draggedItem) {
            this.appendChild(draggedItem);
            draggedItem = null;
        }
    });
});

// TIMELINE FUNCTIONALITY
let allTimelineItems = [];

function initializeTimeline() {
    allTimelineItems = Array.from(document.querySelectorAll('.task-timeline-item'));
    if (allTimelineItems.length > 0) {
        updateTimelineDisplay();
    }
    attachExpandHandlers();
    setupScrollToTop();
}

function updateTimelineDisplay() {
    const filterValue = document.getElementById('timelineFilter')?.value || 'all';
    const sortValue = document.getElementById('timelineSort')?.value || 'asc';
    let filteredItems = [...allTimelineItems];
    if (filterValue !== 'all') {
        filteredItems = filteredItems.filter(item => item.dataset.taskStatus === filterValue);
    }
    filteredItems.sort((a, b) => {
        const dateA = parseInt(a.dataset.taskDate);
        const dateB = parseInt(b.dataset.taskDate);
        return sortValue === 'asc' ? dateA - dateB : dateB - dateA;
    });
    const totalCount = filteredItems.length;
    const completedCount = filteredItems.filter(item => item.dataset.taskStatus === 'approved').length;
    const pendingCount = filteredItems.filter(item => item.dataset.taskStatus === 'pending').length;
    const overdueCount = filteredItems.filter(item => {
        const isPast = new Date(parseInt(item.dataset.taskDate) * 1000) < new Date();
        return isPast && item.dataset.taskStatus !== 'approved';
    }).length;
    document.getElementById('timelineTotalCount').textContent = totalCount;
    document.getElementById('timelineCompletedCount').textContent = completedCount;
    document.getElementById('timelinePendingCount').textContent = pendingCount;
    document.getElementById('timelineOverdueCount').textContent = overdueCount;
    allTimelineItems.forEach(item => item.style.display = 'none');
    filteredItems.forEach(item => item.style.display = 'block');
}

function attachExpandHandlers() {
    document.querySelectorAll('.expand-desc').forEach(link => {
        link.removeEventListener('click', handleExpandClick);
        link.addEventListener('click', handleExpandClick);
    });
}

function handleExpandClick(e) {
    e.preventDefault();
    e.stopPropagation();
    const descElement = this.closest('.timeline-description');
    const fullText = descElement.dataset.fullText;
    if (descElement.textContent.includes('read more')) {
        descElement.innerHTML = fullText;
    } else {
        descElement.innerHTML = fullText.substring(0, 100) + '... <a href="javascript:void(0)" class="expand-desc text-primary text-decoration-none small">read more</a>';
        attachExpandHandlers();
    }
}

function setupScrollToTop() {
    const container = document.getElementById('timelineContainer');
    const scrollBtn = document.getElementById('scrollToTopBtn');
    if (!container || !scrollBtn) return;
    container.addEventListener('scroll', function() {
        scrollBtn.style.display = container.scrollTop > 200 ? 'flex' : 'none';
    });
    scrollBtn.addEventListener('click', function() {
        container.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

document.getElementById('timelineFilter')?.addEventListener('change', () => updateTimelineDisplay());
document.getElementById('timelineSort')?.addEventListener('change', () => updateTimelineDisplay());
document.getElementById('timelineExpandAll')?.addEventListener('click', () => {
    document.querySelectorAll('.timeline-description').forEach(desc => {
        const fullText = desc.dataset.fullText;
        if (fullText && fullText.length > 0) {
            desc.innerHTML = fullText;
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('timelineView').style.display !== 'none') {
        initializeTimeline();
    }
    
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection