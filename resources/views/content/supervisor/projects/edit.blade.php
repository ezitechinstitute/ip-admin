@extends('layouts/layoutMaster')

@section('title', 'Edit Project')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('supervisor.projects') }}" class="btn btn-label-secondary btn-icon me-3">
            <i class="ti ti-arrow-left"></i>
        </a>
        <h4 class="fw-bold mb-0">Edit Project: <span class="text-muted fw-light">{{ $project->title }}</span></h4>
    </div>

    <div class="row">
        {{-- Main Edit Form --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Project Details</h5>
                </div>
                <div class="card-body pt-4">
                    <form action="{{ route('supervisor.projects.update', $project->project_id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            {{-- Project Title --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Project Title</label>
                                <input type="text" name="title" class="form-control" value="{{ $project->title }}" required>
                            </div>

                            {{-- Tech Stack --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Technology Stack</label>
                                <input type="text" name="tech_stack" class="form-control" value="{{ $project->tech_stack }}" placeholder="e.g. Laravel, React">
                            </div>

                            {{-- Difficulty --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Difficulty Level</label>
                                <select name="difficulty_level" class="form-select" required>
                                    <option value="Beginner" {{ $project->difficulty_level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="Intermediate" {{ $project->difficulty_level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="Advanced" {{ $project->difficulty_level == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>

                            {{-- Dates --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $project->start_date }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $project->end_date }}" required>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="pstatus" class="form-select" required>
                                    @foreach(['Pending', 'Ongoing', 'Submitted', 'Completed', 'Approved', 'Rejected', 'Expired'] as $status)
                                        <option value="{{ $status }}" {{ $project->pstatus == $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Description --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description / Requirements</label>
                                <textarea name="description" class="form-control" rows="6" required>{{ $project->description }}</textarea>
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="col-12 text-end mt-4">
                                <hr>
                                <a href="{{ route('supervisor.projects') }}" class="btn btn-label-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Assigned To</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-2">
                            <span class="avatar-initial rounded-circle bg-label-primary">{{ strtoupper(substr($project->email, 0, 1)) }}</span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $project->eti_id }}</h6>
                            <small class="text-muted">{{ $project->email }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Current Status:</span>
                                @php
                                    $statusClass = match(strtolower($project->pstatus)) {
                                        'ongoing' => 'primary',
                                        'completed', 'approved' => 'success',
                                        'pending', 'submitted' => 'warning',
                                        'rejected', 'expired' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-label-{{ $statusClass }}">{{ $project->pstatus }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Project ID:</span>
                                <span>#{{ $project->project_id }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-semibold me-1">Assigned By:</span>
                                <span>{{ $project->supervisor_name ?? 'Current Supervisor' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Task Quick Access --}}
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Quick Actions</h6>
                    <a href="{{ route('supervisor.projects.tasks', $project->project_id) }}" class="btn btn-label-primary w-100 mb-2">
                        <i class="ti ti-list-check me-1"></i> Manage Tasks
                    </a>
                    @if($project->chat ?? null)
                    <a href="{{ route('chat.show', $project->project_id) }}" class="btn btn-label-success w-100">
                        <i class="ti ti-message-dots me-1"></i> Go to Project Chat
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection