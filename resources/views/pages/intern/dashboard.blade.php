@php
use Illuminate\Support\Facades\Auth;
use App\Helpers\Helpers;
use Carbon\Carbon;

$configData = Helper::appClasses();
$intern = Auth::guard('intern')->user();
$profileImage = Helpers::getProfileImage($intern);

// Calculate days properly with clean numbers
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
<style>
/* Card Improvements - Light Theme */
.card {
    border: none;
    border-radius: 0.75rem;
    background: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
}

.card-header {
    background: transparent;
    border-bottom-color: #e9ecef;
    padding: 1rem 1.25rem;
}

.card-header h5, .card-header h6 {
    color: #1e293b;
}

.card-body {
    padding: 1.25rem;
}

.card-title {
    color: #1e293b;
    font-weight: 600;
}

/* Progress Bar */
.progress {
    height: 8px;
    border-radius: 10px;
    background-color: #e2e8f0;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
}

/* Table */
.table {
    vertical-align: middle;
}

.table-light {
    background-color: #f8fafc;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.04);
}

/* Timeline */
.timeline-vertical {
    position: relative;
    padding-left: 1.5rem;
}

.timeline-item {
    position: relative;
    padding-left: 1rem;
    padding-bottom: 1rem;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
}

.timeline-item:last-child::before {
    bottom: auto;
    height: 20px;
}

.timeline-badge-wrapper {
    position: absolute;
    left: -1.1rem;
    top: 0;
}

.timeline-badge {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #667eea;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #e2e8f0;
}

.timeline-content h6 {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    color: #1e293b;
}

.timeline-content small {
    font-size: 0.65rem;
    color: #64748b;
}

.timeline-content p {
    font-size: 0.75rem;
    color: #64748b;
    margin-bottom: 0;
}

/* Notifications */
.notification-item {
    transition: all 0.25s ease;
    border-bottom-color: #e9ecef;
}

.notification-item:hover {
    background-color: #f8fafc;
    transform: translateX(3px);
}

.notification-item.bg-light {
    background-color: #f1f5f9 !important;
}

/* Badges */
.badge {
    font-weight: 500;
    letter-spacing: 0.3px;
}

/* Buttons */
.btn {
    border-radius: 8px;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    transform: translateY(-2px);
}

.btn-outline-primary {
    border-color: #667eea;
    color: #667eea;
}

.btn-outline-primary:hover {
    background-color: #667eea;
    color: white;
}

/* Border */
.border {
    border-color: #e9ecef !important;
}

.bg-light {
    background-color: #f8fafc !important;
}

/* Alert */
.alert-info {
    background-color: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
}

/* Text Colors */
.text-muted {
    color: #64748b !important;
}

.text-primary {
    color: #667eea !important;
}

.text-success {
    color: #10b981 !important;
}

.text-warning {
    color: #f59e0b !important;
}

.text-info {
    color: #3b82f6 !important;
}

/* Section spacing */
.card-body {
    padding: 1.25rem;
}

/* Headings */
h1, h2, h3, h4, h5 {
    letter-spacing: -0.3px;
}

