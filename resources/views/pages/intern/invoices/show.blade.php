@extends('layouts/layoutMaster')

@section('title', 'Invoice Details')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
    }

    .info-box {
        background: rgba(255, 255, 255, 0.6);
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .info-box:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateX(5px);
    }

    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-paid { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .badge-overdue { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

    .btn-custom {
        border-radius: 50px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-custom:hover {
        transform: translateY(-2px);
    }

    .invoice-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .invoice-detail-label {
        font-weight: 600;
        color: #6c86a3;
    }

    .invoice-detail-value {
        font-weight: 500;
        color: #1e293b;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        .glass-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Back Button --}}
    <div class="mb-4 animate-card no-print" style="animation-delay: 0.1s;">
        <a href="{{ route('intern.invoices') }}" class="btn btn-secondary btn-custom">
            <i class="bi bi-arrow-left me-1"></i> Back to Invoices
        </a>
    </div>

    {{-- Invoice Header --}}
    <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.2s;">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-receipt text-primary fs-3"></i>
                    <h3 class="fw-bold mb-0">Invoice Details</h3>
                </div>
                <p class="text-muted mb-0">Invoice #{{ $invoice->inv_id }}</p>
            </div>
            @php
                $remaining = $invoice->remaining_amount ?? $invoice->total_amount;
                if ($remaining <= 0) {
                    $status = 'Paid';
                    $badge = 'paid';
                    $statusIcon = 'check-circle';
                } elseif (\Carbon\Carbon::parse($invoice->due_date)->isPast()) {
                    $status = 'Overdue';
                    $badge = 'overdue';
                    $statusIcon = 'exclamation-triangle';
                } else {
                    $status = 'Pending';
                    $badge = 'pending';
                    $statusIcon = 'hourglass-split';
                }
            @endphp
            <span class="badge-custom badge-{{ $badge }}">
                <i class="bi bi-{{ $statusIcon }} me-1"></i>
                {{ $status }}
            </span>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-building text-primary fs-3"></i>
                        <h6 class="fw-bold mb-0">Invoice Information</h6>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Invoice ID</span>
                        <span class="invoice-detail-value">{{ $invoice->inv_id }}</span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Invoice Type</span>
                        <span class="invoice-detail-value">{{ ucfirst($invoice->invoice_type ?? 'Internship') }}</span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Created Date</span>
                        <span class="invoice-detail-value">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M, Y') }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-person text-primary fs-3"></i>
                        <h6 class="fw-bold mb-0">Billing Information</h6>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Name</span>
                        <span class="invoice-detail-value">{{ $invoice->name }}</span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Email</span>
                        <span class="invoice-detail-value">{{ $invoice->intern_email }}</span>
                    </div>
                    <div class="invoice-detail-row">
                        <span class="invoice-detail-label">Contact</span>
                        <span class="invoice-detail-value">{{ $invoice->contact ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Details --}}
    <div class="glass-card p-4 mb-4 animate-card" style="animation-delay: 0.3s;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-credit-card text-primary fs-3"></i>
            <h5 class="fw-bold mb-0">Payment Details</h5>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calculator text-primary fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Total Amount</small>
                            <div class="fw-bold fs-4 text-primary">PKR {{ number_format($invoice->total_amount, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle text-success fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Paid Amount</small>
                            <div class="fw-bold fs-4 text-success">PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-hourglass text-warning fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Remaining Amount</small>
                            <div class="fw-bold fs-4 {{ $remaining <= 0 ? 'text-success' : 'text-warning' }}">
                                PKR {{ number_format($remaining, 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-calendar-event text-primary fs-3"></i>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block">Due Date</small>
                            <div class="fw-bold fs-4 {{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && $remaining > 0 ? 'text-danger' : '' }}">
                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}
                            </div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Progress --}}
    @php
        $paymentPercentage = $invoice->total_amount > 0 ? round(($invoice->received_amount / $invoice->total_amount) * 100) : 0;
    @endphp
    <div class="glass-card p-4 animate-card" style="animation-delay: 0.4s;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Payment Progress</small>
            <small class="fw-bold">{{ $paymentPercentage }}%</small>
        </div>
        <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-{{ $paymentPercentage >= 100 ? 'success' : ($paymentPercentage >= 50 ? 'primary' : 'warning') }}" 
                 style="width: {{ $paymentPercentage }}%; border-radius: 10px;"></div>
        </div>
    </div>

    {{-- Print Button --}}
    <div class="mt-4 no-print">
        <button onclick="window.print()" class="btn btn-primary btn-custom">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </button>
    </div>

</div>
@endsection