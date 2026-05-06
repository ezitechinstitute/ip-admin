@extends('layouts/layoutMaster')

@section('title', 'Certificate Requests')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Certificate Requests</h4>
            <p class="text-muted mb-0">Manage and approve intern certificate requests</p>
        </div>
        <div>
            <a href="{{ route('manager.certificate.templates') }}" class="btn btn-outline-primary">
                <i class="ti ti-certificate me-1"></i> Manage Templates
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pending</span>
                            <h3 class="mb-0">{{ $requests->where('status', 'pending')->count() }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                            <i class="ti ti-hourglass fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Approved</span>
                            <h3 class="mb-0">{{ $requests->where('status', 'approved')->count() }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="ti ti-check fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total</span>
                            <h3 class="mb-0">{{ $requests->total() }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="ti ti-files fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Templates</span>
                            <h3 class="mb-0">{{ \App\Models\CertificateTemplate::where('is_deleted', 0)->count() }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="ti ti-template fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
        <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Filters Card --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('manager.certificate.requests') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted">SEARCH</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="ti ti-search"></i></span>
                        <input type="search" name="search" class="form-control" placeholder="Search by name, email..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">STATUS</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted">PER PAGE</label>
                    <select name="perpage" class="form-select" onchange="this.form.submit()">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-filter me-1"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Requests Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-semibold">Certificate Requests List</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Request ID</th>
                        <th>Intern</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Request Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $key => $req)
                    <tr>
                        <td>{{ $requests->firstItem() + $key }}</td>
                        <td><code>{{ $req->certificate_request_id }}</code></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm bg-primary bg-opacity-10 rounded-circle">
                                    <span class="fw-semibold">{{ substr($req->intern_name, 0, 2) }}</span>
                                </div>
                                <span class="fw-semibold">{{ $req->intern_name }}</span>
                            </div>
                        </td>
                        <td>{{ $req->email }}</td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info">
                                {{ $req->certificate_type == 'internship' ? 'Internship' : 'Course Completion' }}
                            </span>
                        </td>
                        <td>
                            @if($req->status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                            @elseif($req->status == 'approved')
                                <span class="badge bg-success bg-opacity-10 text-success">Approved</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $req->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                @if($req->status == 'pending')
                                    <button class="btn btn-sm btn-success approve-btn" 
                                            data-id="{{ $req->id }}"
                                            data-type="{{ $req->certificate_type }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#approveModal">
                                        <i class="ti ti-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-btn"
                                            data-id="{{ $req->id }}"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal">
                                        <i class="ti ti-x"></i> Reject
                                    </button>
                                @elseif($req->status == 'approved' && $req->pdf_path)
                                    <a href="{{ route('manager.certificate.request.download', $req->id) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="ti ti-download"></i> Download
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ti ti-certificate fs-1 mb-2 d-block"></i>
                                <p>No certificate requests found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }} entries
                </div>
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Certificate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manager.certificate.request.update-status') }}">
                @csrf
                <input type="hidden" name="id" id="approve_id">
                <input type="hidden" name="status" value="approved">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Certificate Type</label>
                        <select name="certificate_type" id="approve_type" class="form-select" required>
                            <option value="internship">Internship Certificate</option>
                            <option value="course_completion">Course Completion Certificate</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-1"></i>
                        An email with the certificate PDF will be sent to the intern.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Approve & Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Certificate Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('manager.certificate.request.update-status') }}">
                @csrf
                <input type="hidden" name="id" id="reject_id">
                <input type="hidden" name="status" value="rejected">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Certificate Type</label>
                        <select name="certificate_type" id="reject_type" class="form-select" required>
                            <option value="internship">Internship Certificate</option>
                            <option value="course_completion">Course Completion Certificate</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required placeholder="Please provide reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Approve modal data
document.querySelectorAll('.approve-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('approve_id').value = btn.dataset.id;
        document.getElementById('approve_type').value = btn.dataset.type;
    });
});

// Reject modal data
document.querySelectorAll('.reject-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('reject_id').value = btn.dataset.id;
    });
});
</script>
@endpush