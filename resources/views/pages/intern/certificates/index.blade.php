@extends('layouts/layoutMaster')

@section('title', 'My Certificates')

@section('content')
<div class="container-xxl container-p-y">

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">

        {{-- LEFT SIDE TABLE --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>My Certificates</h5>

                    <!-- REQUEST BUTTON -->
                    <button class="btn btn-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#certModal">
                        <i class="ti ti-plus"></i> Request Certificate
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($certificates as $c)
                            <tr>
                                <td>{{ $c->certificate_request_id }}</td>

                                <td>
                                    {{ $c->certificate_type == 'internship' ? 'Internship' : 'Course' }}
                                </td>

                                <td>
                                    @if($c->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($c->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>

                                <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>

                                <td>
                                    @if($c->status == 'approved')
                                        <a href="{{ route('intern.certificates.download', $c->id) }}"
                                           class="btn btn-success btn-sm">
                                           Download
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No certificates yet</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="p-3">
                    {{ $certificates->links() }}
                </div>
            </div>
        </div>

    </div>

</div>


{{-- MODAL --}}
<div class="modal fade" id="certModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('intern.certificates.request') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Request Certificate</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- TYPE -->
                    <div class="mb-3">
                        <label class="form-label">Certificate Type</label>
                        <select name="certificate_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="internship">Internship</option>
                            <option value="course_completion">Course Completion</option>
                        </select>
                    </div>

                    <!-- PURPOSE -->
                    <div class="mb-3">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="purpose" class="form-control"
                               placeholder="e.g. Job, University">
                    </div>

                    <!-- NOTES -->
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary w-100">
                        Submit Request
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection