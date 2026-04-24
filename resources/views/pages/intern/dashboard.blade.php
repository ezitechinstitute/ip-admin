@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Carbon\Carbon;

$configData = Helper::appClasses();
$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

$startDate = Carbon::parse($intern->start_date);
$endDate = $startDate->copy()->addMonths(6);
$totalDays = $startDate->diffInDays($endDate);
$elapsedDays = $startDate->diffInDays(Carbon::now());
$remainingDays = max(0, $totalDays - $elapsedDays);
$progressPercent = $totalDays > 0 ? round(($elapsedDays / $totalDays) * 100) : 0;
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Intern Dashboard')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')

{{-- ── Alert ── --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
    <span>{{ session('success') }}</span>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════ --}}
{{-- ROW 1 · Welcome + Stat Pills             --}}
{{-- ══════════════════════════════════════════ --}}
<div class="row g-4 mb-4">

    {{-- Welcome Card --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card h-100 border shadow-sm">
            <div class="card-body p-4">

                {{-- Avatar + Name --}}
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="{{ $profileImage }}"
                         class="rounded-circle border border-2 border-primary"
                         width="68" height="68"
                         style="object-fit:cover;"
                         alt="{{ $intern->name }}">
                    <div>
                        <p class="text-muted small text-uppercase fw-semibold mb-0">Welcome back</p>
                        <h5 class="fw-bold mb-0">{{ $intern->name }}</h5>
                        <small class="text-muted">
                            <i class="bi bi-calendar-event me-1"></i>
                            Since {{ Carbon::parse($intern->start_date)->format('d M Y') }}
                        </small>
                    </div>
                </div>

                {{-- Internship Progress --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size:.7rem;">Internship Progress</small>
                        <small class="fw-bold text-primary" style="font-size:.7rem;">{{ $progressPercent }}%</small>
                    </div>
                    <div class="progress rounded-pill" style="height:8px;">
                        <div class="progress-bar bg-primary rounded-pill" style="width:{{ $progressPercent }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted" style="font-size:.7rem;">{{ Carbon::parse($intern->start_date)->format('d M') }}</small>
                        <small class="text-muted" style="font-size:.7rem;">{{ $endDate->format('d M Y') }}</small>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="row text-center g-0 border rounded-3 overflow-hidden">
                    <div class="col py-3">
                        <div class="fw-bold fs-5 text-primary">{{ $remainingDays }}</div>
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;">Days Left</div>
                    </div>
                    <div class="col py-3 border-start border-end">
                        <div class="fw-bold fs-5 text-success">{{ $stats['tasks_completed'] }}</div>
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;">Done</div>
                    </div>
                    <div class="col py-3">
                        <div class="fw-bold fs-5 text-secondary">{{ $stats['tasks_total'] }}</div>
                        <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;">Total</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Stat Pills --}}
    <div class="col-xl-8 col-lg-7">
        <div class="row g-3 h-100">

            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded p-2 fs-4 lh-1">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-primary lh-1">{{ $progress['task_percentage'] }}%</div>
                            <div class="text-muted small mt-1">Task Progress</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-success bg-opacity-10 text-success rounded p-2 fs-4 lh-1">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-success lh-1">{{ $progress['project_percentage'] }}%</div>
                            <div class="text-muted small mt-1">Project Progress</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded p-2 fs-4 lh-1">
                            <i class="bi bi-star"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-warning lh-1">{{ round($performance['average_score']) }}%</div>
                            <div class="text-muted small mt-1">Avg. Score</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3 p-3">
                        <div class="bg-info bg-opacity-10 text-info rounded p-2 fs-4 lh-1">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4 text-info lh-1">{{ $performance['completed_tasks'] }}</div>
                            <div class="text-muted small mt-1">Completed</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- ROW 2 · Performance Overview (left) + Recent Tasks (right)--}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="row g-4">

    {{-- ── Performance Overview ── --}}
    <div class="col-lg-4">
        <div class="card h-100 border shadow-sm">
            <div class="card-header border-bottom py-3">
                <span class="fw-semibold text-uppercase text-muted small">
                    <i class="bi bi-graph-up me-2 text-primary"></i>Performance Overview
                </span>
            </div>
            <div class="card-body p-0">

                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-primary bg-opacity-10 text-primary rounded p-2 lh-1 fs-5">
                            <i class="bi bi-clipboard2-check"></i>
                        </span>
                        <span class="text-muted small">Total Tasks</span>
                    </div>
                    <span class="fw-bold fs-5 text-primary">{{ $performance['total_tasks'] }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-success bg-opacity-10 text-success rounded p-2 lh-1 fs-5">
                            <i class="bi bi-check2-circle"></i>
                        </span>
                        <span class="text-muted small">Completed</span>
                    </div>
                    <span class="fw-bold fs-5 text-success">{{ $performance['completed_tasks'] }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-warning bg-opacity-10 text-warning rounded p-2 lh-1 fs-5">
                            <i class="bi bi-hourglass-split"></i>
                        </span>
                        <span class="text-muted small">Pending</span>
                    </div>
                    <span class="fw-bold fs-5 text-warning">
                        {{ $performance['total_tasks'] - $performance['completed_tasks'] }}
                    </span>
                </div>

                <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="bg-info bg-opacity-10 text-info rounded p-2 lh-1 fs-5">
                            <i class="bi bi-trophy"></i>
                        </span>
                        <span class="text-muted small">Avg. Score</span>
                    </div>
                    <span class="fw-bold fs-5 text-info">{{ round($performance['average_score']) }}%</span>
                </div>

                {{-- Completion Rate --}}
                <div class="px-4 py-3">
                    @php
                        $completionRate = $performance['total_tasks'] > 0
                            ? round(($performance['completed_tasks'] / $performance['total_tasks']) * 100)
                            : 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted text-uppercase fw-semibold" style="font-size:.7rem;">Completion Rate</small>
                        <small class="fw-bold text-success" style="font-size:.7rem;">{{ $completionRate }}%</small>
                    </div>
                    <div class="progress rounded-pill" style="height:6px;">
                        <div class="progress-bar bg-success rounded-pill" style="width:{{ $completionRate }}%"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Recent Tasks ── --}}
    <div class="col-lg-8">
        <div class="card h-100 border shadow-sm">
            <div class="card-header  border-bottom d-flex align-items-center justify-content-between py-3">
                <span class="fw-semibold text-uppercase text-muted small">
                    <i class="bi bi-list-task me-2 text-primary"></i>Recent Tasks
                </span>
                <a href="{{ url('/intern/tasks') }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted text-uppercase fw-semibold small ps-4" style="font-size:.72rem;">Task</th>
                            <th class="text-muted text-uppercase fw-semibold small" style="font-size:.72rem;">Deadline</th>
                            <th class="text-muted text-uppercase fw-semibold small" style="font-size:.72rem;">Priority</th>
                            <th class="text-muted text-uppercase fw-semibold small" style="font-size:.72rem;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-text text-muted"></i>
                                    <span class="fw-medium small">{{ $task->task_title }}</span>
                                </div>
                            </td>

                            <td>
                                <span class="d-inline-flex align-items-center gap-1 text-muted small">
                                    <i class="bi bi-calendar-event"></i>
                                    {{ Carbon::parse($task->task_end)->format('d M Y') }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $priority = strtolower($task->task_priority ?? 'medium');
                                    [$pIcon, $pBadge] = match($priority) {
                                        'high'  => ['bi-arrow-up-circle-fill',   'danger'],
                                        'low'   => ['bi-arrow-down-circle-fill', 'success'],
                                        default => ['bi-dash-circle-fill',       'warning'],
                                    };
                                @endphp
                                <span class="badge rounded-pill bg-{{ $pBadge }} bg-opacity-10 text-{{ $pBadge }} fw-semibold">
                                    <i class="bi {{ $pIcon }} me-1"></i>{{ ucfirst($priority) }}
                                </span>
                            </td>

                            <td>
                                @php
                                    $status = strtolower(str_replace(' ', '', $task->task_status));
                                    [$sIcon, $sBadge] = match($status) {
                                        'completed'  => ['bi-check-lg',           'success'],
                                        'inprogress' => ['bi-arrow-repeat',       'primary'],
                                        'overdue'    => ['bi-exclamation-circle', 'danger'],
                                        default      => ['bi-clock',              'warning'],
                                    };
                                @endphp
                                <span class="badge rounded-pill bg-{{ $sBadge }} bg-opacity-10 text-{{ $sBadge }} fw-semibold">
                                    <i class="bi {{ $sIcon }} me-1"></i>{{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                No tasks assigned yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

@endsection