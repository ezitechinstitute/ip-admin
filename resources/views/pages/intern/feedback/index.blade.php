@extends('layouts/layoutMaster')

@section('title', 'Feedback')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Submit Feedback / Complaint</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('intern.feedback.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="general">General Feedback</option>
                                <option value="technical">Technical Issue</option>
                                <option value="supervisor">Supervisor Related</option>
                                <option value="complaint">Complaint</option>
                                <option value="suggestion">Suggestion</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-1"></i> Submit Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Feedback History</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Feedback</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feedbacks as $feedback)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($feedback->created_at)->format('d M, Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($feedback->feedback_text, 80) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $feedback->status == 'Resolved' ? 'success' : 'warning' }}">
                                            {{ $feedback->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="ti ti-message-circle-off ti-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No feedback submitted yet</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $feedbacks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection