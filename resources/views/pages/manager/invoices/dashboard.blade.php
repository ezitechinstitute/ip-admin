@extends('layouts/layoutMaster')

@section('title', 'Invoice Dashboard')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-1">Invoice Management</h4>
      <p class="text-muted mb-0">Manage and track all internship invoices</p>
    </div>
    <div>
      <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Create New Invoice
      </a>
    </div>
  </div>

  {{-- Alert Messages --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
      <i class="ti ti-check-circle fs-2 me-3 text-success"></i>
      <div>
        <strong class="d-block">Success!</strong>
        <span>{{ session('success') }}</span>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
      <i class="ti ti-alert-circle fs-2 me-3 text-danger"></i>
      <div>
        <strong class="d-block">Error!</strong>
        <span>{{ session('error') }}</span>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
  @endif

  {{-- Statistics Cards --}}
  <div class="row g-4 mb-6">
    <div class="col-xl-3 col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3 bg-label-primary rounded p-3">
              <i class="ti ti-file-invoice ti-lg text-primary"></i>
            </div>
            <div>
              <span class="text-muted d-block">Total Invoices</span>
              <h3 class="mb-0 fw-bold">{{ $stats['total'] }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3 bg-label-success rounded p-3">
              <i class="ti ti-check ti-lg text-success"></i>
            </div>
            <div>
              <span class="text-muted d-block">Paid Invoices</span>
              <h3 class="mb-0 fw-bold">{{ $stats['paid'] }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3 bg-label-warning rounded p-3">
              <i class="ti ti-clock ti-lg text-warning"></i>
            </div>
            <div>
              <span class="text-muted d-block">Pending</span>
              <h3 class="mb-0 fw-bold">{{ $stats['pending'] }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="avatar me-3 bg-label-danger rounded p-3">
              <i class="ti ti-alert-triangle ti-lg text-danger"></i>
            </div>
            <div>
              <span class="text-muted d-block">Overdue</span>
              <h3 class="mb-0 fw-bold">{{ $stats['overdue'] }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Financial Summary --}}
  <div class="row g-4 mb-6">
    <div class="col-xl-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="text-muted mb-2">Total Amount</h6>
              <h3 class="mb-0 fw-bold">PKR {{ number_format($stats['total_amount'], 2) }}</h3>
            </div>
            <i class="ti ti-currency-rupee ti-2x text-muted"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="text-muted mb-2">Received Amount</h6>
              <h3 class="mb-0 fw-bold text-success">PKR {{ number_format($stats['received_amount'], 2) }}</h3>
            </div>
            <i class="ti ti-wallet ti-2x text-muted"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-4">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <h6 class="text-muted mb-2">Remaining Amount</h6>
              <h3 class="mb-0 fw-bold text-warning">PKR {{ number_format($stats['remaining_amount'], 2) }}</h3>
            </div>
            <i class="ti ti-currency-rupee ti-2x text-muted"></i>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('invoices.dashboard') }}" id="filterForm">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">All</option>
              <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Invoice Type</label>
            <select name="invoice_type" class="form-select">
              <option value="">All</option>
              <option value="Internship" {{ request('invoice_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
              <option value="Course" {{ request('invoice_type') == 'Course' ? 'selected' : '' }}>Course</option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-8">
            <input type="search" name="search" class="form-control" placeholder="Search by invoice ID, name or email" value="{{ request('search') }}">
          </div>
          <div class="col-md-4">
            <button type="button" class="btn btn-outline-secondary w-100" onclick="exportInvoices()">
              <i class="ti ti-download me-1"></i>Export CSV
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Invoices Table - FIXED LAYOUT with NO WRAPPING --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
        <table class="table table-hover mb-0" style="min-width: 1200px; width: 100%;">
          <thead class="bg-light">
            <tr>
              <th style="width: 5%; white-space: nowrap;">#</th>
              <th style="width: 10%; white-space: nowrap;">Invoice ID</th>
              <th style="width: 18%; white-space: nowrap;">Intern</th>
              <th style="width: 10%; white-space: nowrap;">Contact</th>
              <th style="width: 10%; white-space: nowrap;">Total</th>
              <th style="width: 10%; white-space: nowrap;">Received</th>
              <th style="width: 10%; white-space: nowrap;">Remaining</th>
              <th style="width: 10%; white-space: nowrap;">Due Date</th>
              <th style="width: 8%; white-space: nowrap;">Status</th>
              <th style="width: 12%; white-space: nowrap;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoices as $index => $invoice)
            @php
              $status = $invoice->remaining_amount <= 0 ? 'Paid' : 
                       ($invoice->due_date < now() ? 'Overdue' : 'Pending');
              $statusClass = $invoice->remaining_amount <= 0 ? 'success' : 
                            ($invoice->due_date < now() ? 'danger' : 'warning');
              
              $phone = $invoice->contact;
              if(strlen($phone) > 10) {
                  $phone = substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
              }
            @endphp
            <tr>
              <td style="white-space: nowrap;">{{ $invoices->firstItem() + $index }}</td>
              <td style="white-space: nowrap;"><span class="badge bg-label-primary">{{ $invoice->inv_id }}</span></td>
              <td style="white-space: normal;">
                <div class="fw-semibold">{{ $invoice->name }}</div>
                <small class="text-muted">{{ $invoice->intern_email }}</small>
              </td>
              <td style="white-space: nowrap;">{{ $phone }}</td>
              <td style="white-space: nowrap;" class="fw-semibold">PKR {{ number_format($invoice->total_amount) }}</td>
              <td style="white-space: nowrap;" class="text-success">PKR {{ number_format($invoice->received_amount) }}</td>
              <td style="white-space: nowrap;" class="text-warning">PKR {{ number_format($invoice->remaining_amount) }}</td>
              <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</td>
              <td style="white-space: nowrap;"><span class="badge bg-{{ $statusClass }}">{{ $status }}</span></td>
              <td style="white-space: nowrap;">
                <div class="d-flex gap-1">
                  <a href="{{ route('invoices.view', $invoice->id) }}" 
                     class="btn btn-sm btn-outline-info" 
                     data-bs-toggle="tooltip" 
                     title="View Invoice Details">
                    <i class="ti ti-eye"></i> View
                  </a>
                  @if($invoice->remaining_amount > 0)
                  <a href="{{ route('invoices.payment', $invoice->id) }}" 
                     class="btn btn-sm btn-outline-success" 
                     data-bs-toggle="tooltip" 
                     title="Record Payment">
                    <i class="ti ti-cash"></i> Pay
                  </a>
                  @endif
                  <a href="{{ route('invoices.pdf', $invoice->id) }}" 
                     class="btn btn-sm btn-outline-primary" 
                     target="_blank" 
                     data-bs-toggle="tooltip" 
                     title="Download PDF">
                    <i class="ti ti-file-text"></i> PDF
                  </a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="10" class="text-center py-5">
                <div class="text-center">
                  <i class="ti ti-file-invoice ti-3x text-muted mb-3"></i>
                  <h5>No invoices found</h5>
                  <p class="text-muted mb-3">Create your first invoice to get started</p>
                  <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>Create Invoice
                  </a>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($invoices->hasPages())
      <div class="row mt-4 align-items-center px-4 pb-4">
        <div class="col-md-6">
          <p class="text-muted mb-0">
            Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() ?? 0 }} entries
          </p>
        </div>
        <div class="col-md-6">
          <div class="d-flex justify-content-end">
            {{ $invoices->appends(request()->query())->links() }}
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
function exportInvoices() {
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);
  const params = new URLSearchParams(formData).toString();
  window.location.href = "{{ route('invoices.export') }}?" + params;
}

document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush