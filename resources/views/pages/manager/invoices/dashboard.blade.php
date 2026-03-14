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
      {{-- FIXED: Changed from manager.invoices.create to invoices.create --}}
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
    {{-- FIXED: Changed from manager.invoices.dashboard to invoices.dashboard --}}
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
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Invoice ID</th>
            <th>Intern</th>
            <th>Contact</th>
            <th>Total</th>
            <th>Received</th>
            <th>Remaining</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $index => $invoice)
          @php
            $status = $invoice->remaining_amount <= 0 ? 'Paid' : 
                     ($invoice->due_date < now() ? 'Overdue' : 'Pending');
            $statusClass = $invoice->remaining_amount <= 0 ? 'success' : 
                          ($invoice->due_date < now() ? 'danger' : 'warning');
          @endphp
          <tr>
            <td>{{ $invoices->firstItem() + $index }}</td>
            <td><span class="badge bg-label-primary">{{ $invoice->inv_id }}</span></td>
            <td>
              <div class="fw-semibold">{{ $invoice->name }}</div>
              <small class="text-muted">{{ $invoice->intern_email }}</small>
            </td>
            <td>{{ $invoice->contact }}</td>
            <td class="fw-semibold">PKR {{ number_format($invoice->total_amount, 2) }}</td>
            <td class="text-success">PKR {{ number_format($invoice->received_amount, 2) }}</td>
            <td class="text-warning">PKR {{ number_format($invoice->remaining_amount, 2) }}</td>
            <td>{{ \Carbon\Carbon::parse($invoice->due_date)->format('d M Y') }}</td>
            <td>
              <span class="badge bg-{{ $statusClass }} text-white px-3 py-2">{{ $status }}</span>
            </td>
            <td>
              <div class="d-flex gap-2">
                {{-- FIXED: Changed from manager.invoices.view to invoices.view --}}
                <a href="{{ route('invoices.view', $invoice->id) }}" class="btn btn-sm btn-info">View</a>
                @if($invoice->remaining_amount > 0)
                {{-- FIXED: Changed from manager.invoices.payment to invoices.payment --}}
                <a href="{{ route('invoices.payment', $invoice->id) }}" class="btn btn-sm btn-success">Payment</a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center py-5">
              <i class="ti ti-file-invoice ti-3x text-muted mb-3"></i>
              <h6>No invoices found</h6>
              <p class="text-muted mb-3">Create your first invoice to get started</p>
              {{-- FIXED: Changed from manager.invoices.create to invoices.create --}}
              <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Create Invoice
              </a>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="row mt-4 justify-content-between align-items-center">
      <div class="col-md-auto">
        Showing {{ $invoices->firstItem() ?? 0 }} to {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() ?? 0 }} entries
      </div>
      <div class="col-md-auto">
        {{ $invoices->appends(request()->query())->links() }}
      </div>
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
  {{-- FIXED: Changed from manager.invoices.export to invoices.export --}}
  window.location.href = "{{ route('invoices.export') }}?" + params;
}
</script>
@endpush