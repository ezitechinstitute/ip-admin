@extends('layouts/layoutMaster')

@section('title', 'Invoice Approval Queue')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1">Invoice Approval Queue</h4>
                <p class="text-muted mb-0">Approve or reject invoices created by managers</p>
            </div>
            <a href="{{ route('invoice-page') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left me-1"></i>Back to Invoices
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Pending Approvals</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Invoice ID</th>
                        <th>Intern Name</th>
                        <th>Email</th>
                        <th>Total Amount</th>
                        <th>Type</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingInvoices as $invoice)
                    <tr>
                        <td><span class="fw-bold">{{ $invoice->inv_id }}</span></td>
                        <td>{{ $invoice->name }}</td>
                        <td>{{ $invoice->intern_email }}</td>
                        <td>PKR {{ number_format($invoice->total_amount, 2) }}</td>
                        <td><span class="badge bg-info">{{ $invoice->invoice_type }}</span></td>
                        <td>{{ $invoice->received_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success btn-sm approve-invoice" 
                                        data-id="{{ $invoice->id }}"
                                        data-inv-id="{{ $invoice->inv_id }}"
                                        data-name="{{ $invoice->name }}">
                                    <i class="ti ti-check me-1"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-sm reject-invoice"
                                        data-id="{{ $invoice->id }}"
                                        data-inv-id="{{ $invoice->inv_id }}"
                                        data-name="{{ $invoice->name }}">
                                    <i class="ti ti-x me-1"></i>Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="ti ti-check-circle fs-1 text-success mb-3 d-block"></i>
                            <h5>No Pending Approvals</h5>
                            <p class="text-muted">All invoices have been processed</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $pendingInvoices->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
// Pure JavaScript - No jQuery dependency
document.addEventListener('DOMContentLoaded', function() {
    console.log('Approval Queue page loaded');
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Approve Invoice
    document.querySelectorAll('.approve-invoice').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const invId = this.getAttribute('data-inv-id');
            const name = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'Approve Invoice?',
                html: `Are you sure you want to approve invoice <strong>${invId}</strong> for <strong>${name}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch('/admin/invoices/approve/' + id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Approved!',
                                text: `Invoice ${invId} has been approved successfully.`,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to approve invoice',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });
    
    // Reject Invoice
    document.querySelectorAll('.reject-invoice').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const invId = this.getAttribute('data-inv-id');
            const name = this.getAttribute('data-name');
            
            Swal.fire({
                title: 'Reject Invoice?',
                html: `Are you sure you want to reject invoice <strong>${invId}</strong> for <strong>${name}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reject it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    fetch('/admin/invoices/reject/' + id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Rejected!',
                                text: `Invoice ${invId} has been rejected.`,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to reject invoice',
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush