@extends('layouts/layoutMaster')

@section('title', 'Review Task')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Review Task: {{ $task->task_title }}</h4>

    <div class="row">
        <!-- Submission Details -->
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Intern Submission</h5>
                </div>
                <div class="card-body">
                    <p><strong>Intern:</strong> {{ $task->intern_name }}</p>
                    <p><strong>Description:</strong><br>{{ $task->task_description }}</p>
                    <hr>
                    <h6>Submission Content:</h6>
                    <p>{{ $task->submit_description ?? 'No description provided' }}</p>
                    
                    @if($task->task_git_url)
                        <p><strong>Git URL:</strong> <a href="{{ $task->task_git_url }}" target="_blank">{{ $task->task_git_url }}</a></p>
                    @endif

                    @if($task->task_live_url)
                        <p><strong>Live URL:</strong> <a href="{{ $task->task_live_url }}" target="_blank">{{ $task->task_live_url }}</a></p>
                    @endif

                    @if($task->task_screenshot)
                        <div class="mt-3">
                            <strong>Screenshot:</strong><br>
                            <img src="{{ asset('storage/' . $task->task_screenshot) }}" class="img-fluid rounded mt-2" alt="Screenshot">
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Assessment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('supervisor.tasks.update', $task->task_id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Decision</label>
                            <select name="task_approve" class="form-select" required>
                                <option value="1">Approve (Mark as Completed)</option>
                                <option value="2">Reject (Needs Revision)</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Obtained Points</label>
                                <input type="number" name="task_obt_points" class="form-control" max="{{ $task->task_points }}" value="{{ $task->task_points }}">
                                <small class="text-muted">Total: {{ $task->task_points }}</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code Quality (1-10)</label>
                                <input type="number" name="code_quality_score" class="form-control" min="1" max="10" value="8">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Review Remarks</label>
                            <textarea name="remarks" class="form-control" rows="4" placeholder="Feedback for the intern..."></textarea>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="penalty_flag" class="form-check-input" value="1" id="penalty">
                            <label class="form-check-label" for="penalty">Apply Penalty Flag</label>
                            <small class="d-block text-muted">Flag for poor quality or late submission.</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success w-100">Submit Review</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
