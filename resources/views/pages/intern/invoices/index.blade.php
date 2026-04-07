@extends('layouts/layoutMaster')

@section('title', 'My Invoices')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-md-4 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-wallet fs-1 text-success mb-2"></i>
                    <h3 class="mb-0">{{ $stats['paid'] ?? 0 }}</h3>
                    <small class="text-muted">Paid Invoices</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-clock fs-1 text-warning mb-2"></i>
                    <h3 class="mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    <small class="text-muted">Pending Invoices</small>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="card">
                <div class="card-body text-center">
                    <i class="ti ti-alert-triangle fs-1 text-danger mb-2"></i>
                    <h3 class="mb-0">{{ $stats['overdue'] ?? 0 }}</h3>
                    <small class="text-muted">Overdue Invoices</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Invoice History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Type</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Remaining</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->inv_id ?? 'INV-' . $invoice->id }}</td>
                            <td>{{ ucfirst($invoice->invoice_type ?? 'Internship') }}</td>
                            <td>PKR {{ number_format($invoice->total_amount, 0) }}</td>
                            <td>PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</td>
                            <td>PKR {{ number_format($invoice->remaining_amount ?? $invoice->total_amount, 0) }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</td>
                            <td>
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
                                <span class="badge bg-{{ $badge }}">{{ $status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('intern.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ti ti-file-invoice ti-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No invoices found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection