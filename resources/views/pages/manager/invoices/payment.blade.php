@extends('layouts/layoutMaster')

@section('title', 'Record Payment')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Record Payment</h4>
      <p class="text-muted mb-0">{{ $invoice->inv_id }} - {{ $invoice->name }}</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('invoices.view', $invoice->id) }}" class="btn btn-info">
        <i class="ti ti-eye me-1"></i>View Invoice
      </a>
      <a href="{{ route('invoices.dashboard') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left me-1"></i>Back
      </a>
    </div>
  </div>
</div>

{{-- Error Messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
  <strong>Please fix the following errors:</strong>
  <ul class="mb-0 mt-2">
    @foreach($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
  <div class="col-lg-4">
    {{-- Invoice Summary Card --}}
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-file-invoice me-2"></i>Invoice Summary
        </h6>
      </div>
      <div class="card-body">
        <table class="table table-borderless">
          <tr>
            <td>Invoice ID:</td>
            <td class="fw-semibold">{{ $invoice->inv_id }}</td>
          </tr>
          <tr>
            <td>Intern:</td>
            <td>{{ $invoice->name }}</td>
          </tr>
          <tr>
            <td>Total Amount:</td>
            <td class="fw-bold">PKR {{ number_format($invoice->total_amount, 2) }}</td>
          </tr>
          <tr>
            <td>Already Paid:</td>
            <td class="fw-bold text-success">PKR {{ number_format($invoice->received_amount, 2) }}</td>
          </tr>
          <tr>
            <td>Remaining:</td>
            <td class="fw-bold text-warning">PKR {{ number_format($invoice->remaining_amount, 2) }}</td>
          </tr>
          <tr>
            <td>Due Date:</td>
            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
          </tr>
        </table>

        <div class="progress mt-3" style="height: 8px;">
          @php
            $paidPercentage = ($invoice->received_amount / $invoice->total_amount) * 100;
          @endphp
          <div class="progress-bar bg-success" role="progressbar" 
               style="width: {{ $paidPercentage }}%;" 
               aria-valuenow="{{ $paidPercentage }}" aria-valuemin="0" aria-valuemax="100">
          </div>
        </div>
        <small class="text-muted">{{ number_format($paidPercentage, 1) }}% Paid</small>
      </div>
    </div>

    {{-- Payment Tips --}}
    <div class="card">
      <div class="card-body">
        <h6 class="fw-semibold mb-3">
          <i class="ti ti-info-circle me-2"></i>Payment Tips
        </h6>
        <ul class="small text-muted mb-0">
          <li class="mb-2">Payment amount cannot exceed remaining balance</li>
          <li class="mb-2">You can upload payment proof screenshot</li>
          <li class="mb-2">Transaction will be recorded in payment history</li>
          <li>Intern's remaining balance will auto-update</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-cash me-2"></i>Payment Details
        </h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('invoices.record-payment', $invoice->id) }}" enctype="multipart/form-data">
          @csrf

          <div class="row g-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Amount (PKR) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rs.</span>
                <input type="number" name="payment_amount" id="payment_amount" 
                       class="form-control @error('payment_amount') is-invalid @enderror" 
                       value="{{ old('payment_amount', $invoice->remaining_amount) }}" 
                       step="0.01" min="1" max="{{ $invoice->remaining_amount }}" required>
              </div>
              @error('payment_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="text-muted">Max: PKR {{ number_format($invoice->remaining_amount, 2) }}</small>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Date <span class="text-danger">*</span></label>
              <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" 
                     value="{{ old('payment_date', date('Y-m-d')) }}" required>
              @error('payment_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
              <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                <option value="">Select Method</option>
                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
              </select>
              @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Payment Proof (Optional)</label>
              <input type="file" name="screenshot" class="form-control" accept="image/*">
              <small class="text-muted">Upload screenshot or receipt (max 2MB)</small>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Notes (Optional)</label>
              <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" 
                        rows="3" placeholder="Add any additional notes about this payment">{{ old('notes') }}</textarea>
              @error('notes')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <hr class="my-4">

          <div class="d-flex gap-3">
            <button type="submit" class="btn btn-success px-5">
              <i class="ti ti-cash me-2"></i>Record Payment
            </button>
            <a href="{{ route('invoices.view', $invoice->id) }}" class="btn btn-secondary px-4">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Ensure payment amount doesn't exceed remaining
document.getElementById('payment_amount').addEventListener('change', function() {
  const max = {{ $invoice->remaining_amount }};
  const value = parseFloat(this.value) || 0;
  
  if (value > max) {
    alert('Payment amount cannot exceed remaining balance of PKR ' + max.toFixed(2));
    this.value = max;
  }
});
</script>
@endpush