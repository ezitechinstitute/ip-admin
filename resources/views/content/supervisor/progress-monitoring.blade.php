@extends('layouts/layoutMaster')

@section('title', 'Progress Monitoring')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
<style>
    /* Ensure the dashboard feels tight and professional */
    .kpi-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
    .table-responsive { max-height: 450px; } /* Keeps table from pushing content off-screen */
    .progress-tracking-table .progress { background-color: rgba(115, 103, 240, 0.08); height: 8px; border-radius: 10px; }
</style>
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-3 mb-4">
        {{-- 1. Total Interns (Mini Area Chart) --}}
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body pb-2">
                    <span class="kpi-label text-muted d-block mb-1">Total Active Interns</span>
                    <div class="d-flex align-items-center">
                        <h3 class="mb-0 fw-bold me-2">{{ $interns->count() }}</h3>
                        <span class="badge bg-label-info rounded-pill p-1"><i class="ti ti-users ti-xs"></i></span>
                    </div>
                    <div id="totalInternsChart"></div> {{-- Visual sparkline --}}
                </div>
            </div>
        </div>

        {{-- 2. Overdue Tasks (Radial Gauge) --}}
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-2">
                    <div class="content-left">
                        <span class="kpi-label text-danger d-block mb-1">Critical Overdue</span>
                        <h3 class="mb-0 fw-bold text-danger">{{ $interns->sum('overdue_tasks') }}</h3>
                        <small class="text-muted">Requires Action</small>
                    </div>
                    <div id="overdueRadialChart"></div>
                </div>
            </div>
        </div>

        {{-- 3. Deadline Compliance (Radial Gauge) --}}
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-2">
                    <div class="content-left">
                        <span class="kpi-label text-success d-block mb-1">Deadline Compliance</span>
                        <h3 class="mb-0 fw-bold text-success">{{ round($interns->avg('compliance')) }}%</h3>
                        <small class="text-muted">On-time rate</small>
                    </div>
                    <div id="complianceRadialChart"></div>
                </div>
            </div>
        </div>

        {{-- 4. Avg. Completion Rate (Radial Gauge) --}}
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between py-2">
                    <div class="content-left">
                        <span class="kpi-label text-primary d-block mb-1">Avg. Completion</span>
                        <h3 class="mb-0 fw-bold text-primary">{{ round($interns->avg('progress')) }}%</h3>
                        <small class="text-muted">Task Roadmap</small>
                    </div>
                    <div id="completionRadialChart"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: The Table --}}
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap py-3">
            <h5 class="card-title mb-0">Intern Performance Tracking</h5>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-merge input-group-sm">
                    <span class="input-group-text"><i class="ti ti-search"></i></span>
                    <input type="text" class="form-control" placeholder="Search..." id="tableSearch">
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle progress-tracking-table">
                <thead class="table-light">
                    <tr>
                        <th>Intern</th>
                        <th style="width: 250px;">Progress Roadmap</th>
                        <th class="text-center">Compliance</th>
                        <th class="text-center">Quality</th>
                        <th>Alerts</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($interns as $intern)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-xs me-2">
                                    <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($intern->name, 0, 1)) }}</span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-heading small">{{ $intern->name }}</span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $intern->int_technology }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-between mb-1">
                                <small class="text-muted">{{ $intern->completed_tasks }}/{{ $intern->total_tasks }} Tasks</small>
                                <small class="fw-bold text-primary">{{ $intern->progress }}%</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar shadow-none" style="width:{{ $intern->progress }}%"></div>
                            </div>
                        </td>
                        <td class="text-center fw-bold">{{ $intern->compliance }}%</td>
                        <td class="text-center">
                            @php $qColor = $intern->code_quality >= 80 ? 'success' : ($intern->code_quality >= 50 ? 'warning' : 'danger'); @endphp
                            <span class="badge badge-sm bg-label-{{ $qColor }}">{{ $intern->code_quality }}</span>
                        </td>
                        <td>
                            @if($intern->overdue_tasks > 0)
                                <span class="badge bg-label-danger">! {{ $intern->overdue_tasks }} Overdue</span>
                            @else
                                <span class="badge bg-label-success">On Track</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-icon btn-sm btn-label-secondary shadow-none"><i class="ti ti-user"></i></a>
                                <a href="{{ route('supervisor.evaluations.create', $intern->eti_id) }}" class="btn btn-icon btn-sm btn-label-success shadow-none"><i class="ti ti-star"></i></a>
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
    
    // Configuration for Radial Gauges to keep them small and fitting
    const radialConfig = (color, value) => ({
        series: [value],
        chart: { height: 90, width: 90, type: 'radialBar' },
        plotOptions: {
            radialBar: {
                hollow: { size: '50%' },
                track: { background: isDark ? '#3b3e59' : '#f0f2f8' },
                dataLabels: {
                    name: { show: false },
                    value: { offsetY: 5, fontSize: '14px', fontWeight: '600', color: isDark ? '#fff' : '#444' }
                }
            }
        },
        colors: [color],
        stroke: { lineCap: 'round' }
    });

    new ApexCharts(document.querySelector('#overdueRadialChart'), radialConfig('#ea5455', {{ ($interns->sum('overdue_tasks') / ($interns->sum('total_tasks') ?: 1)) * 100 }})).render();
    new ApexCharts(document.querySelector('#complianceRadialChart'), radialConfig('#28c76f', {{ round($interns->avg('compliance')) }})).render();
    new ApexCharts(document.querySelector('#completionRadialChart'), radialConfig('#7367f0', {{ round($interns->avg('progress')) }})).render();

    // Total Interns Mini Chart
    new ApexCharts(document.querySelector('#totalInternsChart'), {
        series: [{ data: [10, 25, 18, 35, 20, 45, 30] }],
        chart: { height: 40, type: 'area', sparkline: { enabled: true } },
        stroke: { curve: 'smooth', width: 2 },
        fill: { opacity: 0.1 },
        colors: ['#00bad1']
    }).render();

    // Table Filter
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.progress-tracking-table tbody tr');
        rows.forEach(row => { row.style.display = row.innerText.toLowerCase().indexOf(value) > -1 ? '' : 'none'; });
    });
});
</script>
@endsection