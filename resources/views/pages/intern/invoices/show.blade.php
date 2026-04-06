@extends('layouts/layoutMaster')

@section('title', 'Invoice Details')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Invoice #{{ $invoice->inv_id }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Invoice Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Invoice ID</th>
                            <td>{{ $invoice->inv_id }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Type</th>
                            <td>{{ ucfirst($invoice->invoice_type ?? 'Internship') }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $invoice->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $invoice->intern_email }}</td>
                        </tr>
                        <tr>
                            <th>Contact</th>
                            <td>{{ $invoice->contact }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Payment Details</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Amount</th>
                            <td>PKR {{ number_format($invoice->total_amount, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Paid Amount</th>
                            <td>PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Remaining Amount</th>
                            <td>PKR {{ number_format($invoice->remaining_amount ?? $invoice->total_amount, 0) }}</td>
                        </tr>
                        <tr>
                            <th>Due Date</th>
                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
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
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('intern.invoices') }}" class="btn btn-secondary">
                    <i class="ti ti-arrow-left"></i> Back to Invoices
                </a>
            </div>
        </div>
    </div>
</div>
@endsection