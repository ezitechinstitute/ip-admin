@extends('layouts/layoutMaster')

@section('title', 'Intern Feedback')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Performance Feedback</h4>
    </div>

    <div class="row">
        {{-- Left: Feedback Submission Form --}}
        <div class="col-xl-4 col-lg-5">
            <div class="card mb-4">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Submit New Feedback</h5>
                </div>
                <div class="card-body pt-4">
                    <form method="POST" action="{{ route('supervisor.feedback.store') }}">
                        @csrf
                        <div class="row g-3">
                            {{-- Intern Selection --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Select Intern</label>
                                <select name="eti_id" id="feedbackInternSelect" class="form-select select2" required>
                                    <option value="">-- Choose Intern --</option>
                                    @foreach($interns as $intern)
                                        <option value="{{ $intern->eti_id }}">
                                            {{ $intern->name }} ({{ $intern->eti_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Score --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Performance Score (1-100)</label>
                                <div class="input-group input-group-merge">
                                    <input type="number" name="score" class="form-control" min="1" max="100" placeholder="e.g. 85" required>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">General Remarks</label>
                                <textarea name="remarks" class="form-control" rows="3" placeholder="How was their overall performance?" required></textarea>
                            </div>

                            {{-- Improvement Suggestions --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Improvement Suggestions</label>
                                <textarea name="improvement_suggestions" class="form-control" rows="3" placeholder="What should they focus on next?"></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <i class="ti ti-send me-1"></i> Submit Review
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Feedback History --}}
        <div class="col-xl-8 col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="card-title mb-0">Feedback History</h5>
                    <span class="badge bg-label-secondary">{{ $feedbacks->count() }} Reviews Given</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Intern</th>
                                <th class="text-center">Score</th>
                                <th>Feedback Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($feedbacks as $index => $feedback)
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-heading">{{ $feedback->intern_name ?? 'N/A' }}</span>
                                            <small class="text-muted">{{ $feedback->eti_id }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        @php
                                            $score = $feedback->score;
                                            $color = $score >= 80 ? 'success' : ($score >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="avatar avatar-sm mx-auto">
                                            <span class="avatar-initial rounded bg-label-{{ $color }} fw-bold">{{ $score }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div style="max-width: 300px;" class="text-wrap">
                                            <div class="fw-semibold small">Remarks:</div>
                                            <p class="small mb-1 text-muted">{{ $feedback->remarks }}</p>
                                            @if($feedback->improvement_suggestions)
                                                <div class="fw-semibold small text-primary">Suggestions:</div>
                                                <p class="small mb-0 text-muted italic">"{{ $feedback->improvement_suggestions }}"</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-nowrap small">
                                            <i class="ti ti-calendar-check me-1 ti-xs"></i>
                                            {{ \Carbon\Carbon::parse($feedback->created_at)->format('M d, Y') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ti ti-message-off display-6 d-block mb-2"></i>
                                            No feedback history found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('#feedbackInternSelect').select2({
                placeholder: "-- Choose Intern --",
                allowClear: true
            });
        }
    });
</script>
@endsection