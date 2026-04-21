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

{{-- SUCCESS --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
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