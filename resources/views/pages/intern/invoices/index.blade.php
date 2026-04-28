@extends('layouts/layoutMaster')

@section('title', 'Invoice Dashboard')

@section('page-style')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.65);
        --glass-border: rgba(255, 255, 255, 0.4);
        --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
        --card-radius: 1.25rem;
        --transition-smooth: all 0.2s cubic-bezier(0.25, 0.8, 0.25, 1);
        --primary-gradient: linear-gradient(135deg, #3b82f6, #1e40af);
        --success-gradient: linear-gradient(135deg, #10b981, #047857);
        --warning-gradient: linear-gradient(135deg, #f59e0b, #b45309);
        --danger-gradient: linear-gradient(135deg, #ef4444, #b91c1c);
        --info-gradient: linear-gradient(135deg, #8b5cf6, #6d28d9);
    }

    body {
        background: linear-gradient(135deg, #f6f9fc 0%, #eef2f5 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Premium Card */
    .premium-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
    }

    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
    }

    /* Stat Cards */
    .stat-card-premium {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 1.5rem;
        padding: 1.5rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        cursor: pointer;
        border: 1px solid rgba(255, 255, 255, 0.6);
    }

    .stat-card-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--stat-gradient);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .stat-card-premium:hover::after {
        transform: scaleX(1);
    }

    .stat-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 35px -15px rgba(0, 0, 0, 0.2);
    }

    .stat-icon-premium {
        width: 60px;
        height: 60px;
        background: var(--stat-bg);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-value-premium {
        font-size: 2.2rem;
        font-weight: 800;
        background: var(--stat-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1;
    }

    /* Badges */
    .badge-custom {
        padding: 0.4rem 0.8rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .badge-paid { background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); }
    .badge-pending { background: rgba(245,158,11,0.15); color: #f59e0b; border: 1px solid rgba(245,158,11,0.3); }
    .badge-overdue { background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); }

    /* Quick Stats */
    .quick-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .quick-stat-item {
        flex: 1;
        min-width: 100px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.75rem;
        padding: 0.75rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .quick-stat-item:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    /* Invoice Cards for Mobile */
    .invoice-mobile-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .invoice-mobile-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 10px 25px -8px rgba(0, 0, 0, 0.1);
    }

    /* Table Styles */
    .invoices-table {
        width: 100%;
        margin-bottom: 0;
    }

    .invoices-table thead th {
        background: rgba(255, 255, 255, 0.9);
        padding: 0.85rem;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6c86a3;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .invoices-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }

    .invoices-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .invoices-table tbody td {
        padding: 0.85rem;
        vertical-align: middle;
    }

    /* Desktop Table Container */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .invoices-table {
        min-width: 800px;
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-card {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @media (max-width: 768px) {
        .stat-card-premium {
            padding: 1rem;
        }
        .stat-value-premium {
            font-size: 1.5rem;
        }
        .stat-icon-premium {
            width: 45px;
            height: 45px;
        }
        .quick-stats {
            flex-wrap: wrap;
        }
        .quick-stat-item {
            min-width: calc(50% - 0.5rem);
        }
        .desktop-table {
            display: none;
        }
        .mobile-cards-view {
            display: block !important;
        }
    }

    @media (min-width: 769px) {
        .mobile-cards-view {
            display: none;
        }
        .desktop-table {
            display: block;
        }
    }

    /* Professional Stat Cards */
.stat-card-premium-hover {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 1.5rem;
    padding: 1.5rem;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.6);
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.stat-card-premium-hover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--stat-gradient);
}

.stat-card-premium-hover::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--stat-gradient), transparent);
    transform: scaleX(0);
    transition: transform 0.6s ease;
}

.stat-card-premium-hover:hover::after {
    transform: scaleX(1);
}

.stat-card-premium-hover:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 45px -15px rgba(0, 0, 0, 0.2);
}

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.stat-card-premium-hover:hover .stat-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.2;
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #6c86a3;
}

.stat-trend {
    font-size: 0.7rem;
    font-weight: 600;
    padding: 0.25rem 0.6rem;
    border-radius: 50px;
    background: rgba(0, 0, 0, 0.03);
}

