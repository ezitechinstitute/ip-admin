@extends('layouts/layoutMaster')

@section('title', 'Submit Evaluation')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Submit Evaluation for {{ $intern->name }}</h4>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('supervisor.evaluations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="eti_id" value="{{ $intern->eti_id }}">
                
                <div class="mb-3">
                    <label class="form-label">Month of Assessment</label>
                    <input type="month" name="month" class="form-control" value="{{ date('Y-m') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Technical Skills (0-10)</label>
                        <input type="number" name="technical_skills" class="form-control" min="0" max="10" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Problem Solving (0-10)</label>
                        <input type="number" name="problem_solving" class="form-control" min="0" max="10" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Communication (0-10)</label>
                        <input type="number" name="communication" class="form-control" min="0" max="10" required>
                    </div>
                    {{-- <div class="col-md-6 mb-3">
                        <label class="form-label">Professionalism (0-10)</label>
                        <input type="number" name="professionalism" class="form-control" min="0" max="10" required>
                    </div> --}}
                </div>
                <div class="mb-3">
                    <label class="form-label">Task Completion</label>
                    <input type="text" class="form-control" value="Auto calculated from tasks" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Overall Score</label>
                    <input type="text" class="form-control" value="Auto calculated" disabled>
                </div>

                {{-- <div class="mb-3">
                    <label class="form-label">Overall Score (0-10)</label>
                    <input type="number" name="overall_score" class="form-control" min="0" max="10" required>
                </div> --}}

                <div class="mb-3">
                    <label class="form-label">Detailed Remarks / Feedback</label>
                    <textarea name="remarks" class="form-control" rows="5" placeholder="Provide detailed feedback..."></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Submit Evaluation</button>
                    <a href="{{ route('supervisor.viewIntern', $intern->int_id) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
