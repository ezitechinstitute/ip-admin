@extends('layouts/layoutMaster')

@section('title', 'My Invoices')

@section('content')

<div class="container-xxl py-4">

    {{-- Header --}}
   <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
    <div>
        <small class="text-uppercase text-muted">Billing & Payments</small>
        <h2 class="fw-normal">My <em class="text-muted">Invoices</em></h2>
    </div>

    {{-- Create Invoice Button --}}
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
        + Create Invoice
    </button>
</div>

    {{-- Stats --}}
   <div class="row g-3 mb-4">

    {{-- PAID --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">

                <div>
                    <small class="text-muted text-uppercase">Paid</small>
                    <h3 class="text-success mb-0">{{ $stats['paid'] ?? 0 }}</h3>
                </div>

                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>

            </div>
        </div>
    </div>

    {{-- PENDING --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">

                <div>
                    <small class="text-muted text-uppercase">Pending</small>
                    <h3 class="text-warning mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                </div>

                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>

            </div>
        </div>
    </div>

    {{-- OVERDUE --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">

                <div>
                    <small class="text-muted text-uppercase">Overdue</small>
                    <h3 class="text-danger mb-0">{{ $stats['overdue'] ?? 0 }}</h3>
                </div>

                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                    <i class="bi bi-exclamation-triangle fs-4"></i>
                </div>

            </div>
        </div>
    </div>

</div>

   
</div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Invoice History</h5>
            <span class="badge bg-light text-dark">
                {{ $invoices->total() }} invoices
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    @php
                        $remaining = $invoice->remaining_amount ?? $invoice->total_amount;

                        if ($remaining <= 0) {
                            $status = 'Paid';
                            $badge = 'success';
                        } elseif (\Carbon\Carbon::parse($invoice->due_date)->isPast()) {
                            $status = 'Overdue';
                            $badge = 'danger';
                        } else {
                            $status = 'Pending';
                            $badge = 'warning';
                        }
                    @endphp

                    <tr>
                        <td>
                            <strong>{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}</strong>
                        </td>

                        <td>
                            <span class="badge bg-secondary">
                                {{ ucfirst($invoice->invoice_type ?? 'Internship') }}
                            </span>
                        </td>

                        <td>
                            PKR {{ number_format($invoice->total_amount, 0) }}
                        </td>

                        <td>
                            PKR {{ number_format($invoice->received_amount ?? 0, 0) }}
                        </td>

                        <td class="{{ $remaining <= 0 ? 'text-muted' : 'text-danger fw-bold' }}">
                            PKR {{ number_format($remaining, 0) }}
                        </td>

                        <td class="text-muted">
                            {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td>
                            <a href="{{ route('intern.invoices.show', $invoice->id) }}" 
                               class="btn btn-sm btn-outline-dark">
                                View
                            </a>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            No invoices found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($invoices->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
<!-- Create Invoice Modal -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Invoice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

          <form action="{{ route('intern.invoices.store') }}" method="POST">
    @csrf

    <div class="modal-body">

        {{-- INVOICE META --}}
        <div class="mb-4">
            <h6 class="text-muted text-uppercase small">Invoice Details</h6>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Invoice Type</label>
                    <select name="invoice_type" class="form-select">
                        <option value="internship">Internship</option>
                        <option value="service">Service</option>
                        <option value="training">Training</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>

            </div>
        </div>

        {{-- PAYMENT STRUCTURE --}}
        <div class="mb-3">
            <h6 class="text-muted text-uppercase small">Payment Breakdown</h6>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Total Amount (PKR)</label>
                    <input type="number" name="total_amount" id="total_amount"
                           class="form-control" placeholder="0" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Paid Amount (PKR)</label>
                    <input type="number" name="received_amount" id="paid_amount"
                           class="form-control" placeholder="0">
                </div>

                <div class="col-md-12">
                    <label class="form-label">Remaining Balance</label>
                    <input type="number" id="remaining_amount"
                           class="form-control bg-light" readonly>
                </div>

            </div>
        </div>

        {{-- OPTIONAL NOTE --}}
        <div class="mb-2">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="2"
                      placeholder="Add internal remarks..."></textarea>
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            Cancel
        </button>
        <button type="submit" class="btn btn-dark">
            Create Invoice
        </button>
    </div>
</form>

        </div>
    </div>
</div>

@endsection