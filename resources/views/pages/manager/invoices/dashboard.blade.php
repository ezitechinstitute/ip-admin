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
<div class="col-12 mb-6">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mt-6 mb-1 fw-bold">Invoice Management</h4>
      <p class="text-muted mb-0">Manage and track all internship invoices</p>
    </div>
    <div>
      <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i>Create New Invoice
      </a>
    </div>
  </div>
</div>

{{-- Statistics Cards --}}
<div class="row g-4 mb-6">
  <div class="col-xl-3 col-md-6">
    <div class="card border-start border-4 border-primary shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-primary bg-opacity-10 rounded p-3">
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
    <div class="card border-start border-4 border-success shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-success bg-opacity-10 rounded p-3">
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
    <div class="card border-start border-4 border-warning shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-warning bg-opacity-10 rounded p-3">
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
    <div class="card border-start border-4 border-danger shadow-sm">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <div class="avatar me-3 bg-danger bg-opacity-10 rounded p-3">
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
    <div class="card bg-primary text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="text-white-50 mb-2">Total Amount</h6>
            <h2 class="text-white mb-0 fw-bold">PKR {{ number_format($stats['total_amount'], 2) }}</h2>
          </div>
          <i class="ti ti-currency-rupee ti-3x opacity-50"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-4">
    <div class="card bg-success text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="text-white-50 mb-2">Received Amount</h6>
            <h2 class="text-white mb-0 fw-bold">PKR {{ number_format($stats['received_amount'], 2) }}</h2>
          </div>
          <i class="ti ti-wallet ti-3x opacity-50"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-4">
    <div class="card bg-warning text-white">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <h6 class="text-white-50 mb-2">Remaining Amount</h6>
            <h2 class="text-white mb-0 fw-bold">PKR {{ number_format($stats['remaining_amount'], 2) }}</h2>
          </div>
          <i class="ti ti-currency-rupee ti-3x opacity-50"></i>
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
        <div class="col-md-4">
          <input type="search" name="search" class="form-control" placeholder="Search by invoice ID, name or email" value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-outline-primary" onclick="exportInvoices()">
            <i class="ti ti-download me-1"></i>Export CSV
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Invoices Table --}}
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="bg-light">
          <tr>
            <th width="5%">#</th>
            <th width="12%">Invoice ID</th>
            <th width="18%">Intern</th>
            <th width="12%">Contact</th>
            <th width="10%">Total</th>
            <th width="10%">Received</th>
            <th width="10%">Remaining</th>
            <th width="10%">Due Date</th>
            <th width="8%">Status</th>
            <th width="15%">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $index => $invoice)
          @php
            $status = $invoice->remaining_amount <= 0 ? 'Paid' : 
                     ($invoice->due_date < now() ? 'Overdue' : 'Pending');
            $statusClass = $invoice->remaining_amount <= 0 ? 'success' : 
                          ($invoice->due_date < now() ? 'danger' : 'warning');
            
            // Format phone number
            $phone = $invoice->contact;
            if(strlen($phone) > 10) {
                $phone = substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7);
            }
          @endphp
          <tr>
            <td><span class="fw-medium">{{ $invoices->firstItem() + $index }}</span></td>
            <td><span class="badge bg-label-primary fs-6">{{ $invoice->inv_id }}</span></td>
            <td>
              <div class="fw-semibold">{{ $invoice->name }}</div>
              <small class="text-muted">{{ $invoice->intern_email }}</small>
            </td>
            <td>
              <div class="d-flex align-items-center">
                <i class="ti ti-phone-call me-1 text-muted" style="font-size: 0.8rem;"></i>
                <span>{{ $phone }}</span>
              </div>
            </td>
            <td class="fw-semibold">PKR {{ number_format($invoice->total_amount) }}</td>
            <td class="text-success">PKR {{ number_format($invoice->received_amount) }}</td>
            <td class="text-warning">PKR {{ number_format($invoice->remaining_amount) }}</td>
            <td>
              <div class="d-flex align-items-center">
                <i class="ti ti-calendar me-1 text-muted" style="font-size: 0.8rem;"></i>
                <span>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M, Y') }}</span>
              </div>
            </td>
            <td>
              <span class="badge bg-{{ $statusClass }} px-3 py-2 rounded-pill text-white">{{ $status }}</span>
            </td>
            <td>
              {{-- TEXT BUTTONS - WILL SHOW PROPERLY --}}
              <div class="d-flex gap-1 action-buttons">
                <!-- View Button with TEXT -->
                <a href="{{ route('invoices.view', $invoice->id) }}" 
                   class="btn btn-sm btn-outline-info px-3" 
                   data-bs-toggle="tooltip" 
                   title="View Invoice Details">
                  <i class="ti ti-eye me-1"></i> View
                </a>

                <!-- Payment Button with TEXT (only if remaining > 0) -->
                @if($invoice->remaining_amount > 0)
                <a href="{{ route('invoices.payment', $invoice->id) }}" 
                   class="btn btn-sm btn-outline-success px-3" 
                   data-bs-toggle="tooltip" 
                   title="Record Payment">
                  <i class="ti ti-cash me-1"></i> Pay
                </a>
                @endif

                <!-- PDF Button with TEXT -->
                <a href="{{ route('invoices.pdf', $invoice->id) }}" 
                   class="btn btn-sm btn-outline-primary px-3" 
                   target="_blank" 
                   data-bs-toggle="tooltip" 
                   title="Download PDF">
                  <i class="ti ti-file-text me-1"></i> PDF
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
    <div class="row mt-4 align-items-center">
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
  </div>
</div>
@endsection

@push('styles')
<style>
/* Action Buttons - TEXT BUTTONS */
.action-buttons {
    display: flex;
    flex-wrap: nowrap;
    gap: 4px;
    min-width: 180px;
}

.action-buttons .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    border-width: 1.5px;
    white-space: nowrap;
    border-radius: 4px;
}

.action-buttons .btn i {
    font-size: 0.8rem;
}

/* Button Colors */
.btn-outline-info {
    color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-info:hover {
    background-color: #0dcaf0;
    color: white;
}

.btn-outline-success {
    color: #198754;
    border-color: #198754;
}

.btn-outline-success:hover {
    background-color: #198754;
    color: white;
}

.btn-outline-primary {
    color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

/* Ensure table columns have proper width */
th:last-child {
    min-width: 180px;
}

td:last-child {
    min-width: 180px;
}

/* Status Badge */
.badge {
    font-weight: 500;
    font-size: 0.7rem;
    text-transform: capitalize;
}

/* Table Styling */
.table thead th {
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #495057;
    padding: 1rem 0.5rem;
}

.table tbody td {
    padding: 0.75rem 0.5rem;
    vertical-align: middle;
}

/* Hover Effect */
.table-hover tbody tr:hover {
    background-color: rgba(115, 103, 240, 0.02);
}
</style>
@endpush

@push('scripts')
<script>
function exportInvoices() {
  const form = document.getElementById('filterForm');
  const formData = new FormData(form);
  const params = new URLSearchParams(formData).toString();
  window.location.href = "{{ route('invoices.export') }}?" + params;
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush