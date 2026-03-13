@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Withdraw Request')

@section('vendor-style')
<link rel="stylesheet" href="path-to/select2.css">
<link rel="stylesheet" href="path-to/form-validation.css">
<link rel="stylesheet" href="path-to/animate.css">
<link rel="stylesheet" href="path-to/sweetalert2.css">
@endsection

@section('vendor-script')
<script src="path-to/select2.js"></script>
<script src="path-to/form-validation.js"></script>
<script src="path-to/cleave-zen.js"></script>
<script src="path-to/sweetalert2.js"></script>
@endsection

@section('content')
<div class="row">
  <div class="col-12 mb-6">
    <h4 class="mt-6 mb-1">Withdraw Request</h4>
    <p class="mb-6">Submit a new withdrawal request for your commission earnings</p>
  </div>

  {{-- Error Messages --}}
  @if($errors->any())
    @foreach($errors->all() as $error)
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ $error }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endforeach
  @endif

  {{-- Success Message --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Auto-hide script --}}
  <script>
    setTimeout(function() {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        alert.classList.remove('show');
        alert.classList.add('hide');
        setTimeout(() => alert.remove(), 500);
      });
    }, 5000);
  </script>

  <div class="col-xl-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Submit Withdraw Request</h5>
        {{-- <a href="{{ route('manager.withdraw.history') }}" class="btn btn-outline-primary btn-sm">
          <i class="icon-base ti tabler-history me-1"></i> View History
        </a> --}}
      </div>

      <div class="card-body">
        <form method="POST" action="{{ route('manager.withdraw.store') }}">
          @csrf

          <div class="row g-4">
            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="bank">Bank Name <span class="text-danger">*</span></label>
                <input type="text" 
                       name="bank" 
                       id="bank"
                       class="form-control @error('bank') is-invalid @enderror" 
                       value="{{ old('bank') }}"
                       placeholder="Enter bank name"
                       required>
                @error('bank')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="ac_no">Account Number <span class="text-danger">*</span></label>
                <input type="text" 
                       name="ac_no" 
                       id="ac_no"
                       class="form-control @error('ac_no') is-invalid @enderror" 
                       value="{{ old('ac_no') }}"
                       placeholder="Enter account number"
                       required>
                @error('ac_no')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="ac_name">Account Holder Name <span class="text-danger">*</span></label>
                <input type="text" 
                       name="ac_name" 
                       id="ac_name"
                       class="form-control @error('ac_name') is-invalid @enderror" 
                       value="{{ old('ac_name') }}"
                       placeholder="Enter account holder name"
                       required>
                @error('ac_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="amount">Amount (BDT) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">RS</span>
                  <input type="number" 
                         name="amount" 
                         id="amount"
                         class="form-control @error('amount') is-invalid @enderror" 
                         value="{{ old('amount') }}"
                         placeholder="Enter amount"
                         min="1"
                         step="0.01"
                         required>
                </div>
                @error('amount')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12 col-md-6">
              <div class="mb-3">
                <label class="form-label" for="period">Period</label>
                <input type="text" 
                       name="period" 
                       id="period"
                       class="form-control @error('period') is-invalid @enderror" 
                       value="{{ old('period', date('F Y')) }}"
                       placeholder="Example: Feb 2026">
                <small class="text-muted">Select the period for this withdrawal</small>
                @error('period')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12">
              <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" 
                          id="description"
                          class="form-control @error('description') is-invalid @enderror" 
                          rows="4"
                          placeholder="Enter any additional notes or description">{{ old('description') }}</textarea>
                @error('description')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="col-12">
              <div class="alert alert-info" role="alert">
                <div class="d-flex">
                  <i class="icon-base ti tabler-info-circle me-2 mt-1"></i>
                  <div>
                    <p class="mb-0"><strong>Note:</strong> Withdrawal requests are processed within 3-5 business days. You'll be notified once your request is approved.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="icon-base ti tabler-send me-1"></i>
                  Submit Withdraw Request
                </button>
                <a href="{{ route('manager.dashboard') }}" class="btn btn-label-secondary">
                  <i class="icon-base ti tabler-x me-1"></i>
                  Cancel
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Available Balance Card --}}
  {{-- <div class="col-xl-4">
    <div class="card bg-gradient-primary text-white mb-4">
      <div class="card-body">
        <div class="d-flex align-items-center mb-3">
          <div class="badge rounded bg-white bg-opacity-25 p-2 me-3">
            <i class="icon-base ti tabler-wallet icon-lg"></i>
          </div>
          <h5 class="text-white mb-0">Available Balance</h5>
        </div>
        <h2 class="text-white mb-2">৳ {{ number_format(auth()->guard('manager')->user()->commission_balance ?? 0, 2) }}</h2>
        <p class="text-white text-opacity-75 mb-0">Your current withdrawable commission</p>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h6 class="card-title mb-0">Withdrawal Guidelines</h6>
      </div>
      <div class="card-body">
        <ul class="list-unstyled mb-0">
          <li class="d-flex mb-3">
            <i class="icon-base ti tabler-check text-success me-2 mt-1"></i>
            <span>Minimum withdrawal amount: ৳500</span>
          </li>
          <li class="d-flex mb-3">
            <i class="icon-base ti tabler-check text-success me-2 mt-1"></i>
            <span>Processing time: 3-5 business days</span>
          </li>
          <li class="d-flex mb-3">
            <i class="icon-base ti tabler-check text-success me-2 mt-1"></i>
            <span>Bank transfer is the only available method</span>
          </li>
          <li class="d-flex">
            <i class="icon-base ti tabler-check text-success me-2 mt-1"></i>
            <span>Ensure your bank details are correct</span>
          </li>
        </ul>
      </div>
    </div>
  </div> --}}
</div>

@push('scripts')
<script>
  // Optional: Add any custom JavaScript for the withdraw form
  document.addEventListener('DOMContentLoaded', function() {
    // Format amount input
    const amountInput = document.getElementById('amount');
    if (amountInput) {
      amountInput.addEventListener('blur', function() {
        if (this.value) {
          this.value = parseFloat(this.value).toFixed(2);
        }
      });
    }

    // Auto-fill period with current month/year if empty
    const periodInput = document.getElementById('period');
    if (periodInput && !periodInput.value) {
      const now = new Date();
      const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
      ];
      periodInput.value = monthNames[now.getMonth()] + ' ' + now.getFullYear();
    }
  });
</script>
@endpush

@endsection