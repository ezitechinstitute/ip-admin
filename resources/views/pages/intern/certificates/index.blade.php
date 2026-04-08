@extends('layouts/layoutMaster')

@section('title', 'My Certificates')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row g-4">
        {{-- Certificates Table --}}
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 hover-scale">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">My Certificates</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="fw-medium">Certificate ID</th>
                                    <th>Certificate Type</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($certificates as $certificate)
                                <tr class="hover-scale">
                                    <td class="fw-medium">{{ $certificate->certificate_request_id }}</td>
                                    <td>
                                        {{ $certificate->certificate_type == 'internship' ? 'Internship Certificate' : 'Course Certificate' }}
                                    </td>
                                    <td class="text-muted">{{ \Carbon\Carbon::parse($certificate->created_at)->format('d M, Y') }}</td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge rounded-pill bg-{{ $statusColors[$certificate->status] ?? 'secondary' }}">
                                            {{ ucfirst($certificate->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($certificate->status == 'approved' && $certificate->pdf_path)
                                            <a href="{{ route('intern.certificates.download', $certificate->id) }}" class="btn btn-sm btn-primary rounded-pill">
                                                <i class="ti ti-download me-1"></i> Download
                                            </a>
                                        @elseif($certificate->status == 'pending')
                                            <button class="btn btn-sm btn-secondary rounded-pill" disabled>
                                                <i class="ti ti-clock me-1"></i> Pending
                                            </button>
                                        @elseif($certificate->status == 'rejected')
                                            <button class="btn btn-sm btn-danger rounded-pill" disabled>
                                                <i class="ti ti-x-circle me-1"></i> Rejected
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="ti ti-certificate ti-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-1">No certificates yet</p>
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

        {{-- Request Certificate Form --}}
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 hover-scale">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold">Request Certificate</h5>
                </div>
                <div class="card-body">
                    @if($canRequest)
                    <form action="{{ route('intern.certificates.request') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Certificate Type</label>
                            <select name="certificate_type" class="form-select rounded-3" required>
                                <option value="internship">Internship Certificate</option>
                                <option value="course_completion">Course Certificate</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill hover-scale">
                            <i class="ti ti-send me-1"></i> Request Certificate
                        </button>
                    </form>
                    @else
                    <div class="text-center py-4">
                        <i class="ti ti-lock fs-1 text-muted mb-2"></i>
                        <p class="text-muted mb-0">Certificate requests are only available after internship completion.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card.hover-scale:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateX(1px);
}

.badge.rounded-pill {
    padding: 0.45em 0.75em;
    font-size: 0.85rem;
}

.btn.hover-scale:hover {
    transform: translateY(-1px);
}
</style>
@endsection