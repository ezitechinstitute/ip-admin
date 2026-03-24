@extends('layouts/layoutMaster')

@section('title', 'Invoice Details')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Invoice Details</h4>
      <p class="text-muted mb-0">{{ $invoice->inv_id }} - {{ $invoice->name }}</p>
    </div>
    <div class="d-flex gap-2">
      @if($invoice->remaining_amount > 0)
      <a href="{{ route('invoices.payment', $invoice->id) }}" class="btn btn-success">
        <i class="ti ti-cash me-1"></i>Record Payment
      </a>
      @endif
      {{-- PDF BUTTON ADDED HERE --}}
      <a href="{{ route('invoices.pdf', $invoice->id) }}" class="btn btn-primary" target="_blank">
        <i class="ti ti-file-text me-1"></i>Download PDF
      </a>
      <a href="{{ route('invoices.dashboard') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
  <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
  <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  {{-- Invoice Details Column --}}
  <div class="col-lg-5">
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-file-invoice me-2"></i>Invoice Information
        </h6>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <th width="40%">Invoice ID:</th>
            <td><span class="badge bg-label-primary fs-6">{{ $invoice->inv_id }}</span></td>
          </tr>
          <tr>
            <th>Intern Name:</th>
            <td class="fw-semibold">{{ $invoice->name }}</td>
          </tr>
          <tr>
            <th>Email:</th>
            <td>{{ $invoice->intern_email }}</td>
          </tr>
          <tr>
            <th>Contact:</th>
            <td>{{ $invoice->contact }}</td>
          </tr>
          <tr>
            <th>Invoice Type:</th>
            <td><span class="badge bg-label-info">{{ $invoice->invoice_type }}</span></td>
          </tr>
          <tr>
            <th>Created By:</th>
            <td>{{ $invoice->received_by }}</td>
          </tr>
          <tr>
            <th>Created Date:</th>
            <td>{{ $invoice->created_at->format('d M Y, h:i A') }}</td>
          </tr>
        </table>
      </div>
    </div>

    {{-- Financial Summary Card --}}
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-currency-rupee me-2"></i>Financial Summary
        </h6>
      </div>
      <div class="card-body">
        @php
          $status = $invoice->remaining_amount <= 0 ? 'Paid' : 
                   ($invoice->due_date < now() ? 'Overdue' : 'Pending');
          $statusClass = $invoice->remaining_amount <= 0 ? 'success' : 
                        ($invoice->due_date < now() ? 'danger' : 'warning');
        @endphp

        <div class="d-flex justify-content-between mb-3">
          <span>Total Amount:</span>
          <span class="fw-bold">PKR {{ number_format($invoice->total_amount, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Received Amount:</span>
          <span class="fw-bold text-success">PKR {{ number_format($invoice->received_amount, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Remaining Amount:</span>
          <span class="fw-bold text-warning">PKR {{ number_format($invoice->remaining_amount, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span>Due Date:</span>
          <span class="fw-bold">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</span>
        </div>
        <div class="d-flex justify-content-between">
          <span>Status:</span>
          <span class="badge bg-{{ $statusClass }} text-white px-3 py-2">{{ $status }}</span>
        </div>
      </div>
    </div>

    {{-- Screenshot --}}
    @if($invoice->screenshot)
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-photo me-2"></i>Payment Screenshot
        </h6>
      </div>
      <div class="card-body text-center">
        <img src="{{ asset($invoice->screenshot) }}" class="img-fluid rounded" style="max-height: 200px;" alt="Invoice Screenshot">
        <div class="mt-3">
          <a href="{{ asset($invoice->screenshot) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="ti ti-external-link me-1"></i>View Full Image
          </a>
        </div>
      </div>
    </div>
    @endif
  </div>

  {{-- Transaction History Column --}}
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-history me-2"></i>Payment History
        </h6>
      </div>
      <div class="card-body">
        @if($transactions->isEmpty())
        <div class="text-center py-5">
          <i class="ti ti-cash ti-3x text-muted mb-3"></i>
          <h6>No payments recorded yet</h6>
          <p class="text-muted mb-3">Record the first payment for this invoice</p>
          @if($invoice->remaining_amount > 0)
          <a href="{{ route('invoices.payment', $invoice->id) }}" class="btn btn-success">
            <i class="ti ti-plus me-1"></i>Record Payment
          </a>
          @endif
        </div>
        @else
        <div class="timeline-vertical timeline-vertical--icons">
          @foreach($transactions as $transaction)
          <div class="timeline-item">
            <div class="timeline-item-inner d-flex">
              <div class="timeline-badge-wrapper">
                <span class="timeline-badge bg-{{ $transaction->method_badge ?? 'primary' }}"></span>
              </div>
              <div class="timeline-content flex-grow-1 pb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0 fw-semibold">
                    Payment Received - PKR {{ number_format($transaction->amount, 2) }}
                  </h6>
                  <small class="text-muted">
                    @if(isset($transaction->payment_date) && $transaction->payment_date)
                      {{ \Carbon\Carbon::parse($transaction->payment_date)->format('d M Y') }}
                    @else
                      {{ $transaction->created_at ? $transaction->created_at->format('d M Y') : 'Date not set' }}
                    @endif
                  </small>
                </div>
                <p class="mb-1">
                  <span class="badge bg-label-info">{{ ucfirst($transaction->method ?? 'cash') }}</span>
                  <span class="ms-2">by {{ $transaction->created_by_name ?? 'System' }}</span>
                </p>
                @if($transaction->notes)
                <p class="text-muted small mb-2">{{ $transaction->notes }}</p>
                @endif
                @if($transaction->screenshot)
                <a href="{{ asset($transaction->screenshot) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                  <i class="ti ti-photo me-1"></i>View Proof
                </a>
                @endif
              </div>
            </div>
          </div>
          @endforeach
        </div>

        {{-- Summary --}}
        <div class="mt-4 p-3 bg-light rounded">
          <div class="row">
            <div class="col-md-4">
              <small class="text-muted d-block">Total Payments</small>
              <h5 class="mb-0">{{ $transactions->count() }}</h5>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Total Received</small>
              <h5 class="mb-0 text-success">PKR {{ number_format($transactions->sum('amount'), 2) }}</h5>
            </div>
            <div class="col-md-4">
              <small class="text-muted d-block">Remaining</small>
              <h5 class="mb-0 text-warning">PKR {{ number_format($invoice->remaining_amount, 2) }}</h5>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
.timeline-vertical {
  position: relative;
  padding-left: 1.5rem;
}
.timeline-item {
  position: relative;
  padding-left: 1.5rem;
}
.timeline-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 2px;
  background: #e9ecef;
}
.timeline-item:last-child::before {
  height: 20px;
  bottom: auto;
}
.timeline-badge-wrapper {
  position: absolute;
  left: -0.9rem;
  top: 0;
  z-index: 1;
}
.timeline-badge {
  display: inline-block;
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #7367f0;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.timeline-content {
  padding-bottom: 1.5rem;
}
</style>
@endpush