@extends('layouts/layoutMaster')

@section('title', 'Intern Profile')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Intern Profile: {{ $intern->name }}</h4>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row">
        <!-- Intern Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ $intern->image ?? asset('assets/img/avatars/1.png') }}" alt="avatar" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h5>{{ $intern->name }}</h5>
                    <p class="text-muted">{{ $intern->int_technology }} Intern</p>
                    <span class="badge bg-label-primary">{{ $intern->int_status }}</span>
                </div>
                <hr class="my-0">
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $intern->email }}</p>
                    <p><strong>Phone:</strong> {{ $intern->phone }}</p>
                    <p><strong>Type:</strong> <span class="badge bg-label-info">{{ $intern->internship_type ?? 'Remote' }}</span></p>
                    <p><strong>Start Date:</strong> {{ $intern->start_date }}</p>
                    <p><strong>ETI ID:</strong> {{ $intern->eti_id }}</p>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="col-md-8">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tasks" aria-controls="navs-tasks" aria-selected="true">Tasks</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-projects" aria-controls="navs-projects" aria-selected="false">Projects</button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-evaluations" aria-controls="navs-evaluations" aria-selected="false">Evaluations</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <!-- Tasks Tab -->
                    <div class="tab-pane fade show active" id="navs-tasks" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Score</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tasks as $task)
                                        <tr>
                                            <td>{{ $task->task_title }}</td>
                                            <td><span class="badge bg-label-info">{{ $task->task_status }}</span></td>
                                            <td>{{ $task->code_quality_score ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('supervisor.tasks.review', $task->task_id) }}" class="btn btn-sm btn-primary">Details</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center">No tasks assigned</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Projects Tab -->
                    <div class="tab-pane fade" id="navs-projects" role="tabpanel">
                         <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        <tr>
                                            <td>{{ $project->title }}</td>
                                            <td><span class="badge bg-label-success">{{ $project->pstatus }}</span></td>
                                            <td>{{ $project->obt_marks }} / {{ $project->project_marks }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">No projects assigned</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Evaluations Tab -->
                    <div class="tab-pane fade" id="navs-evaluations" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Monthly Assessments</h6>
                            <a href="{{ route('supervisor.evaluations.create', $intern->eti_id) }}" class="btn btn-sm btn-outline-primary">Add Evaluation</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Score</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($evaluations as $eval)
                                        <tr>
                                            <td>{{ $eval->month }}</td>
                                            <td><strong>{{ $eval->overall_score }}</strong>/10</td>
                                            <td>{{ $eval->remarks }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-center">No evaluations yet</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
