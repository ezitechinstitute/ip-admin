@extends('layouts/layoutMaster')

@section('title', 'Progress Monitoring')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
<style>
    /* Styling for the table progress bars to make them look sleek */
    .progress-tracking-table .progress { background-color: rgba(115, 103, 240, 0.08); border-radius: 10px; }
    .progress-tracking-table .progress-bar { border-radius: 10px; }
    .chart-header-small { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Uniform Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Performance Monitoring</h4>
            <p class="text-muted mb-0">Analytics for <span class="fw-bold text-primary">{{ $technology }}</span> department</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <button class="btn btn-label-secondary" onclick="window.print()">
                <i class="ti ti-printer me-1"></i> Print Report
            </button>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="ti ti-refresh me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- Top Statistics Row --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="chart-header-small text-muted d-block mb-1">Avg. Progress</small>
                        <h4 class="mb-0 fw-bold text-primary">{{ round($interns->avg('progress')) }}%</h4>
                    </div>
                    <span class="badge bg-label-primary rounded p-2"><i class="ti ti-chart-pie-2 ti-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="chart-header-small text-muted d-block mb-1">Team Compliance</small>
                        <h4 class="mb-0 fw-bold text-success">{{ round($interns->avg('compliance')) }}%</h4>
                    </div>
                    <span class="badge bg-label-success rounded p-2"><i class="ti ti-discount-check ti-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card bg-label-danger border-0 h-100 shadow-none">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="chart-header-small text-danger d-block mb-1">Critical Overdue</small>
                        <h4 class="mb-0 fw-bold text-danger">{{ $interns->sum('overdue_tasks') }}</h4>
                    </div>
                    <span class="badge bg-white text-danger rounded p-2"><i class="ti ti-alert-octagon ti-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="chart-header-small text-muted d-block mb-1">Active Staff</small>
                        <h4 class="mb-0 fw-bold">{{ $interns->count() }}</h4>
                    </div>
                    <span class="badge bg-label-info rounded p-2"><i class="ti ti-users ti-md"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Analytics Row --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="card-title mb-0">Production Distribution</h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge badge-dot bg-primary"></span> <small class="text-muted">Tasks</small>
                        <span class="badge badge-dot bg-info"></span> <small class="text-muted">Projects</small>
                    </div>
                </div>
                <div class="card-body pt-4">
                    <div id="performanceCompareChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header border-bottom py-3 text-center">
                    <h5 class="card-title mb-0">Overall Team Compliance</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div id="complianceRadialChart"></div>
                    <div class="text-center mt-3">
                        <p class="text-muted small px-3">Aggregated score based on successful milestone completion vs. deadlines.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Searchable Intern Table --}}
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h5 class="card-title mb-0">Real-time Performance Logs</h5>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-merge">
                    <span class="input-group-text"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" class="form-control" placeholder="Search Intern ID or Name..." id="tableSearch">
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle progress-tracking-table">
                <thead class="table-light">
                    <tr>
                        <th>Intern</th>
                        <th>Task Progress</th>
                        <th>Project Milestone</th>
                        <th class="text-center">Quality Score</th>
                        <th>Tracking Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($interns as $intern)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($intern->name, 0, 1)) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading small">{{ $intern->name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $intern->int_technology }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="min-width: 200px;">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">{{ $intern->completed_tasks }}/{{ $intern->total_tasks }} Tasks</small>
                                <small class="fw-bold text-primary">{{ $intern->progress }}%</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar shadow-none" style="width:{{ $intern->progress }}%"></div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-heading">{{ $intern->project_completion }}%</span>
                                <small class="text-muted" title="Projects Assigned">({{ $intern->total_projects }} projects)</small>
                            </div>
                        </td>
                        <td class="text-center">
                            @php $qColor = $intern->code_quality >= 80 ? 'success' : ($intern->code_quality >= 50 ? 'warning' : 'danger'); @endphp
                            <span class="badge bg-label-{{ $qColor }} rounded-pill">{{ $intern->code_quality }}%</span>
                        </td>
                        <td>
                            @if($intern->overdue_tasks > 0)
                                <span class="badge bg-label-danger">
                                    <i class="ti ti-alert-triangle ti-xs me-1"></i> {{ $intern->overdue_tasks }} Overdue
                                </span>
                            @else
                                <span class="badge bg-label-success">On Track</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-icon btn-sm btn-label-secondary" title="View Profile">
                                    <i class="ti ti-user"></i>
                                </a>
                                <a href="{{ route('supervisor.evaluations.create', $intern->eti_id) }}" class="btn btn-icon btn-sm btn-label-success" title="Submit Review">
                                    <i class="ti ti-star"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark-style');
    const labelColor = isDark ? '#b6bee3' : '#6f6b7d';
    const borderColor = isDark ? '#3b3e59' : '#e9ebeb';

    // 1. Productivity Comparison (Bar Chart)
    const perfChartEl = document.querySelector('#performanceCompareChart');
    if (perfChartEl) {
        const perfOptions = {
            series: [{
                name: 'Tasks',
                data: [@foreach($interns as $intern) {{ $intern->progress }}, @endforeach]
            }, {
                name: 'Projects',
                data: [@foreach($interns as $intern) {{ $intern->project_completion }}, @endforeach]
            }],
            chart: { height: 350, type: 'bar', toolbar: { show: false } },
            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4, dataLabels: { position: 'top' } } },
            colors: ['#7367f0', '#00bad1'],
            dataLabels: { enabled: false },
            grid: { borderColor: borderColor, padding: { top: -20, bottom: -10 } },
            xaxis: {
                categories: [@foreach($interns as $intern) '{{ explode(' ', $intern->name)[0] }}', @endforeach],
                labels: { style: { colors: labelColor } },
                axisBorder: { show: false }
            },
            yaxis: { labels: { style: { colors: labelColor } }, max: 100 },
            legend: { position: 'top', horizontalAlign: 'right', labels: { colors: labelColor } }
        };
        new ApexCharts(perfChartEl, perfOptions).render();
    }

    // 2. Compliance Semi-Circle Gauge
    const compChartEl = document.querySelector('#complianceRadialChart');
    if (compChartEl) {
        const compOptions = {
            series: [{{ round($interns->avg('compliance')) }}],
            chart: { height: 320, type: 'radialBar', sparkline: { enabled: true } },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: { size: '65%' },
                    track: { background: isDark ? '#3b3e59' : '#f0f2f8', strokeWidth: '97%' },
                    dataLabels: {
                        name: { show: false },
                        value: { offsetY: -2, fontSize: '32px', color: isDark ? '#fff' : '#444', fontWeight: '700' }
                    }
                }
            },
            colors: ['#28c76f'],
            labels: ['Team Compliance']
        };
        new ApexCharts(compChartEl, compOptions).render();
    }
});

// Simple Table Filter logic for Big Data
document.getElementById('tableSearch').addEventListener('keyup', function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll('.progress-tracking-table tbody tr');
    rows.forEach(row => {
        row.style.display = row.innerText.toLowerCase().indexOf(value) > -1 ? '' : 'none';
    });
});
</script>
@endsection