.trend-up {
    color: #10b981;
}

.trend-down {
    color: #ef4444;
}

.stat-progress {
    width: 100%;
    height: 4px;
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-custom {
    height: 100%;
    border-radius: 10px;
    transition: width 0.8s ease;
}
</style>
@endsection

@section('content')
<div class="container-xxl py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h4 class="fw-bold mb-1" style="color: #1e293b;">
                <i class="bi bi-receipt text-primary me-2"></i>Invoice Dashboard
            </h4>
            <p class="text-muted small mb-0">Track and manage your billing history</p>
        </div>
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                <i class="bi bi-calendar-week me-1"></i> {{ now()->format('F Y') }}
            </span>
        </div>
    </div>

   {{-- Premium Statistics Cards --}}
<div class="row g-4 mb-4">
    @php
        $cards = [
            [
                'icon'=>'bi-receipt',
                'count'=>$stats['total'],
                'label'=>'Total Invoices',
                'gradient'=>'linear-gradient(135deg, #3b82f6, #1e40af)',
                'bg'=>'rgba(59,130,246,0.1)',
                'trend'=>'+12%',
                'trend_up'=>true,
                'icon_bg'=>'#3b82f6'
            ],
            [
                'icon'=>'bi-check-circle',
                'count'=>$stats['paid'],
                'label'=>'Paid',
                'gradient'=>'linear-gradient(135deg, #10b981, #047857)',
                'bg'=>'rgba(16,185,129,0.1)',
                'trend'=>'+8%',
                'trend_up'=>true,
                'icon_bg'=>'#10b981'
            ],
            [
                'icon'=>'bi-hourglass-split',
                'count'=>$stats['pending'],
                'label'=>'Pending',
                'gradient'=>'linear-gradient(135deg, #f59e0b, #b45309)',
                'bg'=>'rgba(245,158,11,0.1)',
                'trend'=>'+5%',
                'trend_up'=>true,
                'icon_bg'=>'#f59e0b'
            ],
            [
                'icon'=>'bi-exclamation-triangle',
                'count'=>$stats['overdue'],
                'label'=>'Overdue',
                'gradient'=>'linear-gradient(135deg, #ef4444, #b91c1c)',
                'bg'=>'rgba(239,68,68,0.1)',
                'trend'=>'+15%',
                'trend_up'=>false,
                'icon_bg'=>'#ef4444'
            ],
        ];
    @endphp

    @foreach($cards as $card)
    <div class="col-md-3 col-6 animate-card" style="animation-delay: {{ 0.1 + ($loop->index * 0.05) }}s;">
        <div class="stat-card-premium-hover" style="--stat-gradient: {{ $card['gradient'] }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="stat-icon-wrapper" style="background: {{ $card['bg'] }}">
                    <i class="bi {{ $card['icon'] }} fs-3" style="color: {{ $card['icon_bg'] }}"></i>
                </div>
                <div class="stat-trend {{ $card['trend_up'] ? 'trend-up' : 'trend-down' }}">
                    <i class="bi bi-{{ $card['trend_up'] ? 'arrow-up' : 'arrow-down' }}-short"></i>
                    {{ $card['trend'] }}
                </div>
            </div>
            <div class="stat-number">{{ $card['count'] ?? 0 }}</div>
            <div class="stat-label">{{ $card['label'] }}</div>
            <div class="stat-progress mt-3">
                <div class="progress-bar-custom" style="width: {{ min(100, ($card['count'] / max($stats['total'], 1)) * 100) }}%; background: {{ $card['gradient'] }}"></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

    {{-- Quick Stats Row --}}
    <div class="quick-stats mb-4 animate-card" style="animation-delay: 0.3s;">
        <div class="quick-stat-item">
            <i class="bi bi-calendar-check text-primary fs-4"></i>
            <div class="fw-bold">{{ \Carbon\Carbon::now()->format('F j, Y') }}</div>
            <small class="text-muted">Current Date</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-star-fill text-warning fs-4"></i>
            <div class="fw-bold">{{ $stats['paid'] ?? 0 }}/{{ $stats['total'] ?? 0 }}</div>
            <small class="text-muted">Payment Rate</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-trophy-fill text-success fs-4"></i>
            <div class="fw-bold">{{ $stats['paid'] ?? 0 }}</div>
            <small class="text-muted">Settled Bills</small>
        </div>
        <div class="quick-stat-item">
            <i class="bi bi-clock-fill text-info fs-4"></i>
            <div class="fw-bold">{{ $stats['pending'] ?? 0 }}</div>
            <small class="text-muted">Awaiting Payment</small>
        </div>
    </div>

    {{-- Invoices Section --}}
    <div class="premium-card animate-card" style="animation-delay: 0.4s;">
        <div class="p-3 border-bottom bg-transparent d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Invoice History</h5>
                <p class="text-muted small mb-0">Complete record of your billing transactions</p>
            </div>
            <div class="input-group" style="width: 250px;">
                <input type="text" id="invoiceSearch" class="form-control form-control-sm rounded-pill" placeholder="Search invoices...">
                <i class="bi bi-search position-absolute end-0 top-50 translate-middle-y me-3"></i>
            </div>
        </div>

        {{-- Desktop Table View --}}
        <div class="desktop-table table-container">
            <table class="invoices-table" id="invoiceTable">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Type</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
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
                    <tr data-id="{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}">
                        <td>
                            <div class="fw-semibold">{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2">
                                <i class="bi bi-file-text me-1"></i>{{ ucfirst($invoice->invoice_type ?? 'Internship') }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold">PKR {{ number_format($invoice->total_amount, 0) }}</div>
                        </td>
                        <td>
                            <span class="text-success">PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</span>
                        </td>
                        <td>
                            <span class="{{ $remaining <= 0 ? 'text-success' : 'text-danger fw-bold' }}">
                                PKR {{ number_format($remaining, 0) }}
                            </span>
                        </td>
                        <td>
                            <div>
                                <span class="{{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && $remaining > 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                                </span>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge-custom badge-{{ $badge }}">
                                <i class="bi bi-{{ $statusIcon }} me-1"></i>
                                {{ $status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('intern.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-receipt fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">No invoices found</p>
                            <small class="text-muted">Your invoices will appear here</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards View --}}
        <div class="mobile-cards-view p-3">
            @forelse($invoices as $invoice)
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
            <div class="invoice-mobile-card" data-id="{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="fw-semibold">{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</small>
                    </div>
                    <span class="badge-custom badge-{{ $badge }}">
                        <i class="bi bi-{{ $statusIcon }} me-1"></i>
                        {{ $status }}
                    </span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Type:</span>
                    <span>{{ ucfirst($invoice->invoice_type ?? 'Internship') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total:</span>
                    <span class="fw-semibold">PKR {{ number_format($invoice->total_amount, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Paid:</span>
                    <span class="text-success">PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Remaining:</span>
                    <span class="{{ $remaining <= 0 ? 'text-success' : 'text-danger' }}">PKR {{ number_format($remaining, 0) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Due Date:</span>
                    <span class="{{ \Carbon\Carbon::parse($invoice->due_date)->isPast() && $remaining > 0 ? 'text-danger' : '' }}">
                        {{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}
                        <small class="text-muted">({{ \Carbon\Carbon::parse($invoice->due_date)->diffForHumans() }})</small>
                    </span>
                </div>
                <div class="mt-2">
                    <a href="{{ route('intern.invoices.show', $invoice->id) }}" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                        <i class="bi bi-eye"></i> View Details
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="bi bi-receipt fs-1 text-muted"></i>
                <p class="mt-2 text-muted">No invoices found</p>
            </div>
            @endforelse
        </div>

        @if($invoices->hasPages())
        <div class="p-3 border-top bg-transparent">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>

<script>
// Search functionality
document.getElementById('invoiceSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    
    // Desktop table rows
    const rows = document.querySelectorAll('#invoiceTable tbody tr');
    rows.forEach(row => {
        const id = row.dataset?.id || '';
        if (id.toLowerCase().includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    // Mobile cards
    const cards = document.querySelectorAll('.invoice-mobile-card');
    cards.forEach(card => {
        const id = card.dataset?.id || '';
        if (id.toLowerCase().includes(searchTerm)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
@endsection