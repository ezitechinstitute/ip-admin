@extends('layouts/layoutMaster')

@section('title', 'Create Invoice')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
])
@endsection

@section('content')
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Create New Invoice</h4>
      <p class="text-muted mb-0">Generate invoice for internship or course</p>
    </div>
    {{-- FIXED: Changed from manager.invoices.dashboard to invoices.dashboard --}}
    <a href="{{ route('invoices.dashboard') }}" class="btn btn-secondary">
      <i class="ti ti-arrow-left me-1"></i>Back to Dashboard
    </a>
  </div>
</div>

{{-- Error Messages --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
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
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">
          <i class="ti ti-file-invoice me-2"></i>Invoice Details
        </h6>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('invoices.store') }}" enctype="multipart/form-data">
          @csrf

          {{-- Intern Selection --}}
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Intern Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                     value="{{ old('name') }}" placeholder="Enter intern's full name" required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
              <input type="email" name="intern_email" class="form-control @error('intern_email') is-invalid @enderror" 
                     value="{{ old('intern_email') }}" placeholder="intern@example.com" required>
              @error('intern_email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Contact Number <span class="text-danger">*</span></label>
              <input type="text" name="contact" class="form-control @error('contact') is-invalid @enderror" 
                     value="{{ old('contact') }}" placeholder="+92 XXX XXXXXXX" required>
              @error('contact')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Invoice Type <span class="text-danger">*</span></label>
              <select name="invoice_type" class="form-select @error('invoice_type') is-invalid @enderror" required>
                <option value="">Select Type</option>
                <option value="Internship" {{ old('invoice_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                <option value="Course" {{ old('invoice_type') == 'Course' ? 'selected' : '' }}>Course</option>
              </select>
              @error('invoice_type')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Intern ID</label>
              <input type="text" name="intern_id" class="form-control" value="{{ old('intern_id') }}" placeholder="Optional">
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Technology</label>
              <input type="text" name="technology" class="form-control" value="{{ old('technology') }}" placeholder="e.g., Web Development">
            </div>
          </div>

          <hr class="my-4">

          {{-- Financial Details --}}
          <h6 class="fw-semibold mb-3">Payment Details</h6>
          
          <div class="row g-4 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Total Amount (PKR) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rs.</span>
                <input type="number" name="total_amount" id="total_amount" 
                       class="form-control @error('total_amount') is-invalid @enderror" 
                       value="{{ old('total_amount') }}" step="0.01" min="1" required>
              </div>
              @error('total_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Received Amount (PKR) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">Rs.</span>
                <input type="number" name="received_amount" id="received_amount" 
                       class="form-control @error('received_amount') is-invalid @enderror" 
                       value="{{ old('received_amount', 0) }}" step="0.01" min="0" required>
              </div>
              @error('received_amount')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Remaining Amount</label>
              <div class="input-group">
                <span class="input-group-text">Rs.</span>
                <input type="text" id="remaining_amount" class="form-control" readonly value="0">
              </div>
              <small class="text-muted">Auto-calculated</small>
            </div>
          </div>

          <div class="row g-4 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Due Date <span class="text-danger">*</span></label>
              <input type="date" name="due_date" class="form-control @error('due_date') is-invalid @enderror" 
                     value="{{ old('due_date') }}" min="{{ date('Y-m-d') }}" required>
              @error('due_date')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Payment Method</label>
              <select name="payment_method" class="form-select">
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="credit_card">Credit Card</option>
                <option value="cheque">Cheque</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label fw-semibold">Notes (Optional)</label>
              <textarea name="notes" class="form-control" rows="1" placeholder="Additional notes">{{ old('notes') }}</textarea>
            </div>
          </div>

          <hr class="my-4">

          {{-- Preview Section --}}
          <div class="row mb-4">
            <div class="col-12">
              <div class="card bg-light">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">Invoice Preview</h6>
                  <div class="row">
                    <div class="col-md-6">
                      <table class="table table-sm">
                        <tr>
                          <td>Invoice ID:</td>
                          <td class="fw-semibold text-primary">INV-xxxx (Auto-generated)</td>
                        </tr>
                        <tr>
                          <td>Total Amount:</td>
                          <td class="fw-semibold" id="preview_total">PKR 0.00</td>
                        </tr>
                        <tr>
                          <td>Received:</td>
                          <td class="fw-semibold text-success" id="preview_received">PKR 0.00</td>
                        </tr>
                        <tr>
                          <td>Remaining:</td>
                          <td class="fw-semibold text-warning" id="preview_remaining">PKR 0.00</td>
                        </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary px-5">
              <i class="ti ti-device-floppy me-2"></i>Create Invoice
            </button>
            {{-- FIXED: Changed from manager.invoices.dashboard to invoices.dashboard --}}
            <a href="{{ route('invoices.dashboard') }}" class="btn btn-secondary px-4">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Calculate remaining amount
function calculateRemaining() {
  const total = parseFloat(document.getElementById('total_amount').value) || 0;
  const received = parseFloat(document.getElementById('received_amount').value) || 0;
  const remaining = total - received;
  
  document.getElementById('remaining_amount').value = remaining.toFixed(2);
  document.getElementById('preview_total').innerText = 'PKR ' + total.toFixed(2);
  document.getElementById('preview_received').innerText = 'PKR ' + received.toFixed(2);
  document.getElementById('preview_remaining').innerText = 'PKR ' + remaining.toFixed(2);
}

document.getElementById('total_amount').addEventListener('input', calculateRemaining);
document.getElementById('received_amount').addEventListener('input', calculateRemaining);

// Initialize on load
calculateRemaining();

// Ensure received amount doesn't exceed total
document.getElementById('received_amount').addEventListener('change', function() {
  const total = parseFloat(document.getElementById('total_amount').value) || 0;
  const received = parseFloat(this.value) || 0;
  
  if (received > total) {
    alert('Received amount cannot exceed total amount');
    this.value = total;
    calculateRemaining();
  }
});
</script>
@endpush
@endsection