@extends('layouts/layoutMaster')

@section('title', 'Payment Accounts')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js',
'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js',
'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/forms-pickers.js'])
@endsection

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Balance</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">Rs: {{ number_format($totalBalance, 2) }}</h4>
            </div>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-wallet icon-26px"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Payment Accounts</h4>
</div>

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Error Message --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ session('error') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
  @foreach($errors->all() as $error)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ $error }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endforeach
@endif

<script>
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.classList.remove('show');
      alert.classList.add('hide');
      setTimeout(() => alert.remove(), 500);
    });
  }, 5000);
</script>

<div class="card">
  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 justify-content-between my-6">
        <div class="d-flex align-items-center gap-md-4 justify-between gap-2" style="justify-content: space-between">
          <form method="GET" action="{{ route('bank-accounts.index') }}" id="filterForm"
            class="d-flex flex-wrap gap-3 justify-between align-items-center w-full"
            style="width: 100%; justify-content: space-between">
            <div>
              <select name="per_page" class="form-select auto-submit">
                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
              </select>
            </div>
            <div>
              <input type="search" name="search" class="form-control" placeholder="Search account..."
                value="{{ request('search') }}">
            </div>
          </form>
          <div class="d-flex gap-2">
            <button class="btn add-new btn-outline-primary rounded-2 waves-effect px-7 waves-light text-nowrap" 
              data-bs-toggle="modal" data-bs-target="#transferHistoryModal">
              <span><i class="icon-base ti tabler-history icon-xs me-2"></i><span class="d-none d-sm-inline-block">History</span></span>
            </button>
            <button class="btn add-new btn-primary rounded-2 waves-effect px-7 waves-light text-nowrap" 
              data-bs-toggle="modal" data-bs-target="#addAccountModal">
              <span><i class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Add Account</span></span>
            </button>
          </div>
        </div>
      </div>

      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto" style="max-height: 500px;">
          <table class="datatables-users table dataTable dtr-column" style="width: 100%;">
            <thead class="border-top sticky-top bg-card">
              <tr>
                <th>Account Name</th>
                <th>Account Number</th>
                <th>Opening Balance</th>
                <th>Current Balance</th>
                <th>Added By</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($bankAccounts as $account)
              <tr>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap fw-bold">{{ $account->account_name }}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ $account->account_number ?? '-' }}</span></td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">Rs: {{ number_format($account->opening_balance, 2) }}</span></td>
                <td>
                  <span class="text-truncate d-flex align-items-center text-heading text-nowrap fw-bold 
                    {{ $account->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                    <i class="icon-base ti tabler-wallet icon-20px me-2"></i>
                    Rs: {{ number_format($account->current_balance, 2) }}
                  </span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{ $account->added_by ?? '-' }}</span></td>
                <td>
                  <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-text-primary rounded-pill waves-effect btn-icon transfer-btn"
                      data-id="{{ $account->id }}"
                      data-name="{{ $account->account_name }}"
                      data-balance="{{ $account->current_balance }}"
                      data-bs-toggle="modal" data-bs-target="#transferModal"
                      title="Fund Transfer">
                      <i class="icon-base ti tabler-transfer icon-22px"></i>
                    </button>
                    <button class="btn btn-text-danger rounded-pill waves-effect btn-icon deactivate-btn"
  data-id="{{ $account->id }}"
  data-name="{{ $account->account_name }}"
  title="Deactivate">
  <i class="icon-base ti tabler-trash icon-22px"></i>
</button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6">
                  <p class="text-center mb-0">No payment accounts found!</p>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="row mx-3 justify-content-between">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $bankAccounts->firstItem() ?? 0 }} to {{ $bankAccounts->lastItem() ?? 0 }} of {{ $bankAccounts->total() ?? 0 }} entries
          </div>
        </div>
        <div class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                <li class="dt-paging-button page-item {{ $bankAccounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $bankAccounts->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $bankAccounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $bankAccounts->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                @foreach ($bankAccounts->getUrlRange(max(1, $bankAccounts->currentPage() - 2), min($bankAccounts->lastPage(), $bankAccounts->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $bankAccounts->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach
                <li class="dt-paging-button page-item {{ $bankAccounts->currentPage() == $bankAccounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $bankAccounts->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
                <li class="dt-paging-button page-item {{ $bankAccounts->currentPage() == $bankAccounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $bankAccounts->url($bankAccounts->lastPage()) }}" aria-label="Last">
                    <i class="icon-base ti tabler-chevrons-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ==================== ADD ACCOUNT MODAL ==================== --}}
<div class="modal fade" id="addAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" 
          class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-start mb-6">
          <h4 class="role-title">Add Payment Account</h4>
        </div>
        <form action="{{ route('bank-accounts.store') }}" method="POST" class="row g-3">
          @csrf
          <div class="col-md-6">
            <label class="form-label" for="account_name">Account Name <span class="text-danger">*</span></label>
            <input type="text" name="account_name" class="form-control" required placeholder="e.g., Cash In Hand">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="account_number">Account Number</label>
            <input type="text" name="account_number" class="form-control" placeholder="e.g., 08010108482832">
          </div>
          <div class="col-md-6">
            <label class="form-label" for="opening_balance">Opening Balance <span class="text-danger">*</span></label>
            <input type="number" name="opening_balance" class="form-control" value="0" step="0.01" min="0" required>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="note">Note</label>
            <textarea name="note" class="form-control" rows="1" placeholder="Optional note..."></textarea>
          </div>
          <input type="hidden" name="account_type" value="payment">
          <div class="col-12 text-end mt-3">
            <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ==================== FUND TRANSFER MODAL ==================== --}}
