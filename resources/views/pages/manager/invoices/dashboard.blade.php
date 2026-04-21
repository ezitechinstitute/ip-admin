@php
use Illuminate\Support\Str;

// These come from controller - DO NOT redeclare
// $chartData is passed from controller
// $recentTransactions is passed from controller

$chartSeries = $chartData['series'] ?? [];
$chartLabels = $chartData['labels'] ?? [];

/* ── Avatar colour pool ─────────────────────────── */
$avColours = ['#2b9a82','#7c3aed','#f97316','#0284c7','#e11d48','#d97706'];
$avIcons   = ['ti-currency-rupee','ti-receipt','ti-shopping-cart','ti-credit-card','ti-cash'];
@endphp

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
<style>
/* ─── Design tokens ─────────────────────────────────────────────────── */
:root{
  --p   :#2b9a82;
  --p-d :#1d7a66;
  --p-s :rgba(43,154,130,.13);
  --p-bg:#e8f5f2;
  --bg  :#f4f6fb;
  --card:#ffffff;
  --bd  :#eaecf2;
  --tx  :#1e2537;
  --mu  :#8892a4;
  --r   :14px;
  --sh  :0 2px 20px rgba(30,37,55,.07);
  --sh2 :0 8px 36px rgba(30,37,55,.13);
}

.ik{background:var(--bg);}

.k{
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r);
  box-shadow:var(--sh);
  transition:box-shadow .22s ease,transform .22s ease;
}
.k:hover{box-shadow:var(--sh2);transform:translateY(-2px);}

.kh{
  padding:.88rem 1.3rem;
  border-bottom:1px solid var(--bd);
  display:flex;align-items:center;justify-content:space-between;
  border-radius:var(--r) var(--r) 0 0;
}
.kh-t{font-size:.9rem;font-weight:700;color:var(--tx);margin:0;}

.pill{
  font-size:.67rem;font-weight:700;color:var(--mu);
  background:var(--bg);border:1px solid var(--bd);
  border-radius:20px;padding:.22em .9em;
  cursor:pointer;white-space:nowrap;letter-spacing:.02em;
  transition:border-color .15s;user-select:none;
}
.pill:hover{border-color:var(--p);color:var(--p);}

.btn-p{
  background:var(--p);color:#fff;border:none;
  border-radius:10px;font-size:.83rem;font-weight:600;
  padding:.46rem 1.15rem;
  transition:background .18s,box-shadow .18s;
  white-space:nowrap;
}
.btn-p:hover,.btn-p:focus{background:var(--p-d);box-shadow:0 4px 14px var(--p-s);color:#fff;}

.phead{
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r);
  box-shadow:var(--sh);
  padding:.82rem 1.4rem;
  display:flex;align-items:center;justify-content:space-between;
}
.phead-eye{
  font-size:.6rem;font-weight:700;letter-spacing:.14em;
  text-transform:uppercase;color:var(--mu);margin-bottom:.15rem;
}
.phead h5{font-size:1rem;font-weight:800;color:var(--tx);margin:0;}

/* ─── Filter Card (Above Invoice List) ─────────────────────────────── */
.filter-card{
  background:var(--card);
  border:1px solid var(--bd);
  border-radius:var(--r);
  box-shadow:var(--sh);
  margin-bottom:1.5rem;
  padding:1.25rem;
}
.filter-label{
  font-size:.7rem;
  font-weight:700;
  letter-spacing:.08em;
  text-transform:uppercase;
  color:var(--mu);
  margin-bottom:.3rem;
}
.filter-input-icon{
  position:relative;
}
.filter-input-icon .ti{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:var(--mu);
  z-index:2;
  font-size:1rem;
}
.filter-input-icon .form-control,
.filter-input-icon .form-select{
  padding-left:36px;
  border-radius:10px;
  border:1.5px solid var(--bd);
  font-size:.8rem;
  color:var(--tx);
  background:var(--card);
  transition:border-color .18s,box-shadow .18s;
}
.filter-input-icon .form-control:focus,
.filter-input-icon .form-select:focus{
  border-color:var(--p);
  box-shadow:0 0 0 3px var(--p-s);
  outline:none;
}
.search-input{
  border-radius:10px;
  border:1.5px solid var(--bd);
  padding:.45rem 1rem;
  font-size:.8rem;
}
.search-input:focus{
  border-color:var(--p);
  box-shadow:0 0 0 3px var(--p-s);
  outline:none;
}

