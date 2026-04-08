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

        {{-- LEFT TABLE --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5>My Certificates</h5>
                </div>

                <div class="table-responsive">
                    <table class="table">

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Status</th>
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

                                <td>{{ \Carbon\Carbon::parse($c->created_at)->format('d M Y') }}</td>

                                <td>
                                    @if($c->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($c->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>

                                <td>
                                    @if($c->status == 'approved')
                                        <a href="{{ route('intern.certificates.download', $c->id) }}"
                                           class="btn btn-success btn-sm">
                                           Download
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No records</td>
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

        {{-- RIGHT SIDE --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Request Certificate</h5>
                </div>

                <div class="card-body text-center">

                    @if($canRequest)
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#certModal">
                            Request New Certificate
                        </button>
                    @else
                        <div class="alert alert-warning">
                            Complete internship first
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>


{{-- MODAL --}}
@if($canRequest)
<div class="modal fade" id="certModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="{{ route('intern.certificates.request') }}">
                @csrf

                <div class="modal-header">
                    <h5>Request Certificate</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Type</label>
                        <select name="certificate_type" class="form-select" required>
                            <option value="">Select</option>
                            <option value="internship">Internship</option>
                            <option value="course_completion">Course</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Purpose</label>
                        <input type="text" name="purpose" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary w-100">Submit</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endif

@endsection