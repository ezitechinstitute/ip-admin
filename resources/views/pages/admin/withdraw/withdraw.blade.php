@extends('layouts/layoutMaster')

@section('title', 'Intern Projects')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
'resources/assets/vendor/libs/select2/select2.scss',
'resources/assets/vendor/libs/@form-validation/form-validation.scss',
'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/moment/moment.js',
'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js',
'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleave-zen/cleave-zen.js',
'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
{{-- @vite(['resources/assets/js/extended-ui-sweetalert2.js']) --}}
@endsection

@section('content')

<!-- Users List Table -->
<div class="col-12 mb-6">
  <h4 class="mt-6 mb-1">Withdraw</h4>
</div>

{{-- Success Message --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show col-12 mb-4" role="alert">
  <div class="d-flex align-items-start">
    <i class="icon-base ti tabler-check-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
    <div class="flex-grow-1">
      <h6 class="alert-heading mb-2">{{ session('success') }}</h6>
      <p class="mb-0 text-muted">The withdrawal request has been processed successfully.</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
@endif

{{-- Error Message --}}
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show col-12 mb-4" role="alert">
  <div class="d-flex align-items-start">
    <i class="icon-base ti tabler-alert-circle me-3 mt-1" style="font-size: 1.5rem;"></i>
    <div class="flex-grow-1">
      <h6 class="alert-heading mb-2">Error</h6>
      <p class="mb-0 text-muted">{{ session('error') }}</p>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
</div>
@endif

<div class="card">

  <div class="card-datatable">
    <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">
      <div class="row m-3 my-0 justify-content-between">
        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-length mb-md-6 mb-0 d-flex">

            <form id="perPageForm" method="GET" action="{{route('admin.withdraw')}}">
              <select name="perPage" id="dt-length-0" class="form-select ms-0"
                onchange="document.getElementById('perPageForm').submit()">
                <option value="15" {{ $perPage==15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage==25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage==50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage==100 ? 'selected' : '' }}>100</option>
              </select>


              <!-- Keep search & status in query -->
              <input type="hidden" name="search" value="{{ request('search') }}">
              <input type="hidden" name="status" value="{{ request('status') }}">
            </form>


            <label for="dt-length-0"></label>
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <form method="GET" id="filterForm" class="d-flex gap-2">

            <input type="search" name="search" id="searchInput" class="form-control" placeholder="Search Withdraw"
              value="{{ request('search') }}">
            <style>
              input[type="search"]::-webkit-search-cancel-button,
              input[type="search"]::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
              }
            </style>
            <select name="status" id="statusFilter" class="form-select">
              <option value="">Select Status</option>
              <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Pending</option>
              <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Approved</option>
              <option value="2" {{ request('status')=='2' ? 'selected' : '' }}>Rejected</option>
              <option value="3" {{ request('status')=='3' ? 'selected' : '' }}>Paid</option>
            </select>

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
                <a class="dt-button dropdown-item" href="javascript:void(0);" onclick="downloadWithdrawCSV()">
                  <span>
                    <span class="d-flex align-items-center">
                      <i class="icon-base ti tabler-file-spreadsheet me-1"></i>CSV / Excel
                    </span>
                  </span>
                </a>
              </div>
            </div>
            @endif

          </form>









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
                  aria-label="Profile Picture" tabindex="0"><span class="dt-column-title" role="button">Bank name
                  </span><span class="dt-column-order"></span></th>

                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Account number</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="3" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="Email" tabindex="0"><span class="dt-column-title" role="button">Account holder name
                  </span><span class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Description</span><span
                    class="dt-column-order"></span></th>
                {{-- <th data-dt-column="6" rowspan="1" colspan="1"
                  class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Internship Duration" tabindex="0">
                  <span class="dt-column-title" role="button">Internship
                    Duration</span><span class="dt-column-order"></span>
                </th> --}}
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Date</span><span class="dt-column-order"></span>
                </th>
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Amount</span><span
                    class="dt-column-order"></span></th>

                <th data-dt-column="6" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">STATUS</span><span
                    class="dt-column-order"></span></th>




              </tr>
            </thead>
            <tbody>
              @forelse ($withdraws as $withdraw)
              <tr class="">

                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->bank
                    }}</span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_no
                    }}</span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_name
                    }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->description
                    }}</span>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->date
                    }}</span>
                </td>
                </td>
                <td><span class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->amount
                    }}</span>
                </td>
                <td>
                  @php
                  // Map all 4 statuses to Bootstrap badge classes
                  $statusMap = [
                    0 => ['label' => 'Pending', 'class' => 'bg-label-warning'],
                    1 => ['label' => 'Approved', 'class' => 'bg-label-success'],
                    2 => ['label' => 'Rejected', 'class' => 'bg-label-danger'],
                    3 => ['label' => 'Paid', 'class' => 'bg-label-info'],
                  ];
                  
                  $statusData = $statusMap[$withdraw->req_status] ?? ['label' => 'Unknown', 'class' => 'bg-label-secondary'];
                  @endphp

                  <span class="badge {{ $statusData['class'] }} text-capitalize">{{ $statusData['label'] }}</span>
                <td>
                  {{-- @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  'ongoing' => 'bg-label-primary',
                  'contact' => 'bg-label-info',
                  'expired' => 'bg-label-danger',
                  'completed' => 'bg-label-success',
                  'active' => 'bg-label-success',
                  'removed' => 'bg-label-danger',
                  'freeze' => 'bg-label-danger',
                  ];

                  $status = strtolower($projects->pstatus);
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp --}}

                  {{-- <span class="badge {{ $badgeClass }} text-capitalize">{{ $status }}</span> --}}
                </td>
                {{-- <td><span class="text-heading text-nowrap">{{$intern->duration}}</span></td> --}}
                {{-- <td><span class="text-heading text-nowrap">1</span></td> --}}


                {{-- <td><span class="text-heading text-nowrap">{{$intern->intern_type}}</span></td> --}}
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-icon btn-text-secondary dropdown-toggle hide-arrow"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                      <i class="icon-base ti tabler-dots-vertical"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                      @if($withdraw->req_status == 0)
                        <!-- Pending Request -->
                        <li>
                          <button class="dropdown-item btn-action-approve" data-id="{{ $withdraw->req_id }}">
                            <i class="icon-base ti tabler-check me-2 text-success"></i> Approve
                          </button>
                        </li>
                        <li>
                          <button class="dropdown-item btn-action-reject text-danger" data-id="{{ $withdraw->req_id }}" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="icon-base ti tabler-x me-2"></i> Reject
                          </button>
                        </li>
                      @elseif($withdraw->req_status == 1)
                        <!-- Approved Request -->
                        <li>
                          <button class="dropdown-item btn-action-pay" data-id="{{ $withdraw->req_id }}">
                            <i class="icon-base ti tabler-credit-card me-2 text-primary"></i> Mark as Paid
                          </button>
                        </li>
                      @elseif($withdraw->req_status == 2)
                        <!-- Rejected -->
                        <li><span class="dropdown-item disabled">Rejected</span></li>
                      @elseif($withdraw->req_status == 3)
                        <!-- Paid -->
                        <li><span class="dropdown-item disabled">Paid</span></li>
                      @endif
                    </ul>
                  </div>
                    {{-- <a href="javascript:;"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon delete-record"
                      data-id="{{ $intern->id }}">
                      <i class="icon-base ti tabler-trash icon-22px"></i>
                    </a>
                    <form id="delete-form-{{ $intern->id }}" action="{{ route('interns.destroy', $intern->int_id) }}"
                      method="POST" style="display: none;">
                      @csrf
                      @method('DELETE')
                    </form> --}}

                    {{-- <a href="javascript:void(0);"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon edit-intern"
                      data-bs-toggle="modal" data-bs-target="#editInternModal" data-id="{{ $intern->int_id }}"
                      data-name="{{ $intern->name }}" data-email="{{ $intern->email }}"
                      data-technology="{{ $intern->int_technology }}"
                      data-status="{{ strtolower($intern->int_status) }}">
                      <i class="icon-base ti tabler-edit icon-22px"></i>
                    </a> --}}








                    {{-- <a
                      href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/user/view/account"
                      class="btn btn-text-secondary rounded-pill waves-effect btn-icon">
                      <i class="icon-base ti tabler-eye icon-22px"></i>
                    </a> --}}
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



        </div>
      </div>
      <div class="row mx-3 justify-content-between">

        <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
          <div class="dt-info" aria-live="polite">
            Showing {{ $withdraws->firstItem() ?? 0 }} to {{ $withdraws->lastItem() ?? 0 }} of {{ $withdraws->total() ??
            0 }} entries
          </div>
        </div>

        <div
          class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
          <div class="dt-paging">
            <nav aria-label="pagination">
              <ul class="pagination">

                <li class="dt-paging-button page-item {{ $withdraws->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $withdraws->url(1) }}" aria-label="First">
                    <i class="icon-base ti tabler-chevrons-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li class="dt-paging-button page-item {{ $withdraws->onFirstPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $withdraws->previousPageUrl() }}" aria-label="Previous">
                    <i class="icon-base ti tabler-chevron-left scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                @foreach ($withdraws->getUrlRange(max(1, $withdraws->currentPage() - 2), min($withdraws->lastPage(),
                $withdraws->currentPage() + 2)) as $page => $url)
                <li class="dt-paging-button page-item {{ $page == $withdraws->currentPage() ? 'active' : '' }}">
                  <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
                @endforeach


                <li
                  class="dt-paging-button page-item {{ $withdraws->currentPage() == $withdraws->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $withdraws->nextPageUrl() }}" aria-label="Next">
                    <i class="icon-base ti tabler-chevron-right scaleX-n1-rtl icon-18px"></i>
                  </a>
                </li>


                <li
                  class="dt-paging-button page-item {{ $withdraws->currentPage() == $withdraws->lastPage() ? 'disabled' : '' }}">
                  <a class="page-link" href="{{ $withdraws->url($withdraws->lastPage()) }}" aria-label="Last">
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
    // Helper function to submit form with proper redirect handling
    function submitForm(url, method, data = {}) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = url;
      
      // Add CSRF token
      const token = document.querySelector('meta[name="csrf-token"]')?.content;
      if (token) {
        form.innerHTML += `<input type="hidden" name="_token" value="${token}">`;
      }
      
      // Add method spoofing for PUT/DELETE
      if (method !== 'POST') {
        form.innerHTML += `<input type="hidden" name="_method" value="${method}">`;
      }
      
      // Add any additional data
      for (let key in data) {
        form.innerHTML += `<input type="hidden" name="${key}" value="${data[key]}">`;
      }
      
      document.body.appendChild(form);
      
      // Submit via fetch to handle JSON response
      const formData = new FormData(form);
      
      fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok && response.status !== 400) {
          throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
      })
      .then(response => {
        document.body.removeChild(form);
        
        if (response.success) {
          // Redirect with success message
          window.location.href = response.redirect_url;
        } else {
          // Show error in alert
          alert(response.message || 'An error occurred. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        if (form.parentElement) {
          document.body.removeChild(form);
        }
        alert('Error: ' + error.message + '. Please check the console for details.');
      });
    }

    // Approve button from dropdown

    // Approve button from dropdown