/* Mobile optimization */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    .card-body {
        padding: 1rem;
    }
}
</style>
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
{{-- Remove forms-pickers.js as it's not needed on dashboard --}}
{{-- @vite(['resources/assets/js/forms-pickers.js']) --}}
@endsection

@section('content')
{{-- Error Messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Auto-hide script --}}
<script>
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

<div class="row g-4">

    {{-- Welcome Card with Profile Image --}}
    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="position-relative">
                        <img src="{{ $profileImage }}" 
                             alt="{{ $intern->name ?? 'Intern' }}" 
                             class="rounded-circle me-3" 
                             style="width: 70px; height: 70px; object-fit: cover; border: 3px solid #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"
                             onerror="this.src='{{ asset('assets/img/branding/ezitech.png') }}'">
                        <span class="position-absolute bottom-0 end-0 me-3 mb-0">
                            <i class="ti ti-circle-check text-success fs-5" style="background: white; border-radius: 50%;"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">Welcome Back 🎉</h5>
                        <h4 class="mb-0">{{ $intern->name ?? 'Intern' }}!</h4>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-primary px-3 py-2">
                            <i class="ti ti-code me-1"></i>{{ $intern->int_technology ?? 'Not Assigned' }}
                        </span>
                        <span class="badge bg-{{ $stats['internship_status'] == 'Active' ? 'success' : 'warning' }} px-3 py-2">
                            <i class="ti ti-{{ $stats['internship_status'] == 'Active' ? 'circle-check' : 'clock' }} me-1"></i>
                            {{ $stats['internship_status'] }}
                        </span>
                    </div>
                    <p class="text-muted mb-0">
                        <i class="ti ti-calendar me-1"></i>Started: {{ Carbon::parse($intern->start_date)->format('d M, Y') }}
                    </p>
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <h6 class="mb-1 text-primary">{{ number_format($remainingDays) }}</h6>
                            <small class="text-muted">Days Remaining</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3 text-center">
                            <h6 class="mb-1 text-info">{{ number_format($stats['tasks_completed']) }}/{{ number_format($stats['tasks_total']) }}</h6>
                            <small class="text-muted">Tasks Done</small>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('intern.profile') }}" class="btn btn-primary w-100">
                        <i class="ti ti-user me-1"></i> View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats Cards --}}
    <div class="col-xl-8 col-md-12">
        <div class="row g-4 h-100">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Tasks Progress</h5>
                            <i class="ti ti-checklist text-primary fs-3"></i>
                        </div>
                        <div class="text-center mb-3">
                            <h1 class="fw-bold text-primary display-4">{{ number_format($progress['task_percentage']) }}%</h1>
                            <p class="text-muted">Completion Rate</p>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress['task_percentage'] }}%"></div>
                        </div>
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <h5 class="mb-0 text-success">{{ number_format($stats['tasks_completed']) }}</h5>
                                <small class="text-muted">Completed</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0 text-warning">{{ number_format($stats['tasks_pending']) }}</h5>
                                <small class="text-muted">Pending</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0 text-info">{{ number_format($stats['tasks_submitted']) }}</h5>
                                <small class="text-muted">Submitted</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Projects Progress</h5>
                            <i class="ti ti-briefcase text-info fs-3"></i>
                        </div>
                        <div class="text-center mb-3">
                            <h1 class="fw-bold text-info display-4">{{ number_format($progress['project_percentage']) }}%</h1>
                            <p class="text-muted">Completion Rate</p>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $progress['project_percentage'] }}%"></div>
                        </div>
                        <div class="row text-center mt-3">
                            <div class="col-4">
                                <h5 class="mb-0 text-success">{{ number_format($stats['projects_completed']) }}</h5>
                                <small class="text-muted">Completed</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0 text-warning">{{ number_format($stats['projects_ongoing']) }}</h5>
                                <small class="text-muted">Ongoing</small>
                            </div>
                            <div class="col-4">
                                <h5 class="mb-0 text-secondary">{{ number_format($stats['projects_assigned']) }}</h5>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Timeline & Notifications Row --}}
    <div class="row g-4 mt-2">
        {{-- Timeline Section --}}
        <div class="col-md-7">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-stats me-2 fs-5 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Internship Timeline</h6>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($timeline) > 0)
                    <div class="timeline-vertical">
                        @foreach($timeline as $item)
                        <div class="timeline-item">
                            <div class="timeline-item-inner d-flex">
                                <div class="timeline-badge-wrapper">
                                    <span class="timeline-badge bg-{{ $item['color'] }}"></span>
                                </div>
                                <div class="timeline-content flex-grow-1 pb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-semibold">{{ $item['title'] }}</h6>
                                        <small class="text-muted">{{ $item['date'] }}</small>
                                    </div>
                                    <p class="mb-2 small text-muted">{{ $item['description'] }}</p>
                                    @if($item['completed'])
                                        <span class="badge bg-success px-3">
                                            <i class="ti ti-check me-1"></i>Completed
                                        </span>
                                    @else
                                        <span class="badge bg-warning px-3">
                                            <i class="ti ti-clock me-1"></i>Pending
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="ti ti-calendar-off ti-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No timeline events yet</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Notifications Section --}}
        <div class="col-md-5">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-bell me-2 fs-5 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Recent Notifications</h6>
                        </div>
                        @if($notifications->count() > 0)
                        <button class="btn btn-sm btn-outline-primary mark-all-read" style="font-size: 11px;">
                            Mark all read
                        </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                        <div class="list-group-item notification-item {{ !$notification->is_read ? 'bg-light' : '' }}" data-id="{{ $notification->id }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <i class="ti ti-{{ $notification->type ?? 'bell' }} me-2 text-primary"></i>
                                        <h6 class="mb-0">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="mb-1 small text-muted">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="ti ti-clock me-1"></i>{{ Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </small>
                                </div>
                                @if(!$notification->is_read)
                                <button class="btn btn-sm btn-link mark-read p-0 ms-2 text-primary" data-id="{{ $notification->id }}">
                                    <i class="ti ti-circle-check fs-5"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="ti ti-bell-off ti-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No new notifications</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Overview --}}
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-chart-line me-2 fs-5 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Performance Overview</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded bg-light">
                                <h2 class="mb-0 text-primary">{{ number_format($performance['total_tasks']) }}</h2>
                                <small class="text-muted">Total Tasks</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded bg-light">
                                <h2 class="mb-0 text-success">{{ number_format($performance['completed_tasks']) }}</h2>
                                <small class="text-muted">Completed Tasks</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded bg-light">
                                <h2 class="mb-0 text-warning">{{ round($performance['average_score'] ?? 0) }}%</h2>
                                <small class="text-muted">Average Score</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  {{-- Recent Tasks Table --}}
<div class="col-12">
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="ti ti-tasks me-2 fs-5 text-primary"></i>
                <h5 class="mb-0">Recent Tasks</h5>
            </div>
            @if($recentTasks->count() > 0)
            <a href="{{ url('/intern/tasks') }}" class="btn btn-outline-primary btn-sm">
                View All <i class="ti ti-arrow-right ms-1"></i>
            </a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Task Title</th>
                            <th>Deadline</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTasks as $task)
                        <tr>
                            <td class="fw-medium">{{ \Illuminate\Support\Str::limit($task->task_title, 50) }}</td>
                            <td>
                                <span class="badge bg-{{ Carbon::parse($task->task_end)->isPast() ? 'danger' : 'info' }} bg-opacity-10 text-{{ Carbon::parse($task->task_end)->isPast() ? 'danger' : 'info' }}">
                                    <i class="ti ti-calendar me-1"></i>{{ Carbon::parse($task->task_end)->format('d M, Y') }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'approved' => ['class' => 'success', 'icon' => 'check-circle'],
                                        'rejected' => ['class' => 'danger', 'icon' => 'x-circle'],
                                        'submitted' => ['class' => 'info', 'icon' => 'send'],
                                        'pending' => ['class' => 'warning', 'icon' => 'clock'],
                                        'in_progress' => ['class' => 'primary', 'icon' => 'loader'],
                                        'completed' => ['class' => 'success', 'icon' => 'check'],
                                    ];
                                    $config = $statusConfig[$task->task_status] ?? ['class' => 'secondary', 'icon' => 'circle'];
                                @endphp
                                <span class="badge bg-{{ $config['class'] }} bg-opacity-10 text-{{ $config['class'] }} px-3 py-2">
                                    <i class="ti ti-{{ $config['icon'] }} me-1"></i>{{ ucfirst($task->task_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <i class="ti ti-tasks-off ti-2x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No tasks assigned yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    {{-- Invoice Summary & Upcoming Deadlines --}}
    <div class="row g-4">
        <div class="col-xl-6 col-md-12">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-wallet me-2 fs-5 text-primary"></i>
                        <h5 class="mb-0">Invoice Summary</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6 col-6">
                            <div class="border rounded p-3 text-center">
                                <i class="ti ti-wallet text-success fs-3 mb-2"></i>
                                <h4 class="mb-0 text-success">{{ number_format($stats['paid_invoices']) }}</h4>
                                <small class="text-muted">Paid Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="border rounded p-3 text-center">
                                <i class="ti ti-clock text-warning fs-3 mb-2"></i>
                                <h4 class="mb-0 text-warning">{{ number_format($stats['pending_invoices']) }}</h4>
                                <small class="text-muted">Pending Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="border rounded p-3 text-center">
                                <i class="ti ti-alert-triangle text-danger fs-3 mb-2"></i>
                                <h4 class="mb-0 text-danger">{{ number_format($stats['overdue_invoices']) }}</h4>
                                <small class="text-muted">Overdue Invoices</small>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="border rounded p-3 text-center">
                                <i class="ti ti-percentage text-primary fs-3 mb-2"></i>
                                <h4 class="mb-0 text-primary">{{ number_format($progress['payment_percentage']) }}%</h4>
                                <small class="text-muted">Payment Progress</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-calendar-time me-2 fs-5 text-primary"></i>
                        <h5 class="mb-0">Upcoming Deadlines</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($upcomingDeadlines->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingDeadlines as $task)
                        <div class="list-group-item notification-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 fw-semibold">{{ $task->task_title }}</h6>
                                    <small class="text-muted">
                                        <i class="ti ti-calendar me-1"></i>Due: {{ Carbon::parse($task->task_end)->format('d M, Y') }}
                                    </small>
                                </div>
                                @php
                                    $daysLeft = Carbon::parse($task->task_end)->diffInDays(Carbon::now());
                                    $badgeClass = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }} bg-opacity-10 text-{{ $badgeClass }} px-3 py-2">
                                    <i class="ti ti-alarm me-1"></i>{{ number_format($daysLeft) }} days left
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="ti ti-calendar-off ti-2x text-muted mb-3"></i>
                        <p class="text-muted mb-0">No upcoming deadlines</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Wait for jQuery to load
$(document).ready(function() {
    console.log('jQuery loaded successfully');
    
    // Mark single notification as read
    $('.mark-read').click(function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let btn = $(this);
        
        $.ajax({
            url: '{{ url("intern/notification") }}/' + id + '/mark-read',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    btn.closest('.notification-item').removeClass('bg-light');
                    btn.remove();
                    
                    // Update mark all button visibility
                    let unreadCount = $('.notification-item.bg-light').length;
                    if(unreadCount === 0) {
                        $('.mark-all-read').hide();
                    }
                    
                    // Remove any existing success messages in notifications card
                    $('.col-md-5 .alert-success').remove();
                }
            },
            error: function(xhr) {
                console.log('Failed to mark notification as read:', xhr.responseText);
            }
        });
    });
    
    // Mark all notifications as read
    $('.mark-all-read').click(function(e) {
        e.preventDefault();
        let btn = $(this);
        
        // Remove any existing success messages in notifications card only
        $('.col-md-5 .alert-success').remove();
        
        btn.prop('disabled', true).text('Marking...');
        
        $.ajax({
            url: '{{ url("intern/notifications/mark-all-read") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    $('.notification-item').removeClass('bg-light');
                    $('.mark-read').remove();
                    btn.hide();
                    
                    // Show success message ONLY in notifications card
                    let successMsg = $('<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 1rem;">' +
                        '<i class="ti ti-check-circle me-2"></i>All notifications marked as read!' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    
                    // Add to notifications card body (col-md-5 .card-body)
                    $('.col-md-5 .card-body').prepend(successMsg);
                    
                    // Auto remove after 3 seconds
                    setTimeout(function() {
                        successMsg.fadeOut(500, function() {
                            successMsg.remove();
                        });
                    }, 3000);
                }
            },
            error: function(xhr) {
                console.log('Failed to mark all notifications as read:', xhr.responseText);
                btn.prop('disabled', false).text('Mark all read');
            }
        });
    });
});
</script>
@endpush