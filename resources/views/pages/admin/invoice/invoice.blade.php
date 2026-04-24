@extends('layouts/layoutMaster')

@section('title', 'Invoice Management')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('content')
<style>
:root {
  --inv-primary: #2b9a82;
  --inv-primary-light: #e8f5f2;
  --inv-primary-soft: rgba(43, 154, 130, 0.12);
  --inv-card-radius: 16px;
  --inv-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
}

.inv-card {
  background: #fff;
  border: none;
  border-radius: var(--inv-card-radius);
  box-shadow: var(--inv-shadow);
  transition: all 0.3s ease;
  height: 100%;
}
.inv-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* Hero Section - Admin Version */
.hero {
  background: linear-gradient(130deg, #fff 50%, var(--inv-primary-light) 100%);
  border-radius: var(--inv-card-radius);
  padding: 1.5rem 1.65rem;
  position: relative;
  overflow: hidden;
  height: 100%;
}
.hero::after {
  content: '';
  position: absolute;
  right: -50px;
  top: -50px;
  width: 220px;
  height: 220px;
  border-radius: 50%;
  background: rgba(43, 154, 130, 0.07);
  pointer-events: none;
}
.hero h5 {
  font-size: 1.08rem;
  font-weight: 800;
  color: #1e2537;
  position: relative;
  z-index: 1;
}
.hero p {
  font-size: 0.79rem;
  color: #8892a4;
  position: relative;
  z-index: 1;
}

/* Ring Card Styles */
.ring-card {
  text-align: center;
  padding: 1.25rem;
}
.ring-value {
  font-size: 1.3rem;
  font-weight: 800;
  color: #1e2537;
  margin-top: 0.5rem;
}
.ring-label {
  font-size: 0.7rem;
  color: #8892a4;
  margin-top: 0.25rem;
}
.ring-percent {
  font-size: 0.6rem;
  font-weight: 600;
  margin-top: 0.5rem;
  padding: 2px 8px;
  border-radius: 12px;
  display: inline-block;
}
.percent-up { background: #e6f6f4; color: #2b9a82; }
.percent-down { background: #fff1f2; color: #e11d48; }
.percent-neutral { background: #eff6ff; color: #3b82f6; }

.filter-input-icon {
  position: relative;
}
.filter-input-icon .ti {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ea5b0;
  z-index: 2;
  font-size: 1rem;
}
.filter-input-icon .form-control,
.filter-input-icon .form-select {
  padding-left: 36px;
  border-radius: 10px;
  border: 1.5px solid #e5e8ec;
  font-size: 0.85rem;
}
.filter-input-icon .form-control:focus,
.filter-input-icon .form-select:focus {
  border-color: var(--inv-primary);
  box-shadow: 0 0 0 3px var(--inv-primary-soft);
  outline: none;
}

.badge-soft-success { background: #dcfce7; color: #16a34a; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
.badge-soft-warning { background: #fef9c3; color: #a16207; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
.badge-soft-danger { background: #fee2e2; color: #b91c1c; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
.badge-soft-primary { background: #eff6ff; color: #1d4ed8; padding: 5px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }

.ikt thead th {
  background: #f8f9fc;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 1rem;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}
.ikt tbody td {
  padding: 0.85rem 1rem;
  vertical-align: middle;
  border-bottom: 1px solid #f1f5f9;
}
.ikt tbody tr:hover td {
  background: #f8fffe;
}

.empty-ico {
  width: 66px;
  height: 66px;
  border-radius: 50%;
  background: var(--inv-primary-light);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.75rem;
}

/* Chart container for rings */
.wov-chart {
  width: 100px;
  height: 100px;
  margin: 0 auto;
}
</style>

<div class="container-xxl flex-grow-1 container-p-y">

  {{-- Header --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold mb-1">Invoice Management</h4>
      <p class="text-muted mb-0">Review, approve and track all internship invoices</p>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="ti ti-circle-check fs-4"></i>
      <div>{{ session('success') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
    <div class="d-flex align-items-center gap-2">
      <i class="ti ti-alert-circle fs-4"></i>
      <div>{{ session('error') }}</div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  {{-- Hero + Financial Rings Row --}}
  <div class="row g-4 mb-4">
    
    {{-- Hero Section - Admin Version (No Tutorial Button) --}}
    <div class="col-xl-5 col-lg-5">
      <div class="hero">
        <div class="d-flex justify-content-between align-items-start">
          <div style="position: relative; z-index: 1; max-width: 58%;">
            <h5>Invoice Oversight Dashboard</h5>
            <p class="mb-0">
              Review, approve, and manage all internship invoices<br>
              from a single centralized dashboard.
            </p>
          </div>
          <div style="position: relative; z-index: 1; flex-shrink: 0;">
            <svg width="100" height="100" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="60" cy="60" r="60" fill="rgba(43,154,130,.08)"/>
              <rect x="30" y="20" width="60" height="72" rx="8" fill="white" stroke="#2b9a82" stroke-width="1.8"/>
              <rect x="38" y="33" width="44" height="5" rx="2.5" fill="#e8f5f2"/>
              <rect x="38" y="45" width="32" height="3.5" rx="1.75" fill="#d1fae5"/>
              <rect x="38" y="55" width="36" height="3.5" rx="1.75" fill="#d1fae5"/>
              <rect x="38" y="65" width="24" height="3.5" rx="1.75" fill="#d1fae5"/>
              <circle cx="85" cy="88" r="20" fill="#2b9a82"/>
              <path d="M77 88l6 6 10-10" stroke="white" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    {{-- Financial Rings --}}
    <div class="col-xl-7 col-lg-7">
      <div class="row g-4 h-100">
        
        @php
          $totalAmountNum = floatval(str_replace(',', '', $totalAmount));
          $receivedAmountNum = floatval(str_replace(',', '', $receivedAmount));
          $remainingAmountNum = floatval(str_replace(',', '', $remainingAmount));
          
          $receivedPercent = $totalAmountNum > 0 ? round(($receivedAmountNum / $totalAmountNum) * 100) : 0;
          $remainingPercent = $totalAmountNum > 0 ? round(($remainingAmountNum / $totalAmountNum) * 100) : 0;
        @endphp

        {{-- Total Amount Ring --}}
        <div class="col-md-4">
          <div class="inv-card card h-100">
            <div class="ring-card">
              <div class="wov-chart" id="chartTotalAmount"></div>
              <div class="ring-value">PKR {{ number_format($totalAmountNum, 0) }}</div>
              <div class="ring-label">Total Amount</div>
              <div class="ring-percent percent-neutral">100% of total</div>
            </div>
          </div>
        </div>

        {{-- Received Amount Ring --}}
        <div class="col-md-4">
          <div class="inv-card card h-100">
            <div class="ring-card">
              <div class="wov-chart" id="chartReceivedAmount"></div>
              <div class="ring-value text-success">PKR {{ number_format($receivedAmountNum, 0) }}</div>
              <div class="ring-label">Received Amount</div>
              <div class="ring-percent percent-up">{{ $receivedPercent }}% of total</div>
            </div>
          </div>
        </div>

        {{-- Remaining Amount Ring --}}
        <div class="col-md-4">
          <div class="inv-card card h-100">
            <div class="ring-card">
              <div class="wov-chart" id="chartRemainingAmount"></div>
              <div class="ring-value text-warning">PKR {{ number_format($remainingAmountNum, 0) }}</div>
              <div class="ring-label">Remaining Amount</div>
              <div class="ring-percent percent-down">{{ $remainingPercent }}% of total</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Approval Queue Button --}}
  <div class="row mb-4">
    <div class="col-12">
      <div class="inv-card card">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <h5 class="mb-1">Invoice Approval Management</h5>
            <p class="text-muted mb-0 small">Review and approve invoices created by managers</p>
          </div>
          <a href="{{ route('admin.invoices.approval-queue') }}" class="btn btn-warning rounded-3 px-4">
            <i class="ti ti-clock me-1"></i>Pending Approvals
            @php
              $pendingApprovalCount = \App\Models\invoice::where('approval_status', 'pending')->count();
            @endphp
            @if($pendingApprovalCount > 0)
            <span class="badge bg-danger ms-1">{{ $pendingApprovalCount }}</span>
            @endif
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters Card --}}
  <div class="inv-card card mb-4">
    <div class="card-body p-4">
      <form method="GET" action="{{ route('invoice-page') }}" id="filterForm">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label small text-muted fw-semibold mb-1">SEARCH</label>
            <div class="filter-input-icon">
              <i class="ti ti-search"></i>
              <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search invoices..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1">STATUS</label>
            <div class="filter-input-icon">
              <i class="ti ti-tag"></i>
              <select name="status" id="statusFilter" class="form-select">
                <option value="">Select Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <label class="form-label small text-muted fw-semibold mb-1">PER PAGE</label>
            <div class="filter-input-icon">
              <i class="ti ti-list"></i>
              <select name="per_page" class="form-select" onchange="document.getElementById('perPageForm').submit()">
                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100 rounded-3" style="background: var(--inv-primary); border: none;">
              <i class="ti ti-adjustments-horizontal me-1"></i>Apply Filters
            </button>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-8">
            <div class="filter-input-icon">
              <i class="ti ti-file-description"></i>
              <select name="invoice_type" id="invoiceTypeFilter" class="form-select">
                <option value="">All Invoice Types</option>
                <option value="Internship" {{ request('invoice_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
                <option value="Course" {{ request('invoice_type') == 'Course' ? 'selected' : '' }}>Course</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <button type="button" class="btn btn-outline-secondary w-100 rounded-3" onclick="downloadInvoiceCSV()">
              <i class="ti ti-download me-1"></i>Export CSV
            </button>
          </div>
        </div>
      </form>
      <form id="perPageForm" method="GET" style="display: none;">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <input type="hidden" name="invoice_type" value="{{ request('invoice_type') }}">
      </form>
    </div>
  </div>

  {{-- Invoice Table --}}
  <div class="inv-card card">
    <div class="card-header bg-white border-bottom py-3 px-4">
      <h6 class="mb-0 fw-bold">Invoices</h6>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive" style="overflow-x: auto;">
        <table class="table ikt mb-0" style="min-width: 900px;">
          <thead>
            <tr>
              <th>Invoice ID</th>
              <th>Name</th>
              <th>Contact</th>
              <th>Due Date</th>
              <th>Total Amount</th>
              <th>Received</th>
              <th>Remaining</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($invoice as $invoices)
            @php
              $statusText = $invoices->status == 1 ? 'Approved' : 'Pending';
              $statusClass = $invoices->status == 1 ? 'success' : 'warning';
            @endphp
            <tr>
              <td class="fw-semibold">{{ $invoices->inv_id }}</td>
              <td>{{ $invoices->name ?? 'N/A' }}</td>
              <td>{{ $invoices->contact ?? 'N/A' }}</td>
              <td>{{ $invoices->due_date ?? 'N/A' }}</td>
              <td class="fw-semibold">PKR {{ number_format($invoices->total_amount, 2) }}</td>
              <td class="text-success">PKR {{ number_format($invoices->received_amount, 2) }}</td>
              <td class="text-warning">PKR {{ number_format($invoices->remaining_amount, 2) }}</td>
              <td><span class="badge-soft-{{ $statusClass }}">{{ $statusText }}</span></td>
            </tr>
            @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <div class="empty-ico mx-auto">
                  <i class="ti ti-file-invoice ti-xl" style="font-size: 2rem; color: var(--inv-primary);"></i>
                </div>
                <h6 class="fw-bold mb-1">No invoices found</h6>
                <p class="text-muted small mb-0">No invoice records available</p>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($invoice->hasPages())
      <div class="d-flex flex-wrap align-items-center justify-content-between px-4 py-3 border-top">
        <div class="small text-muted mb-2 mb-md-0">
          Showing <strong>{{ $invoice->firstItem() ?? 0 }}</strong> to <strong>{{ $invoice->lastItem() ?? 0 }}</strong> of <strong>{{ $invoice->total() ?? 0 }}</strong> entries
        </div>
        <div>
          {{ $invoice->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
      </div>
      @endif
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
<script>
// Financial Amount Rings
(function () {
    const commonOptions = {
        chart: { type: 'radialBar', height: 100, width: 100, sparkline: { enabled: true } },
        plotOptions: {
            radialBar: {
                hollow: { size: '65%' },
                track: { background: '#e2e8f0', opacity: 0.4, strokeWidth: '100%', margin: 0 },
                dataLabels: { show: false }
            }
        },
        stroke: { lineCap: 'round' }
    };

    const totalAmount = {{ $totalAmountNum ?? 0 }};
    const receivedAmount = {{ $receivedAmountNum ?? 0 }};
    const remainingAmount = {{ $remainingAmountNum ?? 0 }};

    const receivedPercent = totalAmount > 0 ? (receivedAmount / totalAmount) * 100 : 0;
    const remainingPercent = totalAmount > 0 ? (remainingAmount / totalAmount) * 100 : 0;

    new ApexCharts(document.querySelector("#chartTotalAmount"), {
        ...commonOptions,
        series: [100],
        colors: ['#3b82f6']
    }).render();

    new ApexCharts(document.querySelector("#chartReceivedAmount"), {
        ...commonOptions,
        series: [receivedPercent],
        colors: ['#16a34a']
    }).render();

    new ApexCharts(document.querySelector("#chartRemainingAmount"), {
        ...commonOptions,
        series: [remainingPercent],
        colors: ['#d97706']
    }).render();
})();

// Filters
let timer;
document.getElementById('searchInput')?.addEventListener('keyup', function() {
  clearTimeout(timer);
  timer = setTimeout(() => document.getElementById('filterForm').submit(), 500);
});
document.getElementById('statusFilter')?.addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});
document.getElementById('invoiceTypeFilter')?.addEventListener('change', function() {
  document.getElementById('filterForm').submit();
});

function downloadInvoiceCSV() {
  const search = document.getElementById('searchInput')?.value || '';
  const status = document.getElementById('statusFilter')?.value || '';
  let exportUrl = "{{ route('admin.export-invoices') }}";
  let params = new URLSearchParams({ search: search, status: status });
  window.location.href = exportUrl + "?" + params.toString();
}
</script>
@endpush
@endsection