document.querySelectorAll('.btn-action-approve').forEach(btn => {
  btn.addEventListener('click', function (e) {
    e.preventDefault();
    const id = this.dataset.id;
    if (confirm('Approve this withdrawal request?')) {
      // 🔥 Use the route helper with a placeholder
      let url = "{{ route('admin.withdraw.approve', ':id') }}";
      url = url.replace(':id', id); 
      submitForm(url, 'PUT');
    }
  });
});
    // document.querySelectorAll('.btn-action-approve').forEach(btn => {
    //   btn.addEventListener('click', function (e) {
    //     e.preventDefault();
    //     const id = this.dataset.id;
    //     if (confirm('Approve this withdrawal request?')) {
    //       submitForm(`/admin/withdraw/${id}/approve`, 'PUT');
    //     }
    //   });
    // });

    // Reject button from dropdown
    document.querySelectorAll('.btn-action-reject').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        document.getElementById('rejectWithdrawId').value = id;
      });
    });

    // Reject form submission
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
      rejectForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const id = document.getElementById('rejectWithdrawId').value;
        const reason = document.getElementById('rejectReason').value;

        if (reason.trim()) {
          // Reset form and close modal before submitting
          rejectForm.reset();
          const rejectModal = bootstrap.Modal.getInstance(document.getElementById('rejectModal'));
          if (rejectModal) rejectModal.hide();
          
          submitForm(`/admin/withdraw/${id}/reject`, 'POST', { reason: reason });
        } else {
          alert('Please enter a reason for rejection');
        }
      });
    }

    // Mark as Paid button from dropdown
    document.querySelectorAll('.btn-action-pay').forEach(btn => {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        if (confirm('Mark this withdrawal as paid?')) {
          submitForm(`/admin/withdraw/${id}/pay`, 'PUT');
        }
      });
    });

    // Auto-hide success/error alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => alert.remove(), 300);
      }, 5000);
    });
  });

  function downloadWithdrawCSV() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    let url = `{{ route('admin.withdraw.export') }}`;
    if (search) url += `?search=${search}`;
    if (status) url += `${search ? '&' : '?'}status=${status}`;
    window.location.href = url;
  }
</script>
@endpush

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Withdrawal Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="rejectForm">
        <input type="hidden" id="rejectWithdrawId">
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
            <textarea class="form-control" id="rejectReason" required placeholder="Enter reason..." rows="4"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Reject Request</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection