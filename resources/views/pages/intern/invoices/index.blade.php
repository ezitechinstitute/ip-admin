@extends('layouts/layoutMaster')

@section('title', 'My Invoices')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Statistics Cards --}}
    <div class="row g-4 mb-4">
        @php
            $cards = [
                ['icon'=>'ti-wallet','color'=>'success','title'=>'Paid Invoices','value'=>$stats['paid'] ?? 0],
                ['icon'=>'ti-clock','color'=>'warning','title'=>'Pending Invoices','value'=>$stats['pending'] ?? 0],
                ['icon'=>'ti-alert-triangle','color'=>'danger','title'=>'Overdue Invoices','value'=>$stats['overdue'] ?? 0],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-md-4 col-6">
            <div class="card shadow-sm rounded-4 text-center hover-scale p-3">
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <div class="bg-{{ $card['color'] }} bg-opacity-10 rounded-circle p-3">
                        <i class="{{ $card['icon'] }} fs-2 text-{{ $card['color'] }}"></i>
                    </div>
                </div>
                <h3 class="mb-0 fw-bold">{{ $card['value'] }}</h3>
                <small class="text-muted">{{ $card['title'] }}</small>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Invoice History Table --}}
    <div class="card shadow-sm rounded-4 hover-scale">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-bold">Invoice History</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
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
                        @php
                            $remaining = $invoice->remaining_amount ?? $invoice->total_amount;
                            if ($remaining <= 0) { $status='Paid'; $badge='success'; }
                            elseif (\Carbon\Carbon::parse($invoice->due_date)->isPast()) { $status='Overdue'; $badge='danger'; }
                            else { $status='Pending'; $badge='warning'; }
                        @endphp
                        <tr class="hover-scale">
                            <td class="fw-medium">{{ $invoice->inv_id ?? 'INV-'.$invoice->id }}</td>
                            <td>{{ ucfirst($invoice->invoice_type ?? 'Internship') }}</td>
                            <td class="fw-bold">PKR {{ number_format($invoice->total_amount, 0) }}</td>
                            <td>PKR {{ number_format($invoice->received_amount ?? 0, 0) }}</td>
                            <td class="fw-bold">PKR {{ number_format($remaining, 0) }}</td>
                            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $badge }}">{{ $status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('intern.invoices.show', $invoice->id) }}" class="btn btn-sm btn-primary rounded-pill">
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

        {{-- Pagination --}}
        <div class="card-footer d-flex justify-content-end">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<style>
.card.hover-scale {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.card.hover-scale:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.badge.rounded-pill {
    padding: 0.5em 0.75em;
    font-size: 0.85rem;
}
</style>
@endsection