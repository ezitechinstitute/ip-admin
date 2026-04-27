@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Supervisor Dashboard')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/swiper/swiper.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/cards-advance.scss')
<style>
  .custom-scrollbar::-webkit-scrollbar { width: 4px; }
  .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
  .custom-scrollbar::-webkit-scrollbar-thumb { background: #dbdade; border-radius: 10px; }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/swiper/swiper.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y pt-0">
  <div class="row g-6">
    
    <div class="col-xl-4 col-lg-5 col-md-12">
      <div class="card h-100 border-0 shadow-sm">
        <div class="d-flex align-items-end row">
          <div class="col-7">
            <div class="card-body text-nowrap">
              <h5 class="card-title mb-0">Welcome Back, {{ explode(' ', auth('manager')->user()->name)[0] }}! 👋</h5>
              <p class="mb-2">Productivity is up <strong>12%</strong></p>
              <h4 class="text-primary mb-1">{{ $activeInterns }}</h4>
              <p class="mb-4">Active Interns under you</p>
              <a href="{{ route('supervisor.myInterns') }}" class="btn btn-sm btn-primary">View My Team</a>
            </div>
          </div>
          <div class="col-5 text-center">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140" alt="dashboard image" />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 col-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between pb-0">
          <div class="card-title mb-0">
            <h5 class="mb-1">Task Performance</h5>
            <p class="card-subtitle small">Review vs Completion</p>
          </div>
        </div>
        <div class="card-body">
          <div id="salesDiamondChart"></div>
          <div class="d-flex align-items-center justify-content-center mt-1">
            <div class="d-flex align-items-center me-4">
              <span class="badge badge-dot bg-primary me-2"></span>
              <small>Done</small>
            </div>
            <div class="d-flex align-items-center">
              <span class="badge badge-dot bg-info me-2"></span>
              <small>Pending</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-md-6 col-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Hiring Success</h5>
          <span class="badge bg-label-success rounded-pill">Real-time</span>
        </div>
        <div class="card-body pt-2">
          <div class="row g-2 mb-4">
            <div class="col-6 border-end">
              <h5 class="mb-0">{{ $interviewCount }}</h5>
              <small class="text-muted">Interviews</small>
            </div>
            <div class="col-6 text-end">
              <h5 class="mb-0 text-success">{{ $completedCount }}</h5>
              <small class="text-muted">Selected</small>
            </div>
          </div>
          <div id="lifecycleRadialChart"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 text-uppercase small text-muted fw-bold">Daily Workload Distribution</h5>
          <span class="badge bg-label-primary">Last 7 Days</span>
        </div>
        <div class="card-body">
          <div id="taskBarChart"></div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="mb-0">Workload Health</h5>
        </div>
        <div class="card-body">
          <div id="taskStatusDonutChart"></div>
          <ul class="p-0 m-0 mt-4">
            <li class="d-flex mb-3 align-items-center">
              <span class="badge badge-dot bg-success me-2"></span>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <p class="mb-0 text-muted small">Completed Today</p>
                <span class="fw-medium">{{ $tasksCompletedToday }}</span>
              </div>
            </li>
            <li class="d-flex mb-3 align-items-center">
              <span class="badge badge-dot bg-warning me-2"></span>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <p class="mb-0 text-muted small">Pending Review</p>
                <span class="fw-medium">{{ $pendingTaskReviews }}</span>
              </div>
            </li>
            <li class="d-flex align-items-center">
              <span class="badge badge-dot bg-danger me-2"></span>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <p class="mb-0 text-muted small">Overdue</p>
                <span class="fw-medium">{{ $overdueTasks }}</span>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 d-flex align-items-center">
             <i class="ti tabler-bell-ringing me-2 text-primary ti-md"></i> System Notifications
          </h5>
        </div>
        <div class="card-body custom-scrollbar" style="max-height: 480px; overflow-y: auto;">
          <ul class="list-group list-group-flush">
            @forelse($notifications as $notif)
              @php
                $color = 'primary'; $icon = 'bell';
                if(str_contains(strtolower($notif->type), 'urgent')) { $color = 'danger'; $icon = 'alert-triangle'; }
                elseif(str_contains(strtolower($notif->type), 'task')) { $color = 'info'; $icon = 'clipboard-check'; }
              @endphp
              <li class="list-group-item list-group-item-action border-0 d-flex align-items-start p-4 mb-2 rounded bg-label-hover-light">
                <div class="avatar flex-shrink-0 me-3">
                  <span class="avatar-initial rounded bg-label-{{ $color }}"><i class="ti tabler-{{ $icon }} ti-sm"></i></span>
                </div>
                <div class="d-flex flex-column w-100">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="mb-0 fw-bold small text-uppercase">{{ $notif->type }}</h6>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</small>
                  </div>
                  <p class="mb-2 small text-heading">{{ $notif->message }}</p>
                </div>
              </li>
            @empty
              <li class="list-group-item border-0 text-center py-5"><p class="text-muted small">No notifications.</p></li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

    <div class="col-xl-6 col-lg-12">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0 text-uppercase small text-muted fw-bold">Recent Activity Log</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive text-nowrap">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="ps-0 border-top-0">Action</th>
                  <th class="border-top-0">Description</th>
                  <th class="border-top-0 text-end">Time</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activityLogs as $log)
                  <tr>
                    <td class="ps-0"><span class="badge bg-label-secondary text-capitalize">{{ $log->action }}</span></td>
                    <td class="small text-truncate" style="max-width: 200px;">{{ $log->details }}</td>
                    <td class="text-end small text-muted">{{ \Carbon\Carbon::parse($log->created_at)->shortRelativeDiffForHumans() }}</td>
                  </tr>
                @empty
                  <tr><td colspan="3" class="text-center text-muted py-5">No activity recorded.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark-style');
    const labelColor = isDark ? '#b6bee3' : '#a1acb8';

    // 1. Diamond Radar Chart (Fixed)
    const diamondEl = document.querySelector('#salesDiamondChart');
    if (diamondEl) {
        const diamondOptions = {
            series: [{
                name: 'Tasks',
                data: [{{ $tasksCompletedToday }}, {{ $pendingTaskReviews }}, {{ $tasksCompletedToday }}, {{ $pendingTaskReviews }}]
            }],
            chart: { height: 280, type: 'radar', toolbar: { show: false } },
            plotOptions: {
                radar: {
                    polygons: {
                        strokeColors: isDark ? '#3b3e59' : '#f0f2f8',
                        connectorColors: isDark ? '#3b3e59' : '#f0f2f8',
                        fill: { colors: [isDark ? '#2f3349' : '#f8f9fa', 'transparent'] }
                    }
                }
            },
            xaxis: {
                categories: ['Done', 'Pending', 'Submits', 'Reviews'],
                labels: { show: true, style: { fontSize: '11px', colors: [labelColor, labelColor, labelColor, labelColor] } }
            },
            yaxis: { show: false, min: 0, max: Math.max({{ $tasksCompletedToday }}, {{ $pendingTaskReviews }}, 1) + 2 },
            colors: ['#7367f0'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.8, opacityTo: 0.5, stops: [0, 100] } },
            grid: { show: false }
        };
        new ApexCharts(diamondEl, diamondOptions).render();
    }

    // 2. Lifecycle Radial Chart
    const radialEl = document.querySelector('#lifecycleRadialChart');
    if (radialEl) {
        const radialOptions = {
            series: [{{ $completedCount > 0 ? 85 : 0 }}],
            chart: { height: 200, type: 'radialBar', sparkline: { enabled: true } },
            plotOptions: {
                radialBar: {
                    hollow: { size: '70%' },
                    track: { background: isDark ? '#3b3e59' : '#f0f2f8' },
                    dataLabels: {
                        name: { show: true, color: labelColor, fontSize: '13px', offsetY: -10 },
                        value: { show: true, color: isDark ? '#fff' : '#444', fontSize: '20px', offsetY: 5, formatter: (val) => val + '%' }
                    }
                }
            },
            labels: ['Success Rate'],
            colors: ['#7367f0']
        };
        new ApexCharts(radialEl, radialOptions).render();
    }

    // 3. Workload Bar Chart
    const taskBarEl = document.querySelector('#taskBarChart');
    if (taskBarEl) {
        const barOptions = {
            series: [{
                name: 'Tasks',
                data: [{{ $tasksCompletedToday }}, {{ $pendingTaskReviews }}, {{ $overdueTasks }}, {{ $totalProjectsAssigned }}]
            }],
            chart: { height: 280, type: 'bar', toolbar: { show: false } },
            plotOptions: { bar: { columnWidth: '30%', borderRadius: 4, distributed: true } },
            dataLabels: { enabled: false },
            colors: ['#28c76f', '#ff9f43', '#ea5455', '#7367f0'],
            xaxis: {
                categories: ['Completed', 'Pending', 'Overdue', 'Projects'],
                labels: { style: { colors: labelColor } }
            }
        };
        new ApexCharts(taskBarEl, barOptions).render();
    }

    // 4. Task Status Donut
    const donutEl = document.querySelector('#taskStatusDonutChart');
    if (donutEl) {
        const donutOptions = {
            series: [{{ $tasksCompletedToday }}, {{ $pendingTaskReviews }}, {{ $overdueTasks }}],
            chart: { height: 200, type: 'donut' },
            labels: ['Completed', 'Pending', 'Overdue'],
            colors: ['#28c76f', '#ff9f43', '#ea5455'],
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'Workload', color: labelColor } } } } }
        };
        new ApexCharts(donutEl, donutOptions).render();
    }
});
</script>
@endpush