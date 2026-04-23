@extends('layouts/layoutMaster')

@section('title', 'Withdraw')

@section('content')

<div class="row mb-4">
  <div class="col-12 d-flex justify-content-between">
    <h4>💸 Withdraw Requests</h4>

    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal">
      + New Request
    </button>
  </div>
</div>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show col-12 mb-4" role="alert">
  <div class="d-flex align-items-start">
    <i class="icon-base ti tabler-check-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
    <div class="flex-grow-1">
      <h6 class="alert-heading mb-2">{{ session('success') }}</h6>
      <p class="mb-0 text-muted">Your withdrawal request has been submitted successfully. The admin will review it shortly.</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
@endif

{{-- TABLE --}}
<table class="table table-bordered">
  <thead>
    <tr>
      <th>#</th>
      <th>Bank</th>
      <th>Amount</th>
      <th>Status</th>
      <th>Date</th>
    </tr>
  </thead>

  <tbody>
    @forelse($withdrawRequests as $r)
    <tr>
      <td>{{ $loop->iteration }}</td>
      <td>{{ $r->bank }}</td>
      <td>PKR {{ number_format($r->amount) }}</td>
      <td>
        @if($r->req_status == 0)
          <span class="badge bg-warning">Pending</span>
        @elseif($r->req_status == 1)
          <span class="badge bg-success">Approved</span>
        @elseif($r->req_status == 2)
          <span class="badge bg-danger">Rejected</span>
        @elseif($r->req_status == 3)
          <span class="badge bg-primary">Paid</span>
        @endif
      </td>
      <td>{{ \Carbon\Carbon::parse($r->created_at)->format('d M Y') }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center">No data</td>
    </tr>
    @endforelse
  </tbody>
</table>

{{-- MODAL --}}
<div class="modal fade" id="modal">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('manager.withdraw.store') }}">
      @csrf

      <div class="modal-content">
        <div class="modal-header">
          <h5>New Request</h5>
        </div>

        <div class="modal-body">

          <input name="bank" class="form-control mb-2" placeholder="Bank Name" required>

          <input name="ac_no" class="form-control mb-2" placeholder="Account No" required>

          <input name="ac_name" class="form-control mb-2" placeholder="Account Name" required>

          <input name="amount" class="form-control mb-2" placeholder="Amount" required>

          <input name="period" class="form-control mb-2" placeholder="Period">

          <textarea name="description" class="form-control" placeholder="Description"></textarea>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>

    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success/error alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
      }, 5000);
    });
  });
</script>
@endpush