@extends('layouts/layoutMaster')

@section('title', 'Edit Project')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Edit Project</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('supervisor.projects.update', $project->project_id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Project Title</label>
                        <input type="text" name="title" class="form-control" value="{{ $project->title }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Technology Stack</label>
                        <input type="text" name="tech_stack" class="form-control" value="{{ $project->tech_stack }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Difficulty Level</label>
                        <select name="difficulty_level" class="form-select" required>
                            <option value="Beginner" {{ $project->difficulty_level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="Intermediate" {{ $project->difficulty_level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="Advanced" {{ $project->difficulty_level == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $project->start_date }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $project->end_date }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Duration</label>
                        <input type="number" name="duration" class="form-control" value="{{ $project->duration }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Days</label>
                        <input type="number" name="days" class="form-control" value="{{ $project->days }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Project Marks</label>
                        <input type="text" name="project_marks" class="form-control" value="{{ $project->project_marks }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Obtained Marks</label>
                        <input type="number" step="0.01" name="obt_marks" class="form-control" value="{{ $project->obt_marks }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="pstatus" class="form-select" required>
                            <option value="Ongoing" {{ $project->pstatus == 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="Submitted" {{ $project->pstatus == 'Submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="Approved" {{ $project->pstatus == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ $project->pstatus == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="Expired" {{ $project->pstatus == 'Expired' ? 'selected' : '' }}>Expired</option>
                            <option value="Completed" {{ $project->pstatus == 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Pending" {{ $project->pstatus == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ $project->description }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Update Project</button>
                        <a href="{{ route('supervisor.projects') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