.hero{
  background:linear-gradient(130deg,#fff 50%,var(--p-bg) 100%);
  border:1px solid var(--bd);
  border-radius:var(--r);
  box-shadow:var(--sh);
  padding:1.5rem 1.65rem;
  position:relative;overflow:hidden;
  height:100%;
}
.hero::after{
  content:'';position:absolute;right:-50px;top:-50px;
  width:220px;height:220px;border-radius:50%;
  background:rgba(43,154,130,.07);pointer-events:none;
}
.hero h5{font-size:1.08rem;font-weight:800;color:var(--tx);position:relative;z-index:1;}
.hero p{font-size:.79rem;color:var(--mu);position:relative;z-index:1;}

/* ═══ Week Overview ═══ */
.wov-n{font-size:1.5rem;font-weight:800;color:var(--tx);line-height:1;}
.wov-l{font-size:.71rem;color:var(--mu);margin-top:.22rem;}
.wov-chart { width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.wov-card { display: flex; align-items: center; justify-content: space-between; padding: 1.5rem; min-height: 140px; }
.percentage-tag { font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; margin-right: 5px; }
.tag-up-lite { background: #e6f6f4; color: #2b9a82; }
.tag-dn-lite { background: #fff1f2; color: #e11d48; }
.tag-in-lite { background: #eff6ff; color: #3b82f6; }

.txn{
  display:flex;align-items:center;gap:.72rem;
  padding:.6rem 0;border-bottom:1px solid var(--bd);
}
.txn:last-child{border-bottom:none;}
.txn-av{
  width:38px;height:38px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;
  font-size:.85rem;flex-shrink:0;color:#fff;
}
.txn-name{font-size:.81rem;font-weight:600;color:var(--tx);line-height:1.15;}
.txn-date{font-size:.68rem;color:var(--mu);}
.txn-amt{font-size:.81rem;font-weight:700;white-space:nowrap;}
.txn-pos{color:#16a34a;}
.txn-neg{color:#e11d48;}

.ikt thead th{
  background:#f8f9fc;color:var(--mu);
  font-size:.66rem;font-weight:700;letter-spacing:.09em;
  text-transform:uppercase;padding:.88rem 1rem;
  border-bottom:2px solid var(--bd);white-space:nowrap;
  position:sticky;top:0;z-index:1;
}
.ikt tbody td{
  padding:.82rem 1rem;vertical-align:middle;
  border-color:var(--bd);font-size:.81rem;color:var(--tx);
}
.ikt tbody tr{transition:background .15s;}
.ikt tbody tr:hover td{background:#f7fffe;}

.iav{
  width:34px;height:34px;border-radius:50%;
  background:var(--p-bg);color:var(--p);
  font-size:.67rem;font-weight:700;
  display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;
}

.ib{
  display:inline-block;font-size:.69rem;font-weight:700;
  padding:.27em .78em;border-radius:20px;white-space:nowrap;
}
.ib-s{background:#dcfce7;color:#15803d;}
.ib-w{background:#fef9c3;color:#a16207;}
.ib-d{background:#fee2e2;color:#b91c1c;}
.ib-p{background:#eff6ff;color:#1d4ed8;}

.iact{
  padding:.26rem .62rem;border-radius:8px;
  font-size:.75rem;font-weight:600;border-width:1.5px;
  white-space:nowrap;transition:all .15s;
}

.al-s{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:12px;}
.al-e{background:#fff1f2;border:1px solid #fecdd3;color:#9f1239;border-radius:12px;}
.al-ico{
  width:36px;height:36px;border-radius:50%;
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}

.empty-ico{
  width:66px;height:66px;border-radius:50%;
  background:var(--p-bg);
  display:inline-flex;align-items:center;justify-content:center;
  font-size:1.8rem;color:var(--p);margin-bottom:.75rem;
}

</style>

<div class="ik container-xxl flex-grow-1 container-p-y">

{{-- ══════════════════════════════════════════════════════
                    ALERTS
══════════════════════════════════════════════════════ --}}
@if(session('success'))
<div class="al-s d-flex align-items-center gap-3 p-3 mb-3 alert-dismissible fade show" role="alert">
  <div class="al-ico" style="background:#dcfce7;">
    <i class="ti ti-circle-check text-success fs-5"></i>
  </div>
  <div>
    <strong style="font-size:.82rem;display:block;">Success!</strong>
    <span style="font-size:.79rem;">{{ session('success') }}</span>
  </div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif
@if(session('error'))
<div class="al-e d-flex align-items-center gap-3 p-3 mb-3 alert-dismissible fade show" role="alert">
  <div class="al-ico" style="background:#fee2e2;">
    <i class="ti ti-alert-circle text-danger fs-5"></i>
  </div>
  <div>
    <strong style="font-size:.82rem;display:block;">Error!</strong>
    <span style="font-size:.79rem;">{{ session('error') }}</span>
  </div>
  <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- ══════════════════════════════════════════════════════
     PAGE HEADER
══════════════════════════════════════════════════════ --}}
<div class="phead mb-4">
  <div>
    <div class="phead-eye">Invoice Management</div>
    <h5>Manage &amp; track all internship invoices</h5>
  </div>
  <div class="d-flex align-items-center gap-2">
    <span class="text-muted d-none d-md-inline" style="font-size:.77rem;">
      Dashboard &rsaquo; Invoices
    </span>
    <a href="{{ route('invoices.create') }}" class="btn btn-p ms-2">
      <i class="ti ti-plus me-1"></i>New Invoice
    </a>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     ROW 1 — Hero  +  Week Overview
══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">

  {{-- Hero --}}
  <div class="col-xl-5 col-lg-5">
    <div class="hero">
      <div class="d-flex justify-content-between align-items-start">
        <div style="position:relative;z-index:1;max-width:58%;">
          <h5>Professional Invoices Made Easy.</h5>
          <p class="mb-3">
            Quickly understand who your best interns are<br>
            and motivate them to clear their dues.
          </p>
               <a href="javascript:void(0);" 
   class="btn btn-p sm" 
   id="watchTutorialBtn">
  <i class="ti ti-sparkles me-1"></i>Watch Tutorial
</a>

<script>
document.getElementById('watchTutorialBtn').addEventListener('click', function() {
  Swal.fire({
    title: '📺 Tutorial Coming Soon',
    html: '<div class="text-start"><p><strong>Invoice Management Guide:</strong></p><ul><li>Create Invoice → Fill details → Submit</li><li>Filter by date/status/type</li><li>Record payments when received</li><li>Download PDF for records</li></ul></div>',
    icon: 'info',
    confirmButtonText: 'Got it',
    confirmButtonColor: '#2b9a82'
  });
});
</script>
        </div>
        <div style="position:relative;z-index:1;flex-shrink:0;">
          <svg width="120" height="120" viewBox="0 0 120 120"
               fill="none" xmlns="http://www.w3.org/2000/svg">
            <circle cx="60" cy="60" r="60" fill="rgba(43,154,130,.08)"/>
            <rect x="30" y="20" width="60" height="72" rx="8"
                  fill="white" stroke="#2b9a82" stroke-width="1.8"/>
            <rect x="38" y="33" width="44" height="5" rx="2.5" fill="#e8f5f2"/>
            <rect x="38" y="45" width="32" height="3.5" rx="1.75" fill="#d1fae5"/>
            <rect x="38" y="55" width="36" height="3.5" rx="1.75" fill="#d1fae5"/>
            <rect x="38" y="65" width="24" height="3.5" rx="1.75" fill="#d1fae5"/>
            <circle cx="85" cy="88" r="20" fill="#2b9a82"/>
            <path d="M77 88l6 6 10-10"
                  stroke="white" stroke-width="2.8"
                  stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>
    </div>
  </div>

 {{-- Week Overview --}}
<div class="col-xl-7 col-lg-7">
    <div class="k h-100">
        <div class="kh">
            <span class="kh-t">This Week's Overview</span>
            <span class="pill">
    Current Week
</span>
        </div>
        <div class="row g-0 h-100">
            <div class="col-6 col-md-4 border-end border-bottom border-md-bottom-0" style="border-color:var(--bd)!important;">
                <div class="wov-card">
                    <div class="wov-content">
                        <div class="wov-n">{{ $stats['total'] }}</div>
                        <div class="wov-l">Total Invoices</div>
                        <div class="mt-2">
                            <span class="percentage-tag tag-in-lite">3.14%</span>
                            <span class="text-muted" style="font-size: 0.65rem;">since last week</span>
                        </div>
                    </div>
                    <div class="wov-chart" id="chartTotal"></div>
                </div>
            </div>

            <div class="col-6 col-md-4 border-end border-bottom border-md-bottom-0" style="border-color:var(--bd)!important;">
                <div class="wov-card">
                    <div class="wov-content">
                        <div class="wov-n">{{ $stats['paid'] }}</div>
                        <div class="wov-l">Paid Invoices</div>
                        <div class="mt-2">
                            <span class="percentage-tag tag-dn-lite">1.15%</span>
                            <span class="text-muted" style="font-size: 0.65rem;">since last week</span>
                        </div>
                    </div>
                    <div class="wov-chart" id="chartPaid"></div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="wov-card">
                    <div class="wov-content">
                        <div class="wov-n">{{ $stats['pending'] }}</div>
                        <div class="wov-l">Invoices Sent</div>
                        <div class="mt-2">
                            <span class="percentage-tag tag-up-lite">1.15%</span>
                            <span class="text-muted" style="font-size: 0.65rem;">since last week</span>
                        </div>
                    </div>
                    <div class="wov-chart" id="chartPending"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
    ROW 2 — Chart + Transactions + Structure
══════════════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">

  {{-- LEFT: PAYMENT OVERVIEW (Area Chart) --}}
  <div class="col-xl-7 col-lg-7">
    <div class="k h-100">
      <div class="kh">
        <span class="kh-t">
          <i class="ti ti-chart-area me-1" style="color:var(--p);"></i>
          Payment Activity
        </span>
        <div class="d-flex gap-1">
            <span class="pill">ALL</span>
            <span class="pill">1M</span>
            <span class="pill">6M</span>
            <span class="pill" style="background:var(--p); color:#fff; border-color:var(--p);">1Y</span>
        </div>
      </div>

      <div class="p-3">
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div>
                <div style="font-size:1.4rem;font-weight:800;color:var(--tx);">
                  PKR {{ number_format($stats['total_amount'], 2) }}
                </div>
            </div>
            <div class="d-flex gap-3" style="font-size:.75rem;">
                <span><b style="color:var(--p);">PKR {{ number_format($stats['received_amount'] / 1000, 0) }}k</b> Incomes</span>
                <span><b style="color:#e11d48;">PKR {{ number_format($stats['remaining_amount'] / 1000, 0) }}k</b> Expenses</span>
            </div>
        </div>
        <div id="payChart" style="min-height:280px;"></div>
      </div>
    </div>
  </div>

  {{-- RIGHT: TRANSACTIONS & STRUCTURE --}}
  <div class="col-xl-5 col-lg-5">
    <div class="row g-3 h-100">
        
        {{-- Recent Transactions --}}
        <div class="col-12 col-md-12 col-xl-6">
            <div class="k h-100">
              <div class="kh">
                <span class="kh-t">Recent Transactions</span>
              </div>
              <div class="p-3" style="overflow-y:auto; max-height:400px;">
                @forelse($recentTransactions as $ri => $txn)
                  @php
                    $txnColor = $avColours[$ri % count($avColours)];
                    $txnIcon   = $avIcons[$ri % count($avIcons)];
                    $txnAmt   = $txn->amount ?? $txn->received_amount ?? 0;
                  @endphp
                  <div class="txn">
                    <div class="txn-av" style="background:{{ $txnColor }}; width:32px; height:32px; font-size:.7rem;">
                      <i class="ti {{ $txnIcon }}"></i>
                    </div>
                    <div class="flex-grow-1">
                      <div class="txn-name" style="font-size:.75rem;">{{ optional($txn->invoice)->inv_id ?? 'TXN-'.$txn->id }}</div>
                      <div class="txn-date" style="font-size:.6rem;">{{ $txn->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="txn-amt txn-pos" style="font-size:.75rem;">+{{ number_format($txnAmt) }}</div>
                  </div>
                @empty
                  <div class="text-muted text-center py-4 small">No transactions</div>
                @endforelse
              </div>
            </div>
        </div>

       {{-- Structure (Donut Chart) --}}
<div class="col-12 col-md-12 col-xl-6">
    <div class="k h-100 d-flex flex-column">
        <div class="kh">
            <span class="kh-t">Structure</span>
            <span class="pill">Weekly ▾</span>
        </div>
        
        <div class="p-3 d-flex flex-column flex-grow-1 justify-content-between">
            <div id="structureDonut" style="min-height: 210px;"></div>
            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center py-2 border-top" style="border-color:var(--bd)!important;">
                    <div class="d-flex align-items-center" style="font-size: 0.72rem; color: var(--mu);">
                        <span class="rounded-circle me-2" style="width: 8px; height: 8px; background-color: #2b9a82; display: inline-block;"></span>
                        Invoiced
                    </div>
                    <div class="fw-bold" style="font-size: 0.75rem; color: var(--tx);">
                        $56,236 <span class="text-success ms-1" style="font-size: 0.65rem;">+0.4%</span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-top" style="border-color:var(--bd)!important;">
                    <div class="d-flex align-items-center" style="font-size: 0.72rem; color: var(--mu);">
                        <span class="rounded-circle me-2" style="width: 8px; height: 8px; background-color: #334155; display: inline-block;"></span>
                        Collected
                    </div>
                    <div class="fw-bold" style="font-size: 0.75rem; color: var(--tx);">
                        $12,596 <span class="text-danger ms-1" style="font-size: 0.65rem;">-0.7%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════
     FILTERS CARD (Above Invoice List)
══════════════════════════════════════════════════════ --}}
<div class="filter-card">
  <form method="GET" action="{{ route('invoices.dashboard') }}" id="filterForm">
    <div class="row g-3">
      <div class="col-md-3">
        <label class="filter-label">FROM DATE</label>
        <div class="filter-input-icon">
          <i class="ti ti-calendar"></i>
          <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
      </div>
      <div class="col-md-3">
        <label class="filter-label">TO DATE</label>
        <div class="filter-input-icon">
          <i class="ti ti-calendar"></i>
          <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
      </div>
      <div class="col-md-2">
        <label class="filter-label">STATUS</label>
        <div class="filter-input-icon">
          <i class="ti ti-tag"></i>
          <select name="status" class="form-select">
            <option value="">All</option>
            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <label class="filter-label">INVOICE TYPE</label>
        <div class="filter-input-icon">
          <i class="ti ti-file-description"></i>
          <select name="invoice_type" class="form-select">
            <option value="">All</option>
            <option value="Internship" {{ request('invoice_type') == 'Internship' ? 'selected' : '' }}>Internship</option>
            <option value="Course" {{ request('invoice_type') == 'Course' ? 'selected' : '' }}>Course</option>
          </select>
        </div>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-p w-100">
          <i class="ti ti-adjustments-horizontal me-1"></i>Apply Filters
        </button>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-md-8">
        <div class="filter-input-icon">
          <i class="ti ti-search"></i>
          <input type="search" name="search" class="form-control search-input" placeholder="Search by invoice ID, name or email" value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-4">
        <button type="button" class="btn btn-outline-secondary w-100 rounded-3" onclick="exportInvoices()" style="border-color:var(--bd);">
          <i class="ti ti-download me-1"></i>Export CSV
        </button>
      </div>
    </div>
  </form>
</div>

{{-- ══════════════════════════════════════════════════════
     INVOICE LIST TABLE
══════════════════════════════════════════════════════ --}}
<div class="k">

  <div class="kh">
    <div class="d-flex align-items-center gap-2">
      <span class="kh-t">
        <i class="ti ti-table me-1" style="color:var(--p);"></i>Invoice List
      </span>
      <span class="ib ib-p ms-1">{{ $invoices->total() ?? 0 }} Total</span>
    </div>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('invoices.dashboard') }}"
         class="pill text-decoration-none">
        <i class="ti ti-refresh me-1"></i>Reset
      </a>
      <span class="pill">SORT BY: Weekly ▾</span>
    </div>
  </div>

  <div style="overflow-x:auto;-webkit-overflow-scrolling:touch;">
    <table class="table ikt mb-0" style="min-width:1100px;width:100%;">
      <thead>
        <tr>
          <th style="width:4%;">#</th>
          <th style="width:10%;">Invoice ID</th>
          <th style="width:16%;">Client</th>
          <th style="width:14%;">Email</th>
          <th style="width:10%;">Total</th>
          <th style="width:10%;">Received</th>
          <th style="width:10%;">Remaining</th>
          <th style="width:9%;">Due Date</th>
          <th style="width:8%;">Status</th>
          <th style="width:9%;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoices as $index => $invoice)
        @php
          $bc = match($invoice->status_badge_class){
              'success' => 'ib-s',
              'danger' => 'ib-d',
              default => 'ib-w'
          };
        @endphp
        <tr>
          <td style="white-space:nowrap;color:var(--mu);font-size:.75rem;">
            {{ $invoices->firstItem() + $index }}
          </td>
          <td style="white-space:nowrap;">
            <span class="ib ib-p">{{ $invoice->inv_id }}</span>
          </td>
          <td style="white-space:normal;">
            <div class="d-flex align-items-center gap-2">
              <span class="iav">{{ $invoice->initials }}</span>
              <div>
                <div style="font-size:.81rem;font-weight:600;color:var(--tx);">
                  {{ $invoice->name }}
                </div>
                <div style="font-size:.69rem;color:var(--mu);">{{ $invoice->formatted_phone }}</div>
              </div>
            </div>
          </td>
          <td style="white-space:nowrap;font-size:.78rem;color:var(--mu);">
            {{ Str::limit($invoice->intern_email, 22) }}
          </td>
          <td style="white-space:nowrap;font-weight:600;font-size:.81rem;">
            {{ $invoice->formatted_total ?? 'PKR '.number_format($invoice->total_amount) }}
          </td>
          <td style="white-space:nowrap;color:#16a34a;font-weight:600;font-size:.81rem;">
            {{ $invoice->formatted_received ?? 'PKR '.number_format($invoice->received_amount) }}
          </td>
          <td style="white-space:nowrap;color:#d97706;font-weight:600;font-size:.81rem;">
            {{ $invoice->formatted_remaining ?? 'PKR '.number_format($invoice->remaining_amount) }}
          </td>
          <td style="white-space:nowrap;font-size:.78rem;color:var(--mu);">
            {{ $invoice->formatted_due_date }}
          </td>
          <td style="white-space:nowrap;">
            <span class="ib {{ $bc }}">{{ $invoice->payment_status }}</span>
          </td>
          <td style="white-space:nowrap;">
            <div class="d-flex gap-1">
              <a href="{{ route('invoices.view', $invoice->id) }}"
                 class="btn iact btn-outline-info"
                 data-bs-toggle="tooltip" title="View Details">
                <i class="ti ti-eye me-1"></i>View
              </a>
              @if($invoice->remaining_amount > 0)
              <a href="{{ route('invoices.payment', $invoice->id) }}"
                 class="btn iact btn-outline-success"
                 data-bs-toggle="tooltip" title="Record Payment">
                <i class="ti ti-cash me-1"></i>Pay
              </a>
              @endif
              <a href="{{ route('invoices.pdf', $invoice->id) }}"
                 class="btn iact btn-outline-primary"
                 target="_blank"
                 data-bs-toggle="tooltip" title="Download PDF">
                <i class="ti ti-file-text me-1"></i>PDF
              </a>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" class="text-center py-5">
            <div class="empty-ico mx-auto">
              <i class="ti ti-file-invoice"></i>
            </div>
            <h6 class="fw-bold mb-1" style="color:var(--tx);">No invoices found</h6>
            <p class="text-muted small mb-3">Create your first invoice to get started</p>
            <a href="{{ route('invoices.create') }}" class="btn btn-p sm px-4">
              <i class="ti ti-plus me-1"></i>Create Invoice
            </a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

 @if($invoices->hasPages())
<div class="d-flex flex-wrap align-items-center justify-content-between pt-3 mt-3 border-top" style="border-color: var(--bd) !important;">
  <div class="small text-muted mb-2 mb-md-0">
    Showing 
    <strong>{{ $invoices->firstItem() ?? 0 }}</strong> 
    to 
    <strong>{{ $invoices->lastItem() ?? 0 }}</strong> 
    of 
    <strong>{{ $invoices->total() ?? 0 }}</strong> 
    entries
  </div>
  
  <div>
    {{ $invoices->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>
@endif

</div>

</div>{{-- /.ik --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>
<script>
/* ══════════════════════════════════════
   Week Overview (Radial Progress Rings)
   ══════════════════════════════════════ */
(function () {
    const commonOptions = {
        chart: { 
            type: 'radialBar', 
            height: 110, 
            width: 110, 
            sparkline: { enabled: true } 
        },
        plotOptions: {
            radialBar: {
                hollow: { size: '72%' },
                track: { 
                    background: '#e2e8f0', 
                    opacity: 0.4,
                    strokeWidth: '100%',
                    margin: 0
                },
                dataLabels: { show: false }
            }
        },
        stroke: { lineCap: 'round' }
    };

    new ApexCharts(document.querySelector("#chartTotal"), {
        ...commonOptions,
        series: [75], 
        colors: ['#0ea5e9'] 
    }).render();

    new ApexCharts(document.querySelector("#chartPaid"), {
        ...commonOptions,
        series: [45], 
        colors: ['#f43f5e'] 
    }).render();

    new ApexCharts(document.querySelector("#chartPending"), {
        ...commonOptions,
        series: [60],
        colors: ['#2b9a82'] 
    }).render();
})();

/* ══════════════════════════════════════
   Payment Activity (Area Chart)
   ══════════════════════════════════════ */
(function () {
    const seriesData = @json($chartSeries);
    const labelData  = @json($chartLabels);

    const options = {
        chart: {
            type: 'area',
            height: 280,
            toolbar: { show: false },
            zoom: { enabled: false },
            fontFamily: 'inherit',
            background: 'transparent'
        },
        series: [{ name: 'Received', data: seriesData }],
        xaxis: {
            categories: labelData,
            axisBorder: { show: false },
            axisTicks:  { show: false },
            labels: { style: { fontSize: '10px', colors: '#8892a4' } }
        },
        yaxis: {
            labels: {
                style: { fontSize: '10px', colors: '#8892a4' },
                formatter: v => {
                    if (v >= 1000000) return (v/1000000).toFixed(1)+'M';
                    if (v >= 1000)    return (v/1000).toFixed(0)+'k';
                    return v;
                },
            },
        },
        stroke: { curve: 'smooth', width: 2.5 },
        colors: ['#2b9a82'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: { enabled: false },
        grid: {
            borderColor: '#eaecf2',
            strokeDashArray: 4,
            padding: { right: 20 }
        },
        markers: {
            size: 4,
            colors: ['#fff'],
            strokeColors: '#2b9a82',
            strokeWidth: 2,
            hover: { size: 6 }
        },
        tooltip: {
            theme: 'light',
            y: { formatter: v => 'PKR ' + Number(v).toLocaleString('en-PK') }
        }
    };

    new ApexCharts(document.querySelector('#payChart'), options).render();
})();

/* ══════════════════════════════════════
   Structure (Donut Chart)
   ══════════════════════════════════════ */
(function () {
    const options = {
        series: [56.3, 25.4, 18.3],
        chart: {
            type: 'donut',
            height: 220,
            sparkline: { enabled: true }
        },
        labels: ['Invoiced', 'Collected', 'Pending'],
        colors: ['#2b9a82', '#334155', '#e11d48'], 
        legend: { show: false },
        dataLabels: { enabled: false },
        plotOptions: {
            pie: {
                donut: {
                    size: '82%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '12px',
                            color: '#8892a4',
                            formatter: () => '56%'
                        }
                    }
                }
            }
        },
        stroke: { width: 0 }
    };

    new ApexCharts(document.querySelector("#structureDonut"), options).render();
})();

/* ══════════════════════════════════════
   Export Invoices Function
   ══════════════════════════════════════ */
function exportInvoices() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    window.location.href = "{{ route('invoices.export') }}?" + params;
}

/* ══════════════════════════════════════
   Initialize Tooltips
   ══════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush