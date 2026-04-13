@extends('layouts/layoutMaster')

@section('title', 'Edit Evaluation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Edit Evaluation for {{ $intern->name }}</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('supervisor.evaluations.update', $evaluation->id) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Month</label>
                        <input type="month" name="month" class="form-control" value="{{ $evaluation->month }}" required>
                    </div>

                    {{-- <div class="col-md-6">
                        <label class="form-label">Overall Score (0-10)</label>
                        <input type="number" name="overall_score" class="form-control" min="0" max="10" value="{{ $evaluation->overall_score }}" required>
                    </div> --}}


                    <div class="col-md-3">
                        <label class="form-label">Technical Skills (0-10)</label>
                        <input type="number" name="technical_skills" class="form-control" min="0" max="10" value="{{ $evaluation->technical_skills }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Problem Solving (0-10)</label>
                        <input type="number" name="problem_solving" class="form-control" min="0" max="10" value="{{ $evaluation->problem_solving }}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Communication (0-10)</label>
                        <input type="number" name="communication" class="form-control" min="0" max="10" value="{{ $evaluation->communication }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Task Completion</label>
                        <input type="text" class="form-control" value="{{ $evaluation->task_completion }}/10" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Overall Score</label>
                        <input type="text" class="form-control" value="{{ $evaluation->overall_score }}/10" readonly>
                    </div>

                    {{-- <div class="col-md-3">
                        <label class="form-label">Professionalism (0-10)</label>
                        <input type="number" name="professionalism" class="form-control" min="0" max="10" value="{{ $evaluation->professionalism }}" required>
                    </div> --}}

                    <div class="col-12">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="4">{{ $evaluation->remarks }}</textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Update Evaluation</button>
                        <a href="{{ route('supervisor.evaluations.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
