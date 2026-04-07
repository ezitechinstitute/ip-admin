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

@section('vendor-style')
@vite([
'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
'resources/assets/vendor/libs/swiper/swiper.scss',
'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/fonts/flag-icons.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss'
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/cards-advance.scss')
@endsection

@section('vendor-script')
@vite([
'resources/assets/vendor/libs/apex-charts/apexcharts.js',
'resources/assets/vendor/libs/swiper/swiper.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js',
'resources/assets/vendor/libs/pickr/pickr.js'
])
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')

{{-- Flash Messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
    <i class="ti ti-check-circle me-1"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<script>
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(a => {
            a.classList.remove('show');
            setTimeout(() => a.remove(), 400);
        });
    }, 5000);
</script>

{{-- ── ROW 1: Profile + Stat Cards ──────────────────────── --}}
<div class="row g-4">

    {{-- Profile Card --}}
    <div class="col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body bg-primary text-white rounded-3 p-4">

                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="position-relative flex-shrink-0">
                        <img src="{{ $profileImage }}"
                             alt="{{ $intern->name ?? 'Intern' }}"
                             class="rounded-circle border border-3 border-white shadow"
                             width="68" height="68"
                             style="object-fit:cover;"
                             onerror="this.src='{{ asset('assets/img/branding/ezitech.png') }}'">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle d-block"
                              style="width:13px;height:13px;"></span>
                    </div>
                    <div>
                        <p class="mb-0 fw-semibold text-uppercase opacity-75" style="font-size:.65rem;letter-spacing:.08em;">Welcome back 👋</p>
                        <h5 class="mb-0 fw-bold text-white">{{ $intern->name ?? 'Intern' }}</h5>
                        <p class="mb-0 opacity-50" style="font-size:.74rem;">
                            <i class="ti ti-calendar me-1"></i>Started {{ Carbon::parse($intern->start_date)->format('d M Y') }}
                        </p>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mb-4">
                    <span class="badge bg-sucess bg-opacity-10 border border-white border-opacity-25 text-white fw-semibold px-3 py-2 rounded-pill" style="font-size:.71rem;">
                        <i class="ti ti-code me-1"></i>{{ $intern->int_technology ?? 'Not Assigned' }}
                    </span>
                    <span class="badge bg-success bg-opacity-25 border border-success border-opacity-25 text-white fw-semibold px-3 py-2 rounded-pill" style="font-size:.71rem;">
                        <i class="ti ti-{{ $stats['internship_status'] == 'Active' ? 'circle-check' : 'clock' }} me-1"></i>{{ $stats['internship_status'] }}
                    </span>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="bg-sucess bg-opacity-10 border border-white border-opacity-10 rounded-3 p-3 text-center">
                            <div class="fs-5 fw-bold">{{ number_format($remainingDays) }}</div>
                            <div class="opacity-50" style="font-size:.68rem;">Days Remaining</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-sucess bg-opacity-10 border border-white border-opacity-10 rounded-3 p-3 text-center">
                            <div class="fs-5 fw-bold">{{ number_format($stats['tasks_completed']) }}/{{ number_format($stats['tasks_total']) }}</div>
                            <div class="opacity-50" style="font-size:.68rem;">Tasks Done</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1 opacity-75 fw-semibold" style="font-size:.7rem;">
                        <span>Internship Progress</span>
                        <span>{{ $progressPercent }}%</span>
                    </div>
                    <div class="progress rounded-pill" style="height:6px;background:rgba(255,255,255,.2);">
                        <div class="progress-bar bg-white rounded-pill" style="width:{{ $progressPercent }}%;"></div>
                    </div>
                </div>

                <a href="{{ route('intern.profile') }}" class="btn btn-light btn-sm w-100 fw-semibold">
                    <i class="ti ti-user me-1"></i>View Full Profile
                </a>

            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="col-xl-8">
        <div class="row g-4 h-100">

            {{-- Tasks --}}
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-uppercase fw-bold text-muted mb-0" style="font-size:.63rem;letter-spacing:.1em;">Tasks</p>
                                <h6 class="fw-bold mb-0">Overall Progress</h6>
                            </div>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-2">
                                <i class="ti ti-checklist fs-5"></i>
                            </div>
                        </div>
                        <div class="display-5 fw-bold text-primary mb-1">{{ number_format($progress['task_percentage']) }}%</div>
                        <p class="text-muted mb-2" style="font-size:.74rem;">Completion rate</p>
                        <div class="progress rounded-pill mb-4" style="height:6px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width:{{ $progress['task_percentage'] }}%;"></div>
                        </div>
                        <div class="row g-0 text-center">
                            <div class="col-4 border-end">
                                <div class="fw-bold text-success">{{ number_format($stats['tasks_completed']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Done</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="fw-bold text-warning">{{ number_format($stats['tasks_pending']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Pending</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-info">{{ number_format($stats['tasks_submitted']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Submitted</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Projects --}}
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <p class="text-uppercase fw-bold text-muted mb-0" style="font-size:.63rem;letter-spacing:.1em;">Projects</p>
                                <h6 class="fw-bold mb-0">Overall Progress</h6>
                            </div>
                            <div class="bg-info bg-opacity-10 text-info rounded-2 p-2">
                                <i class="ti ti-briefcase fs-5"></i>
                            </div>
                        </div>
                        <div class="display-5 fw-bold text-info mb-1">{{ number_format($progress['project_percentage']) }}%</div>
                        <p class="text-muted mb-2" style="font-size:.74rem;">Completion rate</p>
                        <div class="progress rounded-pill mb-4" style="height:6px;">
                            <div class="progress-bar bg-info rounded-pill" style="width:{{ $progress['project_percentage'] }}%;"></div>
                        </div>
                        <div class="row g-0 text-center">
                            <div class="col-4 border-end">
                                <div class="fw-bold text-success">{{ number_format($stats['projects_completed']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Done</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="fw-bold text-warning">{{ number_format($stats['projects_ongoing']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Ongoing</div>
                            </div>
                            <div class="col-4">
                                <div class="fw-bold text-secondary">{{ number_format($stats['projects_assigned']) }}</div>
                                <div class="text-muted" style="font-size:.68rem;">Total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ── ROW 2: Timeline + Notifications ──────────────────── --}}
<div class="row g-4 mt-1">

    {{-- Timeline --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                    <i class="ti ti-calendar-stats" style="font-size:.85rem;"></i>
                </div>
                <h6 class="fw-bold mb-0">Internship Timeline</h6>
            </div>
            <div class="card-body p-4">
                @if(count($timeline) > 0)
                <div class="ps-3 border-start border-2 border-primary">
                    @foreach($timeline as $item)
                    <div class="position-relative {{ $loop->last ? '' : 'pb-4' }}">
                        <span class="position-absolute bg-{{ $item['completed'] ? 'primary' : 'secondary' }} rounded-circle border border-2 border-white shadow-sm"
                              style="width:12px;height:12px;left:-1.45rem;top:4px;"></span>
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="fw-bold mb-0" style="font-size:.84rem;">{{ $item['title'] }}</h6>
                            <small class="text-muted ms-3 text-nowrap" style="font-size:.68rem;">{{ $item['date'] }}</small>
                        </div>
                        <p class="text-muted mb-2" style="font-size:.76rem;line-height:1.5;">{{ $item['description'] }}</p>
                        @if($item['completed'])
                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold rounded-pill px-3 py-1" style="font-size:.68rem;">
                                <i class="ti ti-check me-1"></i>Completed
                            </span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-semibold rounded-pill px-3 py-1" style="font-size:.68rem;">
                                <i class="ti ti-clock me-1"></i>Pending
                            </span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ti ti-calendar-off fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0" style="font-size:.83rem;">No timeline events yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Notifications --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                        <i class="ti ti-bell" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Notifications</h6>
                </div>
                @if($notifications->count() > 0)
                <button class="btn btn-outline-primary btn-sm mark-all-read py-1 px-2" style="font-size:.72rem;">Mark all read</button>
                @endif
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                    <div class="list-group-item list-group-item-action border-0 border-bottom px-3 py-3 notification-item {{ !$notification->is_read ? 'bg-primary bg-opacity-10 border-start border-3 border-primary' : '' }}"
                         data-id="{{ $notification->id }}">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-2 flex-shrink-0">
                                <i class="ti ti-{{ $notification->type ?? 'bell' }}" style="font-size:.85rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold mb-1" style="font-size:.82rem;">{{ $notification->title }}</div>
                                <div class="text-muted mb-1" style="font-size:.74rem;line-height:1.45;">{{ $notification->message }}</div>
                                <small class="text-muted" style="font-size:.66rem;">
                                    <i class="ti ti-clock me-1"></i>{{ Carbon::parse($notification->created_at)->diffForHumans() }}
                                </small>
                            </div>
                            @if(!$notification->is_read)
                            <button class="btn btn-link btn-sm p-0 text-primary mark-read" data-id="{{ $notification->id }}" title="Mark as read">
                                <i class="ti ti-circle-check fs-5"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ti ti-bell-off fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0" style="font-size:.83rem;">No new notifications</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- ── ROW 3: Performance Overview ──────────────────────── --}}
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                    <i class="ti ti-chart-line" style="font-size:.85rem;"></i>
                </div>
                <h6 class="fw-bold mb-0">Performance Overview</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded-3 p-4 text-center">
                            <div class="display-6 fw-bold text-primary mb-1">{{ number_format($performance['total_tasks']) }}</div>
                            <div class="text-muted fw-semibold" style="font-size:.74rem;">Total Tasks Assigned</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-4 text-center">
                            <div class="display-6 fw-bold text-success mb-1">{{ number_format($performance['completed_tasks']) }}</div>
                            <div class="text-muted fw-semibold" style="font-size:.74rem;">Tasks Completed</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded-3 p-4 text-center">
                            <div class="display-6 fw-bold text-warning mb-1">{{ round($performance['average_score'] ?? 0) }}%</div>
                            <div class="text-muted fw-semibold" style="font-size:.74rem;">Average Score</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── ROW 4: Recent Tasks Table ─────────────────────────── --}}
<div class="row g-4 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                        <i class="ti ti-subtask" style="font-size:.85rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-0">Recent Tasks</h6>
                </div>
                @if($recentTasks->count() > 0)
                <a href="{{ url('/intern/tasks') }}" class="btn btn-outline-primary btn-sm">
                    View All <i class="ti ti-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-bold text-muted text-uppercase border-0 ps-4" style="font-size:.65rem;letter-spacing:.08em;">Task Title</th>
                                <th class="fw-bold text-muted text-uppercase border-0" style="font-size:.65rem;letter-spacing:.08em;">Deadline</th>
                                <th class="fw-bold text-muted text-uppercase border-0" style="font-size:.65rem;letter-spacing:.08em;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTasks as $task)
                            <tr>
                                <td class="fw-semibold ps-4" style="font-size:.84rem;">{{ \Illuminate\Support\Str::limit($task->task_title, 50) }}</td>
                                <td>
                                    <span class="fw-semibold text-{{ Carbon::parse($task->task_end)->isPast() ? 'danger' : 'muted' }}" style="font-size:.74rem;">
                                        <i class="ti ti-calendar me-1"></i>{{ Carbon::parse($task->task_end)->format('d M Y') }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusMap = [
                                            'approved'    => ['bg' => 'success', 'icon' => 'check-circle'],
                                            'rejected'    => ['bg' => 'danger',  'icon' => 'x-circle'],
                                            'submitted'   => ['bg' => 'info',    'icon' => 'send'],
                                            'pending'     => ['bg' => 'warning', 'icon' => 'clock'],
                                            'in_progress' => ['bg' => 'primary', 'icon' => 'loader'],
                                            'completed'   => ['bg' => 'success', 'icon' => 'check'],
                                        ];
                                        $sc = $statusMap[$task->task_status] ?? ['bg' => 'secondary', 'icon' => 'circle'];
                                    @endphp
                                    <span class="badge bg-{{ $sc['bg'] }} bg-opacity-10 text-{{ $sc['bg'] }} rounded-pill px-3 py-2 fw-semibold" style="font-size:.7rem;">
                                        <i class="ti ti-{{ $sc['icon'] }} me-1"></i>{{ ucfirst(str_replace('_', ' ', $task->task_status)) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="ti ti-clipboard-off fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0" style="font-size:.83rem;">No tasks assigned yet</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── ROW 5: Invoice Summary + Upcoming Deadlines ──────── --}}
<div class="row g-4 mt-1 mb-4">

    {{-- Invoice Summary --}}
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="bg-success bg-opacity-10 text-success rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                    <i class="ti ti-wallet" style="font-size:.85rem;"></i>
                </div>
                <h6 class="fw-bold mb-0">Invoice Summary</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="ti ti-circle-check fs-3 text-success d-block mb-2"></i>
                            <div class="fs-4 fw-bold text-success">{{ number_format($stats['paid_invoices']) }}</div>
                            <div class="text-muted fw-semibold" style="font-size:.7rem;">Paid</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="ti ti-clock fs-3 text-warning d-block mb-2"></i>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($stats['pending_invoices']) }}</div>
                            <div class="text-muted fw-semibold" style="font-size:.7rem;">Pending</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="ti ti-alert-triangle fs-3 text-danger d-block mb-2"></i>
                            <div class="fs-4 fw-bold text-danger">{{ number_format($stats['overdue_invoices']) }}</div>
                            <div class="text-muted fw-semibold" style="font-size:.7rem;">Overdue</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3 text-center">
                            <i class="ti ti-percentage fs-3 text-primary d-block mb-2"></i>
                            <div class="fs-4 fw-bold text-primary">{{ number_format($progress['payment_percentage']) }}%</div>
                            <div class="text-muted fw-semibold" style="font-size:.7rem;">Payment Progress</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Upcoming Deadlines --}}
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-2 p-1 d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                    <i class="ti ti-calendar-time" style="font-size:.85rem;"></i>
                </div>
                <h6 class="fw-bold mb-0">Upcoming Deadlines</h6>
            </div>
            <div class="card-body p-0">
                @if($upcomingDeadlines->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($upcomingDeadlines as $task)
                    @php
                        $daysLeft = Carbon::parse($task->task_end)->diffInDays(Carbon::now());
                        $dc = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info');
                    @endphp
                    <div class="list-group-item border-0 border-bottom px-3 py-3">
                        <div class="d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <div class="fw-semibold mb-1" style="font-size:.84rem;">{{ $task->task_title }}</div>
                                <small class="text-muted" style="font-size:.7rem;">
                                    <i class="ti ti-calendar me-1"></i>Due {{ Carbon::parse($task->task_end)->format('d M Y') }}
                                </small>
                            </div>
                            <span class="badge bg-{{ $dc }} bg-opacity-10 text-{{ $dc }} rounded-pill px-3 py-2 fw-semibold flex-shrink-0" style="font-size:.7rem;">
                                <i class="ti ti-alarm me-1"></i>{{ number_format($daysLeft) }}d left
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-5">
                    <i class="ti ti-calendar-off fs-1 text-muted d-block mb-2"></i>
                    <p class="text-muted mb-0" style="font-size:.83rem;">No upcoming deadlines</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // Mark single notification as read
    $('.mark-read').on('click', function (e) {
        e.preventDefault();
        var id  = $(this).data('id');
        var btn = $(this);
        $.ajax({
            url: '{{ url("intern/notification") }}/' + id + '/mark-read',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (r) {
                if (r.success) {
                    btn.closest('.notification-item')
                       .removeClass('bg-primary bg-opacity-10 border-start border-3 border-primary');
                    btn.remove();
                    if ($('.notification-item.bg-primary').length === 0) {
                        $('.mark-all-read').hide();
                    }
                }
            }
        });
    });

    // Mark all notifications as read
    $('.mark-all-read').on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.prop('disabled', true).text('Marking…');
        $.ajax({
            url: '{{ url("intern/notifications/mark-all-read") }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (r) {
                if (r.success) {
                    $('.notification-item')
                        .removeClass('bg-primary bg-opacity-10 border-start border-3 border-primary');
                    $('.mark-read').remove();
                    btn.hide();
                }
            },
            error: function () {
                btn.prop('disabled', false).text('Mark all read');
            }
        });
    });

});
</script>
@endpush