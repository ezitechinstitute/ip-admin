@extends('layouts/layoutMaster')

@section('title', 'My Certificates')

@section('content')
<div class="container-xxl container-p-y">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 rounded-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT SIDE --}}
        <div class="col-lg-8">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-header  border-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Certificate Requests</h5>
                        <small class="text-muted">Track your internship & course certificates</small>
                    </div>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table align-middle mb-0">

                            <thead class="bg-light">
                                <tr class="text-uppercase small text-muted">
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Purpose</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                            @forelse($certificates as $c)

                                <tr>

                                    <td>
                                        <div class="fw-semibold">#{{ $c->certificate_request_id }}</div>
                                    </td>

                                    <td>
                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            {{ $c->certificate_type == 'internship' ? 'Internship' : 'Course' }}
                                        </span>
                                    </td>

                                    <td class="text-muted">
                                        {{ $c->purpose ?? '-' }}
                                    </td>

                                    <td>
                                        @if($c->status == 'pending')
                                            <span class="badge bg-warning text-dark px-3 py-2">Pending Review</span>
                                        @elseif($c->status == 'approved')
                                            <span class="badge bg-success px-3 py-2">Approved</span>
                                        @else
                                            <span class="badge bg-danger px-3 py-2">Rejected</span>
                                        @endif
                                    </td>

                                    <td class="text-muted">
                                        {{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}
                                    </td>

                                    <td class="text-center">

                                        <div class="d-flex justify-content-center gap-2">

                                            <button class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewModal{{ $c->id }}">
                                                Details
                                            </button>

                                            @if($c->status == 'approved')
                                                <a href="{{ route('intern.certificates.download', $c->id) }}"
                                                   class="btn btn-success btn-sm">
                                                    Download
                                                </a>
                                            @endif

                                        </div>

                                    </td>

                                </tr>

                                {{-- MODAL --}}
                                <div class="modal fade" id="viewModal{{ $c->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 rounded-4">

                                            <div class="modal-header border-0">
                                                <div>
                                                    <h5 class="mb-0">Request Details</h5>
                                                    <small class="text-muted">Certificate workflow info</small>
                                                </div>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <div class="mb-3">
                                                    <small class="text-muted">Request ID</small>
                                                    <div class="fw-semibold">#{{ $c->certificate_request_id }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <small class="text-muted">Type</small>
                                                    <div>{{ $c->certificate_type }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <small class="text-muted">Purpose</small>
                                                    <div>{{ $c->purpose ?? '-' }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <small class="text-muted">Notes</small>
                                                    <div>{{ $c->notes ?? '-' }}</div>
                                                </div>

                                                <div>
                                                    <small class="text-muted">Status</small>
                                                    <div class="fw-semibold">{{ ucfirst($c->status) }}</div>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            No certificate requests yet
                                        </div>
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="card-footer  border-0">
                    {{ $certificates->links() }}
                </div>

            </div>

        </div>

        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">

            <div class="card shadow-sm border-0 rounded-4">

                <div class="card-body p-4">

                    <h5 class="mb-1">Certificate Request</h5>
                    <p class="text-muted small mb-4">
                        Submit a request after completing your internship or course.
                    </p>

                    @if($canRequest)

                        <div class="p-3 rounded-3 bg-light mb-3">
                            <div class="fw-semibold">Ready to request?</div>
                            <small class="text-muted">
                                You can request internship or course completion certificate.
                            </small>
                        </div>

                        <button class="btn btn-primary w-100 py-2"
                                data-bs-toggle="modal"
                                data-bs-target="#certModal">
                            + Create Request
                        </button>

                    @else

                        <div class="alert alert-warning border-0">
                            You must complete your internship before requesting a certificate.
                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>
</div>

{{-- REQUEST MODAL --}}
@if($canRequest)
<div class="modal fade" id="certModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4">

            <form method="POST" action="{{ route('intern.certificates.request') }}">
                @csrf

                <div class="modal-header border-0">
                    <div>
                        <h5 class="mb-0">New Certificate Request</h5>
                        <small class="text-muted">Fill required details</small>
                    </div>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">Certificate Type</label>
                        <select name="certificate_type" class="form-select" required>
                            <option value="">Select type</option>
                            <option value="internship">Internship Certificate</option>
                            <option value="course_completion">Course Certificate</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="purpose" class="form-control" placeholder="e.g. Job application">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Optional"></textarea>
                    </div>

                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-primary w-100 py-2">
                        Submit Request
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>
@endif

@endsection