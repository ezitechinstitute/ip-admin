@extends('layouts.layoutMaster')

@section('title', 'Intern Performance Detail')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <!-- BACK BUTTON -->
    <a href="{{ url('manager/performance-analytics') }}" class="btn btn-secondary mb-3">
        ← Back
    </a>

    <!-- INTERN INFO -->
    <div class="card p-3 mb-4">
        <h4>{{ $intern->name }}</h4>
        <p>{{ $intern->email }} | {{ $intern->technology }}</p>
    </div>

    <!-- METRICS -->
    <div class="row">
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Task Completion</h6>
                <h3>{{ $intern->task_completion }}%</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Deadline Adherence</h6>
                <h3>{{ $intern->deadline }}%</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Quality Score</h6>
                <h3>{{ $intern->quality }}%</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Attendance</h6>
                <h3>{{ $intern->attendance }}%</h3>
            </div>
        </div>
    </div>

    <!-- OVERALL SCORE -->
    <div class="card mt-4 text-center p-4">
        <h4>Overall Performance</h4>
        <h1 class="
            @if($intern->overall >= 80) text-success
            @elseif($intern->overall >= 50) text-warning
            @else text-danger
            @endif
        ">
            {{ $intern->overall }}%
        </h1>
    </div>

    <!-- CHARTS -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="text-center mb-3">Performance Breakdown</h6>
                <div style="position: relative; height: 300px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="text-center mb-3">Overall Score</h6>
                <div style="position: relative; height: 300px;">
                    <canvas id="overallChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        const completion = {{ $intern->task_completion ?? 80 }};
        const deadline   = {{ $intern->deadline ?? 70 }};
        const quality    = {{ $intern->quality ?? 85 }};
        const attendance = {{ $intern->attendance ?? 90 }};
        const overall    = {{ $intern->overall ?? 82 }};

        // BAR CHART
        new Chart(document.getElementById('performanceChart'), {
            type: 'bar',
            data: {
                labels: ['Task', 'Deadline', 'Quality', 'Attendance'],
                datasets: [{
                    label: 'Performance (%)',
                    data: [completion, deadline, quality, attendance],
                    backgroundColor: ['#696cff', '#03c3ec', '#71dd37', '#ffab00']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { min: 0, max: 100 }
                }
            }
        });

        // DONUT CHART
        new Chart(document.getElementById('overallChart'), {
            type: 'doughnut',
            data: {
                labels: ['Achieved', 'Remaining'],
                datasets: [{
                    data: [overall, 100 - overall],
                    backgroundColor: ['#71dd37', '#e0e0e0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

    });
</script>
@endsection