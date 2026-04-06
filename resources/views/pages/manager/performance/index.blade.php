@extends('layouts.layoutMaster')

@section('title', 'Performance Analytics')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="mb-4">📊 Intern Performance Analytics</h4>

    <!-- KPI CARDS -->
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Total Interns</h6>
                <h3>{{ count($data) }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Average Performance</h6>
                <h3>
                    {{ round(collect($data)->avg('overall'), 2) }}%
                </h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Top Performers</h6>
                <h3>
                    {{ collect($data)->where('overall', '>=', 80)->count() }}
                </h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center p-3">
                <h6>Low Performers</h6>
                <h3>
                    {{ collect($data)->where('overall', '<', 50)->count() }}
                </h3>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="card">
        <div class="card-header">
            <h5>Intern Performance Details</h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover text-nowrap">

                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Intern</th>
                        <th>Technology</th>
                        <th>Task Completion</th>
                        <th>Deadline</th>
                        <th>Quality</th>
                        <th>Attendance</th>
                        <th>Overall</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($data as $index => $intern)

                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            <strong>{{ $intern->name }}</strong><br>
                            <small>{{ $intern->email }}</small>
                        </td>

                        <td>{{ $intern->technology }}</td>

                        <!-- Task Completion -->
                        <td>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success"
                                     style="width: {{ $intern->task_completion }}%">
                                </div>
                            </div>
                            <small>{{ $intern->task_completion }}%</small>
                        </td>

                        <!-- Deadline -->
                        <td>
                            <span class="badge bg-warning">
                                {{ $intern->deadline }}%
                            </span>
                        </td>

                        <!-- Quality -->
                        <td>
                            <span class="badge bg-info">
                                {{ $intern->quality }}%
                            </span>
                        </td>

                        <!-- Attendance -->
                        <td>
                            <span class="badge bg-success">
                                {{ $intern->attendance }}%
                            </span>
                        </td>

                        <!-- Overall -->
                        <td>
                            <span class="badge 
                                @if($intern->overall >= 80) bg-success
                                @elseif($intern->overall >= 50) bg-warning
                                @else bg-danger
                                @endif
                            ">
                                {{ $intern->overall }}%
                            </span>
                        </td>

                        <!-- ACTION -->
                        <td>
                            <a href="{{ url('manager/performance-analytics/'.$intern->id) }}"
                               class="btn btn-sm btn-primary">
                                View Details
                            </a>
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="9" class="text-center">
                            No data found
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection