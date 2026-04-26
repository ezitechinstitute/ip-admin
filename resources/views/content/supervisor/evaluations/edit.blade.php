@extends('layouts/layoutMaster')

@section('title', 'Edit Evaluation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Uniform Page Header --}}
    <h4 class="fw-bold mb-4">
        <span class="text-muted fw-light">Evaluations /</span> Edit Record
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                {{-- Header matching Project Blade style --}}
                <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="mb-0">Evaluation Details for {{ $intern->name }}</h5>
                    <span class="badge bg-label-secondary">{{ $intern->eti_id }}</span>
                </div>
                
                <div class="card-body pt-4">
                    <form action="{{ route('supervisor.evaluations.update', $evaluation->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            {{-- Evaluation Period --}}
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Evaluation Month</label>
                                <input type="month" name="month" class="form-control" value="{{ $evaluation->month }}" required>
                            </div>

                            {{-- Skill Metrics Row --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Technical Skills (0-10)</label>
                                <input type="number" name="technical_skills" class="form-control" min="0" max="10" step="0.1" value="{{ $evaluation->technical_skills }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Problem Solving (0-10)</label>
                                <input type="number" name="problem_solving" class="form-control" min="0" max="10" step="0.1" value="{{ $evaluation->problem_solving }}" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Communication (0-10)</label>
                                <input type="number" name="communication" class="form-control" min="0" max="10" step="0.1" value="{{ $evaluation->communication }}" required>
                            </div>

                            {{-- Readonly Calculated Row --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Task Completion (Readonly)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" value="{{ $evaluation->task_completion }}/10" readonly>
                                    <span class="input-group-text"><i class="ti ti-lock-square-rounded"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Overall Calculated Score</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light fw-bold text-primary" value="{{ number_format($evaluation->overall_score, 2) }}/10" readonly>
                                    <span class="input-group-text"><i class="ti ti-calculator"></i></span>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="4" placeholder="Enter your feedback...">{{ $evaluation->remarks }}</textarea>
                            </div>

                            {{-- Actions --}}
                            <div class="col-12 mt-4">
                                <hr>
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti ti-device-floppy me-1"></i> Update Evaluation
                                </button>
                                <a href="{{ route('supervisor.evaluations.index') }}" class="btn btn-label-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection