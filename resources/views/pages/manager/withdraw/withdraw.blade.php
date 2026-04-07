@php
$configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Withdraw / Payout Request')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
])
@endsection

@section('page-style')
@vite('resources/assets/vendor/scss/pages/cards-advance.scss')
<style>
  /* ── Status pill colours ── */
  .badge-pending   { background: #fff3cd; color: #856404; }
  .badge-approved  { background: #d1e7dd; color: #0a3622; }
  .badge-rejected  { background: #f8d7da; color: #842029; }
  .badge-paid      { background: #cfe2ff; color: #084298; }

  /* ── Timeline ── */
  .timeline-item { position: relative; padding-left: 2rem; margin-bottom: 1.5rem; }
  .timeline-item::before {
    content: '';
    position: absolute; left: .45rem; top: 1.6rem;
    width: 2px; bottom: -1.5rem;
    background: #e0e0e0;
  }
  .timeline-item:last-child::before { display: none; }
  .timeline-dot {
    position: absolute; left: 0; top: .35rem;
    width: 14px; height: 14px;
    border-radius: 50%; border: 2px solid #fff;
    box-shadow: 0 0 0 2px currentColor;
  }

  /* ── Progress steps ── */
  .step-bar { display: flex; gap: 0; }
  .step-bar .step {
    flex: 1; text-align: center;
    padding: .5rem .25rem;
    font-size: .72rem; font-weight: 600;
    color: #9e9e9e;
    border-bottom: 3px solid #e0e0e0;
    transition: all .3s;
  }
  .step-bar .step.done   { color: #28a745; border-color: #28a745; }
  .step-bar .step.active { color: #696cff; border-color: #696cff; }

  /* ── Stat card accent ── */
  .kpi-accent { border-left: 4px solid; border-radius: .375rem; }
  .kpi-accent.purple { border-color: #696cff; }
  .kpi-accent.green  { border-color: #28a745; }
  .kpi-accent.orange { border-color: #fd7e14; }
  .kpi-accent.red    { border-color: #dc3545; }

  /* ── Amount input highlight ── */
  #withdrawAmount:focus { border-color: #696cff; box-shadow: 0 0 0 .2rem rgba(105,108,255,.2); }

  /* ── Freeze alert ── */
  .freeze-banner {
    background: linear-gradient(135deg,#fff3e0,#ffe0b2);
    border-left: 5px solid #fd7e14;
    border-radius: .5rem;
    padding: 1rem 1.25rem;
  }
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/apex-charts/apexcharts.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
])
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
<script>
document.addEventListener('DOMContentLoaded', function () {

  /* ── Flatpickr date ── */
  flatpickr('#withdrawPeriod', { mode: 'range', dateFormat: 'Y-m-d', allowInput: true });

  /* ── Available balance guard ── */
  const availableBalance = {{ $availableBalance ?? 0 }};
  const amountInput = document.getElementById('withdrawAmount');
  const balanceHint = document.getElementById('balanceHint');

  if (amountInput) {
    amountInput.addEventListener('input', function () {
      const val = parseFloat(this.value) || 0;
      if (val > availableBalance) {
        this.classList.add('is-invalid');
        balanceHint.textContent = '⚠ Amount exceeds available balance.';
        balanceHint.className = 'form-text text-danger';
      } else {
        this.classList.remove('is-invalid');
        balanceHint.textContent = `Available: PKR ${availableBalance.toLocaleString()}`;
        balanceHint.className = 'form-text text-muted';
      }
    });
  }

  /* ── Confirm submission ── */
  const withdrawForm = document.getElementById('withdrawForm');
  if (withdrawForm) {
    withdrawForm.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: 'Submit Payout Request?',
        text: 'Your request will be reviewed by Admin before processing.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Submit',
      }).then((result) => {
        if (result.isConfirmed) {
          withdrawForm.submit();
        }
      });
    });
  }

  /* ── Mini sparkline chart ── */
  const sparkOptions = {
    chart: { type: 'area', height: 60, sparkline: { enabled: true } },
    series: [{ data: [800, 1200, 900, 1500, 1100, 1800, 2200] }],
    colors: ['#696cff'],
    stroke: { curve: 'smooth', width: 2 },
    fill: { opacity: .15 },
    tooltip: { fixed: { enabled: false }, x: { show: false }, marker: { show: false } }
  };
  if (document.getElementById('commissionSparkline')) {
    new ApexCharts(document.querySelector('#commissionSparkline'), sparkOptions).render();
  }

  /* ── DataTable for history ── */
  if ($.fn.DataTable) {
    $('#withdrawHistoryTable').DataTable({
      responsive: true,
      pageLength: 10,
      order: [[0, 'desc']],
      columnDefs: [{ orderable: false, targets: -1 }],
      language: { search: '', searchPlaceholder: '🔍  Search requests...' }
    });
  }
});
</script>
@endsection

@section('content')

{{-- ───────────────────────────────────────────────────
     PAGE HEADER
────────────────────────────────────────────────────── --}}
<div class="row mb-4">
  <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <h4 class="mb-0">💸 Withdraw / Payout Request</h4>
      <small class="text-muted">Manage your commission withdrawals and track payout status</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newWithdrawModal">
      <i class="ti ti-plus me-1"></i> New Withdraw Request
    </button>
  </div>
</div>

{{-- ───────────────────────────────────────────────────
     FREEZE BANNER (conditional)
────────────────────────────────────────────────────── --}}
@if(isset($pendingRequest) && $pendingRequest)
<div class="freeze-banner mb-4 d-flex align-items-center gap-3">
  <span style="font-size:1.6rem;">⏳</span>
  <div>
    <strong>Request Under Review</strong><br>
    <small class="text-muted">You have a pending payout request of <strong>PKR {{ number_format($pendingRequest->amount) }}</strong>. A new request cannot be submitted until Admin processes the current one.</small>
  </div>
</div>
@endif

{{-- ───────────────────────────────────────────────────
     KPI CARDS
────────────────────────────────────────────────────── --}}
@php
$kpis = [
  ['label' => 'Total Commission Earned', 'value' => 'PKR '.number_format($totalCommission ?? 0),  'icon' => '🏆', 'accent' => 'purple'],
  ['label' => 'Total Withdrawn',          'value' => 'PKR '.number_format($totalWithdrawn ?? 0),   'icon' => '💸', 'accent' => 'green'],
  ['label' => 'Available Balance',        'value' => 'PKR '.number_format($availableBalance ?? 0), 'icon' => '💰', 'accent' => 'orange'],
  ['label' => 'Pending Requests',         'value' => $pendingCount ?? '0',                          'icon' => '🔄', 'accent' => 'red'],
];
@endphp

<div class="row g-4 mb-4">
  @foreach($kpis as $kpi)
  <div class="col-lg-3 col-md-6 col-12">
    <div class="card h-100 shadow-sm kpi-accent {{ $kpi['accent'] }}">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2">
          <div class="me-3" style="font-size:26px;">{{ $kpi['icon'] }}</div>
          <h5 class="mb-0">{{ $kpi['value'] }}</h5>
        </div>
        <p class="mb-0 text-muted small">{{ $kpi['label'] }}</p>
      </div>
    </div>
  </div>
  @endforeach
</div>

{{-- ───────────────────────────────────────────────────
     MAIN ROW  (Summary Chart | Request History)
────────────────────────────────────────────────────── --}}
<div class="row g-4 mb-4">

  {{-- LEFT – Commission Summary Card --}}
  <div class="col-lg-4 col-12">
    <div class="card h-100">
      <div class="card-body">
        <h6 class="card-title mb-1">Commission Summary</h6>
        <small class="text-muted">7-day earning trend</small>
        <div id="commissionSparkline" class="my-3"></div>
        <hr>

        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Total Earned</span>
          <strong>PKR {{ number_format($totalCommission ?? 0) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Already Withdrawn</span>
          <strong class="text-success">PKR {{ number_format($totalWithdrawn ?? 0) }}</strong>
        </div>
        <div class="d-flex justify-content-between mb-3">
          <span class="text-muted small">Available to Withdraw</span>
          <strong class="text-primary">PKR {{ number_format($availableBalance ?? 0) }}</strong>
        </div>

        {{-- Balance progress bar --}}
        @php
          $pct = ($totalCommission > 0) ? round(($totalWithdrawn / $totalCommission) * 100) : 0;
        @endphp
        <div class="mb-1 d-flex justify-content-between small">
          <span>Withdrawn</span><span>{{ $pct }}%</span>
        </div>
        <div class="progress mb-4" style="height:8px">
          <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
        </div>

        <div class="d-grid">
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newWithdrawModal"
            {{ isset($pendingRequest) && $pendingRequest ? 'disabled' : '' }}>
            <i class="ti ti-cash me-1"></i> Request Payout
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- RIGHT – Withdraw History Table --}}
  <div class="col-lg-8 col-12">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">Payout Request History</h5>
        <div class="dropdown">
          <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
            <i class="ti ti-filter me-1"></i> Filter
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item filter-status" href="#" data-status="all">All</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-status="Pending">Pending</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-status="Approved">Approved</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-status="Rejected">Rejected</a></li>
            <li><a class="dropdown-item filter-status" href="#" data-status="Paid">Paid</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="withdrawHistoryTable">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Date</th>
                <th>Period</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($withdrawRequests ?? [] as $request)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}</td>
                <td><small class="text-muted">{{ $request->period }}</small></td>
                <td><strong>PKR {{ number_format($request->amount) }}</strong></td>
                <td>
                  <span class="text-truncate d-inline-block" style="max-width:140px;"
                        title="{{ $request->description }}">
                    {{ $request->description ?? '—' }}
                  </span>
                </td>
                <td>
                  @php
                    $badgeMap = [
                      'Pending'  => 'badge-pending',
                      'Approved' => 'badge-approved',
                      'Rejected' => 'badge-rejected',
                      'Paid'     => 'badge-paid',
                    ];
                    $badgeClass = $badgeMap[$request->status] ?? 'bg-secondary text-white';
                  @endphp
                  <span class="badge rounded-pill {{ $badgeClass }} px-3 py-1">
                    {{ $request->status }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-sm btn-icon btn-text-secondary"
                          data-bs-toggle="modal"
                          data-bs-target="#detailModal"
                          data-id="{{ $request->id }}"
                          data-amount="{{ number_format($request->amount) }}"
                          data-period="{{ $request->period }}"
                          data-status="{{ $request->status }}"
                          data-desc="{{ $request->description }}"
                          data-admin-note="{{ $request->admin_note ?? '' }}"
                          data-date="{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}"
                          title="View Details">
                    <i class="ti ti-eye"></i>
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <span style="font-size:2rem;">📭</span><br>
                  No payout requests yet. Submit your first request!
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ───────────────────────────────────────────────────
     PAYOUT FLOW STEPS BANNER
────────────────────────────────────────────────────── --}}
<div class="card mb-4">
  <div class="card-body pb-0">
    <p class="text-muted small mb-2">📋 Payout Request Flow</p>
    <div class="step-bar">
      <div class="step done">1. Submit Request</div>
      <div class="step {{ isset($pendingRequest) && $pendingRequest ? 'active' : (isset($anyRequest) && $anyRequest ? 'done' : '') }}">2. Admin Review</div>
      <div class="step {{ isset($approvedRequest) && $approvedRequest ? 'active' : '' }}">3. Approval</div>
      <div class="step {{ isset($paidRequest) && $paidRequest ? 'done' : '' }}">4. Paid to Account</div>
    </div>
  </div>
</div>

{{-- ───────────────────────────────────────────────────
     MODAL: NEW WITHDRAW REQUEST
────────────────────────────────────────────────────── --}}
<div class="modal fade" id="newWithdrawModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">💸 New Payout Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="withdrawForm" action="{{ route('manager.withdraw.store') }}" method="POST">
        @csrf
        <div class="modal-body">

          {{-- Available balance badge --}}
          <div class="alert alert-primary d-flex align-items-center gap-2 py-2 mb-4" role="alert">
            <i class="ti ti-info-circle fs-5"></i>
            <span>Available Balance: <strong>PKR {{ number_format($availableBalance ?? 0) }}</strong></span>
          </div>

          {{-- Amount --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" for="withdrawAmount">
              Requested Amount <span class="text-danger">*</span>
            </label>
            <div class="input-group">
              <span class="input-group-text">PKR</span>
              <input type="number" class="form-control" id="withdrawAmount" name="amount"
                     placeholder="Enter amount" min="1"
                     max="{{ $availableBalance ?? 9999999 }}" required>
            </div>
            <small id="balanceHint" class="form-text text-muted">
              Available: PKR {{ number_format($availableBalance ?? 0) }}
            </small>
          </div>

          {{-- Period --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" for="withdrawPeriod">
              Period <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="withdrawPeriod" name="period"
                   placeholder="e.g. 01 Jan – 31 Jan 2025" required>
            <small class="form-text text-muted">Select the date range this payout covers.</small>
          </div>

          {{-- Payment Method --}}
          <div class="mb-3">
            <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
            <div class="row g-2">
              @foreach(['Bank Transfer','JazzCash','EasyPaisa','Other'] as $method)
              <div class="col-6">
                <div class="form-check border rounded p-2">
                  <input class="form-check-input" type="radio" name="payment_method"
                         id="method{{ $loop->index }}" value="{{ $method }}"
                         {{ $loop->first ? 'checked' : '' }} required>
                  <label class="form-check-label" for="method{{ $loop->index }}">
                    {{ $method }}
                  </label>
                </div>
              </div>
              @endforeach
            </div>
          </div>

          {{-- Account Details --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" for="accountDetails">
              Account / Wallet Details <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control" id="accountDetails" name="account_details"
                   placeholder="Account number / IBAN / Wallet number" required>
          </div>

          {{-- Description --}}
          <div class="mb-1">
            <label class="form-label fw-semibold" for="withdrawDesc">Description / Note</label>
            <textarea class="form-control" id="withdrawDesc" name="description" rows="3"
                      placeholder="Optional: reason or context for this payout request..."></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-send me-1"></i> Submit Request
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

{{-- ───────────────────────────────────────────────────
     MODAL: REQUEST DETAIL VIEW
────────────────────────────────────────────────────── --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">📄 Request Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        {{-- Status Step --}}
        <div class="step-bar mb-4" id="detailStepBar">
          <div class="step done">Submitted</div>
          <div class="step" id="stepReview">Admin Review</div>
          <div class="step" id="stepApproval">Approved</div>
          <div class="step" id="stepPaid">Paid</div>
        </div>

        {{-- Info rows --}}
        <table class="table table-sm">
          <tbody>
            <tr><td class="text-muted">Request Date</td><td id="dDate" class="fw-semibold"></td></tr>
            <tr><td class="text-muted">Period</td><td id="dPeriod"></td></tr>
            <tr><td class="text-muted">Amount</td><td id="dAmount" class="fw-bold text-primary"></td></tr>
            <tr><td class="text-muted">Status</td><td id="dStatus"></td></tr>
            <tr><td class="text-muted">Description</td><td id="dDesc" class="small"></td></tr>
            <tr><td class="text-muted">Admin Note</td><td id="dAdminNote" class="small text-danger"></td></tr>
          </tbody>
        </table>

        {{-- Timeline --}}
        <h6 class="mt-3 mb-3">Activity Timeline</h6>
        <div class="timeline-item">
          <div class="timeline-dot" style="color:#696cff; background:#696cff;"></div>
          <strong>Request Submitted</strong>
          <p class="text-muted small mb-0" id="tDate"></p>
        </div>
        <div class="timeline-item" id="timelineReview" style="display:none">
          <div class="timeline-dot" style="color:#fd7e14; background:#fd7e14;"></div>
          <strong>Admin Reviewing</strong>
          <p class="text-muted small mb-0">Your request is currently under admin review.</p>
        </div>
        <div class="timeline-item" id="timelineApproved" style="display:none">
          <div class="timeline-dot" style="color:#28a745; background:#28a745;"></div>
          <strong>Request Approved</strong>
          <p class="text-muted small mb-0">Admin has approved your payout request.</p>
        </div>
        <div class="timeline-item" id="timelineRejected" style="display:none">
          <div class="timeline-dot" style="color:#dc3545; background:#dc3545;"></div>
          <strong>Request Rejected</strong>
          <p class="text-muted small mb-0" id="tRejectNote"></p>
        </div>
        <div class="timeline-item" id="timelinePaid" style="display:none">
          <div class="timeline-dot" style="color:#0d6efd; background:#0d6efd;"></div>
          <strong>Payment Transferred</strong>
          <p class="text-muted small mb-0">Your commission has been credited to your account.</p>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

@endsection

@push('page-script')
<script>
/* ── Detail Modal population ── */
document.getElementById('detailModal').addEventListener('show.bs.modal', function (e) {
  const btn = e.relatedTarget;
  if (!btn) return;

  const status     = btn.dataset.status;
  const amount     = btn.dataset.amount;
  const period     = btn.dataset.period;
  const desc       = btn.dataset.desc   || '—';
  const adminNote  = btn.dataset.adminNote || '—';
  const date       = btn.dataset.date;

  /* Populate table */
  document.getElementById('dDate').textContent      = date;
  document.getElementById('dPeriod').textContent    = period;
  document.getElementById('dAmount').textContent    = 'PKR ' + amount;
  document.getElementById('dDesc').textContent      = desc;
  document.getElementById('dAdminNote').textContent = adminNote;

  /* Status badge */
  const badgeMap = {
    Pending  : 'badge-pending',
    Approved : 'badge-approved',
    Rejected : 'badge-rejected',
    Paid     : 'badge-paid',
  };
  document.getElementById('dStatus').innerHTML =
    `<span class="badge rounded-pill ${badgeMap[status] ?? ''} px-3 py-1">${status}</span>`;

  /* Timeline date */
  document.getElementById('tDate').textContent = date;

  /* Show/hide timeline steps */
  const show = id => document.getElementById(id).style.display = '';
  const hide = id => document.getElementById(id).style.display = 'none';

  ['timelineReview','timelineApproved','timelineRejected','timelinePaid'].forEach(hide);

  const stepReview   = document.getElementById('stepReview');
  const stepApproval = document.getElementById('stepApproval');
  const stepPaid     = document.getElementById('stepPaid');

  stepReview.className   = 'step';
  stepApproval.className = 'step';
  stepPaid.className     = 'step';

  if (status === 'Pending') {
    show('timelineReview');
    stepReview.classList.add('active');
  } else if (status === 'Approved') {
    show('timelineReview');
    show('timelineApproved');
    stepReview.classList.add('done');
    stepApproval.classList.add('active');
  } else if (status === 'Rejected') {
    show('timelineReview');
    show('timelineRejected');
    document.getElementById('tRejectNote').textContent = 'Reason: ' + adminNote;
    stepReview.classList.add('done');
  } else if (status === 'Paid') {
    show('timelineReview');
    show('timelineApproved');
    show('timelinePaid');
    stepReview.classList.add('done');
    stepApproval.classList.add('done');
    stepPaid.classList.add('done');
  }
});
</script>
@endpush