<div class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" 
          class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-start mb-6">
          <h4 class="role-title">Fund Transfer</h4>
          <small class="text-muted" id="transferFromDisplay"></small>
        </div>
        <form action="{{ route('bank-accounts.transfer.process') }}" method="POST" class="row g-3" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="from_bank_account_id" id="transferFromId">
          
          <div class="col-12">
            <label class="form-label" for="to_bank_account_id">Transfer To <span class="text-danger">*</span></label>
            <select name="to_bank_account_id" class="form-select" required>
              <option value="">Select Destination Account</option>
              @foreach($bankAccounts as $acc)
                <option value="{{ $acc->id }}" data-name="{{ $acc->account_name }}">
                  {{ $acc->account_name }} @if($acc->account_number)({{ $acc->account_number }})@endif
                  - Rs {{ number_format($acc->current_balance, 2) }}
                </option>
              @endforeach
            </select>
          </div>
          
          <div class="col-md-6">
            <label class="form-label" for="amount">Amount (Rs) <span class="text-danger">*</span></label>
            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00" id="transferAmount">
            <small class="text-danger" id="amountWarning" style="display:none;">Insufficient balance!</small>
          </div>
          
          <div class="col-md-6">
            <label class="form-label" for="transfer_date">Date <span class="text-danger">*</span></label>
            <input type="text" name="transfer_date" class="form-control flatpickr-date" value="{{ date('Y-m-d') }}" required>
          </div>
          
          <div class="col-12">
            <label class="form-label" for="note">Note</label>
            <textarea name="note" class="form-control" rows="2" placeholder="Optional transfer note..."></textarea>
          </div>
          
          <div class="col-12">
            <label class="form-label" for="document">Attach Document</label>
            <input type="file" name="document" class="form-control" accept=".pdf,.csv,.zip,.doc,.docx,.jpeg,.jpg,.png">
            <small class="text-muted">Max 5MB | .pdf, .csv, .zip, .doc, .docx, .jpeg, .jpg, .png</small>
          </div>
          
          <div class="col-12 text-end mt-3">
            <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="transferSubmit">Transfer Funds</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ==================== TRANSFER HISTORY MODAL ==================== --}}
<div class="modal fade" id="transferHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content p-2">
      <div class="modal-body">
        <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" 
          class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-start mb-6">
          <h4 class="role-title">Fund Transfer History</h4>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Transfer ID</th>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Amount</th>
                <th>Note</th>
              </tr>
            </thead>
            <tbody id="transferHistoryBody">
              <tr><td colspan="6" class="text-center">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Filter auto-submit
    const form = document.getElementById('filterForm');
    document.querySelectorAll('.auto-submit').forEach(input => {
      input.addEventListener('change', function () { form.submit(); });
    });
    const searchInput = form.querySelector('input[name="search"]');
    let typingTimer;
    searchInput.addEventListener('keyup', function () {
      clearTimeout(typingTimer);
      typingTimer = setTimeout(() => { form.submit(); }, 600);
    });

    // Initialize flatpickr for transfer date
    if (typeof flatpickr !== 'undefined') {
      flatpickr('.flatpickr-date', { dateFormat: "Y-m-d", defaultDate: "{{ date('Y-m-d') }}" });
    }

    // Transfer modal - pre-fill source account
    document.querySelectorAll('.transfer-btn').forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const name = this.dataset.name;
        const balance = this.dataset.balance;
        
        document.getElementById('transferFromId').value = id;
        document.getElementById('transferFromDisplay').textContent = 
          'From: ' + name + ' (Available: Rs ' + parseFloat(balance).toLocaleString('en-US', {minimumFractionDigits: 2}) + ')';
        
        document.getElementById('transferFromId').dataset.balance = balance;
        document.getElementById('amountWarning').style.display = 'none';
        document.getElementById('transferSubmit').disabled = false;
        document.getElementById('transferAmount').value = '';
        
        const toSelect = document.querySelector('#transferModal select[name="to_bank_account_id"]');
        for (let opt of toSelect.options) {
          opt.style.display = (opt.value == id) ? 'none' : '';
        }
        toSelect.value = '';
      });
    });

    // Validate amount on input
    document.getElementById('transferAmount').addEventListener('input', function () {
      const amount = parseFloat(this.value) || 0;
      const balance = parseFloat(document.getElementById('transferFromId').dataset.balance) || 0;
      const warning = document.getElementById('amountWarning');
      const submit = document.getElementById('transferSubmit');
      
      if (amount > balance) {
        warning.style.display = 'block';
        submit.disabled = true;
      } else {
        warning.style.display = 'none';
        submit.disabled = false;
      }
    });

    // Load transfer history on modal open
    document.getElementById('transferHistoryModal').addEventListener('show.bs.modal', function () {
      fetch('{{ route("bank-accounts.transfer.history.data") }}')
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById('transferHistoryBody');
          if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No transfers yet</td></tr>';
            return;
          }
          tbody.innerHTML = data.map(t => `
            <tr>
              <td><strong>${t.transfer_id}</strong></td>
              <td>${t.transfer_date}</td>
              <td>${t.from_account}</td>
              <td>${t.to_account}</td>
              <td class="fw-bold text-info">Rs ${parseFloat(t.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
              <td>${t.note || '-'}</td>
            </tr>
          `).join('');
        });
    });

    // ============ DEACTIVATE ACCOUNT WITH SWEETALERT ============
    document.querySelectorAll('.deactivate-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const name = this.dataset.name;
        
        Swal.fire({
          title: 'Deactivate Account?',
          html: `<span>Are you sure you want to deactivate <strong>"${name}"</strong>?</span>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, deactivate',
          confirmButtonColor: '#ef4444',
          cancelButtonColor: '#6c757d',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
           window.location.href = '{{ url("admin/payment-accounts") }}/' + id + '/deactivate';          }
        });
      });
    });

  });
</script>
@endpush