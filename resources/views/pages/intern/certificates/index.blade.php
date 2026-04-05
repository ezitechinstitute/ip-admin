@extends('layouts/layoutMaster')

@section('title', 'My Certificates')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Certificates</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Certificate ID</th>
                                    <th>Certificate Type</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificates as $certificate)
                                <tr>
                                    <td>{{ $certificate->certificate_request_id }}</td>
                                    <td>
                                        {{ $certificate->certificate_type == 'internship' ? 'Internship Certificate' : 'Course Certificate' }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($certificate->created_at)->format('d M, Y') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$certificate->status] ?? 'secondary' }}">
                                            {{ ucfirst($certificate->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($certificate->status == 'approved' && $certificate->pdf_path)
                                            <a href="{{ route('intern.certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary">
                                                <i class="ti ti-download"></i> Download
                                            </a>
                                        @elseif($certificate->status == 'pending')
                                            <button class="btn btn-sm btn-secondary" disabled>
                                                <i class="ti ti-clock"></i> Pending
                                            </button>
                                        @elseif($certificate->status == 'rejected')
                                            <button class="btn btn-sm btn-danger" disabled>
                                                <i class="ti ti-x-circle"></i> Rejected
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="ti ti-certificate ti-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">No certificates yet</p>
                                        <p class="text-muted small">Complete your internship to request a certificate</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Request Certificate</h5>
                </div>
                <div class="card-body">
                    @if($canRequest)
                    <form action="{{ route('intern.certificates.request') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Certificate Type</label>
                            <select name="certificate_type" class="form-select" required>
                                <option value="internship">Internship Certificate</option>
                                <option value="course_completion">Course Certificate</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-send me-1"></i> Request Certificate
                        </button>
                    </form>
                    @else
                    <div class="text-center py-3">
                        <i class="ti ti-lock fs-1 text-muted mb-2"></i>
                        <p class="text-muted mb-0">Certificate requests are only available after internship completion.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection