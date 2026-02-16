@extends('layouts/layoutMaster')

@section('title', 'Accounts')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss',
'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.scss',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.scss',
'resources/assets/vendor/libs/pickr/pickr-themes.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/flatpickr/flatpickr.js',
'resources/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js',
'resources/assets/vendor/libs/jquery-timepicker/jquery-timepicker.js', 'resources/assets/vendor/libs/pickr/pickr.js'])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@vite(['resources/assets/js/forms-pickers.js'])
@endsection
@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Credit</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">Rs: {{$totalCredit}}</h4>

            </div>

          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="icon-base ti tabler-trending-up icon-26px"></i>


            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Debit</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">Rs: {{$totalDebit}}</h4>

            </div>

          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="icon-base ti tabler-trending-down icon-26px"></i>


            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span class="text-heading">Total Balance</span>
            <div class="d-flex align-items-center my-1">
              <h4 class="mb-0 me-2">Rs: {{$totalBalance}}</h4>

            </div>

          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="icon-base ti tabler-building-bank icon-26px"></i>



            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Accounts</h4>
</div>
{{-- Error Messages --}}
@if($errors->any())
@foreach($errors->all() as $error)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  {{ $error }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endforeach
@endif

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
  {{ session('success') }}
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Auto-hide script --}}
<script>
  setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            alert.classList.add('hide');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000); // 5 seconds
</script>

<div class="card">

  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 justify-content-between my-6">



        <div class="d-flex align-items-center gap-md-4 justify-between gap-2" style="justify-content: space-between">

          <form method="GET" action="{{ route('accounts.admin') }}" id="filterForm"
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
            <div class="d-flex gap-3 flex-wrap">
              <!-- From Date -->
              <div class="d-flex" style="align-items: center">
                <label class="form-label me-1">From:</label>
                <input type="date" name="from_date" class="form-control auto-submit" value="{{ request('from_date') }}">
              </div>

              <!-- To Date -->
              <div class="d-flex" style="align-items: center">
                <label class="form-label me-1">To:</label>
                <input type="date" name="to_date" class="form-control auto-submit" value="{{ request('to_date') }}">
              </div>

              <!-- Search -->
              <div>
                <input type="search" name="search" class="form-control" placeholder="Search description..."
                  value="{{ request('search') }}">
              </div>
            </div>

            
          </form>
          @php
            $adminSettings = \App\Models\AdminSetting::first();

            if (!$adminSettings) {
            $isAdminAllowed = true;
            } else {
            $permissions = $adminSettings->export_permissions;
            $isAdminAllowed = isset($permissions['admin']) && $permissions['admin'] == 1;
            }
            @endphp

            @if($isAdminAllowed)
            <div class="btn-group" role="group">
              <button id="btnGroupDrop1" type="button" class="btn add-new btn-outline-primary dropdown-toggle"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="icon-base ti tabler-dots-vertical icon-md d-sm-none"></i>
                <i class="icon-base ti tabler-upload icon-xs me-2"></i>
                <span class="d-none d-sm-block">Export</span>
              </button>
              <div class="dropdown-menu" style="z-index: 1021" aria-labelledby="btnGroupDrop1">
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadAccountsCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
                </a>
              </div>
            </div>
            @endif
          <button class="btn add-new btn-primary rounded-2 waves-effect px-7 waves-light text-nowrap" tabindex="0"
            aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
            data-bs-target="#addTransactionModal"><span><i
                class="icon-base ti tabler-plus me-0 me-sm-1 icon-16px"></i><span class="d-none d-sm-inline-block">Add
                Transaction</span></span></button>
          <!-- Add Transaction -->
          <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Add Transaction</h4>
                  </div>
                  <form id="addRoleForm" class="row g-3" method="POST" action="{{route('add-transaction.admin')}}">
                    @csrf
                    <div class="col-12 col-md-4 form-control-validation mb-3">
                      <label for="flatpickr-date" class="form-label">Date</label>
                      <input type="text" required class="form-control" name='date' placeholder="YYYY-MM-DD"
                        id="flatpickr-date" />
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                      <label class="form-label" for="operation">Operation</label>
                      <select name="operation" id="operation" required class="form-select text-capitalize">
                        <option value="">Select Operation</option>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                      </select>
                    </div>

                    <div class="col-12 col-md-4 form-control-validation mb-3">
                      <label class="form-label" for="amount">Amount</label>
                      <input type="number" id="amount" name="amount" class="form-control" placeholder="Enter Amount"
                        tabindex="-1" />
                    </div>
                    <div class="col-12 form-control-validation mb-3">
                      <label class="form-label" for="description">Description</label>
                      <textarea name="description" rows="5" class="form-control" placeholder="Write description..."
                        tabindex="-1"></textarea>
                    </div>






                    <div class="col-12 text-end mt-3">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                      <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- Add Transaction -->
        </div>
      </div>
      <div class="justify-content-between dt-layout-table">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto"
          style="max-height: 500px;">
          <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
            aria-describedby="DataTables_Table_0_info" style="width: 100%;">

            <thead class="border-top sticky-top bg-card">
              <tr>
                <th data-dt-column="0" class="control dt-orderable-none dtr-hidden" rowspan="1" colspan="1"
                  aria-label="" style="display: none;"><span class="dt-column-title"></span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Date
                  </span><span class="dt-column-order"></span></th>


                <th data-dt-column="1" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Description
                  </span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Credit</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Debit</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Balance</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Action</span><span
                    class="dt-column-order"></span></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($accounts as $account)
              <tr class="">
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$account->date}}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$account->description}}</span>
                </td>

                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">
                    @if ($account->credit)
                    <i class="icon-base ti tabler-trending-up icon-20px text-success me-2"></i> Rs: {{$account->credit}}
                    @else
                    -
                    @endif
                  </span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"> 
                  @if($account->debit)
                    <i class="icon-base ti tabler-trending-down icon-20px text-warning me-2"></i> Rs:
                    {{$account->debit}}
                    @else
                    -
                    @endif</span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap"> 
                  @if($account->balance)
                    <i class="icon-base ti tabler-building-bank icon-20px me-2 text-danger"></i>


                    Rs: {{$account->balance}}
                    @else
                    -
                    @endif</span>
                </td>
                <td>

                  <div class="d-flex align-items-center">



                    <a href="javascript:void(0);"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-transaction-btn"
                      data-bs-toggle="modal" data-bs-target="#editTransactionModal" data-id="{{ $account->id }}"
                      data-date="{{ $account->date }}" data-operation="{{ $account->credit > 0 ? 'credit' : 'debit' }}"
                      data-amount="{{ $account->credit > 0 ? $account->credit : $account->debit }}"
                      data-description="{{ $account->description }}">
                      <i class="icon-base ti tabler-edit icon-22px"></i>
                    </a>

                  </div>

                </td>


              </tr>


              @empty
              <tr>
                <td colspan="11">
                  <p class="text-center mb-0">No data available!</p>
                </td>
              </tr>
              @endforelse
            </tbody>
            <tfoot></tfoot>
          </table>
          <!-- Edit Transaction Modal -->
          <div class="modal fade" id="editTransactionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
              <div class="modal-content p-2">
                <div class="modal-body">
                  <button type="button" style="inset-block-start: 0rem !important; inset-inline-end: 0rem !important;" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  <div class="text-start mb-6">
                    <h4 class="role-title">Edit Transaction</h4>
                  </div>

                  <form method="POST" class="row g-3" id="editTransactionForm">
                    @csrf
                    @method('PUT')

                    <div class="col-12 col-md-4 form-control-validation mb-3">
                      <label for="edit-date" class="form-label">Date</label>
                      <input type="text" class="form-control" name="date" id="edit-date" placeholder="YYYY-MM-DD"
                        required />
                    </div>

                    <div class="col-12 col-md-4 mb-3">
                      <label class="form-label" for="edit-operation">Operation</label>
                      <select name="operation" id="edit-operation" required class="form-select text-capitalize">
                        <option value="">Select Operation</option>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                      </select>
                    </div>

                    <div class="col-12 col-md-4 form-control-validation mb-3">
                      <label class="form-label" for="edit-amount">Amount</label>
                      <input type="number" id="edit-amount" name="amount" class="form-control"
                        placeholder="Enter Amount" required />
                    </div>

                    <div class="col-12 form-control-validation mb-3">
                      <label class="form-label" for="edit-description">Description</label>
                      <textarea name="description" rows="5" id="edit-description" class="form-control"
                        placeholder="Write description..." required></textarea>
                    </div>

                    <div class="col-12 text-end mt-3">
                      <button type="reset" class="btn btn-label-secondary me-2" data-bs-dismiss="modal"
                        aria-label="Close">Cancel</button>
                      <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                  </form>

                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
      <div class="row mx-3 justify-content-between">
        {{-- Info --}}
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $accounts->firstItem() ?? 0 }} to {{ $accounts->lastItem() ?? 0 }} of {{
            $accounts->total() ??
            0 }} entries
          </div>
        </div>

        {{-- Pagination --}}
        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">
                {{-- First Page --}}
                <li class="dt-paging-button page-item {{ $accounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $accounts->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Previous Page --}}
                <li class="dt-paging-button page-item {{ $accounts->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $accounts->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Page Numbers --}}
                @foreach ($accounts->getUrlRange(max(1, $accounts->currentPage() - 2),
                min($accounts->lastPage(),
                $accounts->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $accounts->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach

                {{-- Next Page --}}
                <li
                  class="dt-paging-button page-item {{ $accounts->currentPage() == $accounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $accounts->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>

                {{-- Last Page --}}
                <li
                  class="dt-paging-button page-item {{ $accounts->currentPage() == $accounts->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $accounts->url($accounts->lastPage()) }}" aria-label="Last">
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

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const addTransactionForm = document.getElementById('addRoleForm');
    const dateInput = addTransactionForm.querySelector('input[name="date"]');

    // Set default date to today if empty
    const today = new Date().toISOString().split('T')[0];
    if (!dateInput.value) dateInput.value = today;

    // Make all fields required
    ['date', 'operation', 'amount', 'description'].forEach(name => {
        const field = addTransactionForm.querySelector(`[name="${name}"]`);
        if (field) field.required = true;
    });

    // Initialize flatpickr with modal fix
    if (typeof flatpickr !== 'undefined') {
        flatpickr(dateInput, {
            dateFormat: "Y-m-d",
            defaultDate: today,
            static: true,
            appendTo: document.getElementById('addTransactionModal'),
        });
    }
});


</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editTransactionModal');
    const form = document.getElementById('editTransactionForm');

    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        // Get data from button attributes
        const id = button.getAttribute('data-id');
        const date = button.getAttribute('data-date');
        const operation = button.getAttribute('data-operation');
        const amount = button.getAttribute('data-amount');
        const description = button.getAttribute('data-description');

        // Set form action dynamically
        form.action = "{{ url('admin/transactions/update') }}/" + id;


        // Fill form fields
        document.getElementById('edit-date').value = date;
        document.getElementById('edit-operation').value = operation;
        document.getElementById('edit-amount').value = amount;
        document.getElementById('edit-description').value = description;

        // Initialize flatpickr if needed
        if (typeof flatpickr !== 'undefined') {
            flatpickr(document.getElementById('edit-date'), {
                dateFormat: "Y-m-d",
                defaultDate: date,
                appendTo: editModal,
                static: true,
            });
        }
    });
});

</script>

@endpush



<script>
  document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');

    // Auto submit when date changes
    document.querySelectorAll('.auto-submit').forEach(input => {
        input.addEventListener('change', function () {
            form.submit();
        });
    });

    // Auto submit search with small delay (typing)
    const searchInput = form.querySelector('input[name="search"]');

    let typingTimer;
    searchInput.addEventListener('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            form.submit();
        }, 600); // 600ms delay
    });

});
</script>

<script>
  function downloadAccountsCSV() {
    // Get current filter values
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();

    // Redirect to export route with filters
    window.location.href = "{{ route('accounts.export.csv.admin') }}?" + params;
}
</script>






@endsection