@extends('layouts/layoutMaster')

@section('title', 'Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="mb-4">Feedback</h4>

    {{-- ✅ ADD FEEDBACK FORM --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Add Feedback</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('supervisor.feedback.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <label>Intern (ETI ID)</label>
                        <select name="eti_id" class="form-control" required>
                            <option value="">Select Intern</option>

                            @foreach($interns as $intern)
                                <option value="{{ $intern->eti_id }}">
                                    {{ $intern->name }} ({{ $intern->eti_id }})
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Score</label>
                        <input type="number" name="score" class="form-control" min="1" max="100" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label>Remarks</label>
                    <textarea name="remarks" class="form-control" required></textarea>
                </div>

                <div class="mt-3">
                    <label>Improvement Suggestions</label>
                    <textarea name="improvement_suggestions" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">My Feedback History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Intern</th>
                        <th>Score</th>
                        <th>Remarks</th>
                        <th>Suggestions</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($feedbacks as $index => $feedback)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        {{-- Show ETI ID (or we can upgrade to name later) --}}
                        <td>
                            {{ $feedback->intern_name ?? 'N/A' }} 
                            ({{ $feedback->eti_id }})
                        </td>

                        <td>{{ $feedback->score }}</td>
                        <td>{{ $feedback->remarks }}</td>
                        <td>{{ $feedback->improvement_suggestions ?? '-' }}</td>
                        <td>{{ $feedback->created_at }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No feedback submitted yet</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

    

@endsection