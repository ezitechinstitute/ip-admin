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
  {{-- <p class="mb-0">Find all of your company’s administrator accounts and their associate roles.</p> --}}
</div>
<div class="card">
  {{-- <div class="card-header border-bottom">
    <h5 class="card-title mb-0">Interview Interns
    </h5>

  </div> --}}
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
          <form method="GET"  id="filterForm" class="d-flex gap-2">

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
  <option value="1" {{ request('status')=='1' ? 'selected' : '' }}>Completed</option>
  <option value="0" {{ request('status')=='0' ? 'selected' : '' }}>Pending</option>
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
        <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive overflow-auto" style="max-height: 500px;">
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
</span><span
                    class="dt-column-order"></span></th>
                <th data-dt-column="4" rowspan="1" colspan="1" class="dt-orderable-asc dt-orderable-desc text-nowrap"
                  aria-label="City" tabindex="0"><span class="dt-column-title" role="button">Description</span><span
                    class="dt-column-order"></span></th>
                {{-- <th data-dt-column="6" rowspan="1" colspan="1"
                  class="dt-orderable-asc dt-orderable-desc text-nowrap" aria-label="Internship Duration" tabindex="0">
                  <span class="dt-column-title" role="button">Internship
                    Duration</span><span class="dt-column-order"></span></th> --}}
                <th data-dt-column="5" rowspan="1" colspan="1" class="dt-orderable-none text-nowrap"
                  aria-label="Join Date"><span class="dt-column-title">Date</span><span
                    class="dt-column-order"></span></th>
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
              
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->bank }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_no }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->ac_name }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->description }}</span>
                </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->date }}</span>
                </td>
                  </td>
                <td><span
                    class="text-truncate d-flex align-items-center text-heading text-nowrap">{{$withdraw->amount }}</span>
                </td>
               <td>
                  @php
                  // Map statuses to Bootstrap badge classes
                  $statusClasses = [
                  '1' => 'bg-label-success',
                  '0' => 'bg-label-danger',
                  ];

                  $status = strtolower($withdraw->req_status); // ensure lowercase
                  $badgeClass = $statusClasses[$status] ?? 'bg-label-secondary';
                  @endphp

                  <span class="badge {{ $badgeClass }} text-capitalize">@if ($withdraw->req_status == 1)
                    Completed
                    @else
                    pending
                    @endif</span>
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
                  <div class="d-flex align-items-center">
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
   data-bs-toggle="modal"
   data-bs-target="#editInternModal"
   data-id="{{ $intern->int_id }}"
   data-name="{{ $intern->name }}"
   data-email="{{ $intern->email }}"
   data-technology="{{ $intern->int_technology }}"
   data-status="{{ strtolower($intern->int_status) }}">
   <i class="icon-base ti tabler-edit icon-22px"></i>
</a> --}}








                    {{-- <a href="https://demos.pixinvent.com/vuexy-html-laravel-admin-template/demo-1/app/user/view/account"
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
    const editModal = document.getElementById('editInternModal');
    
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            const button = event.relatedTarget;
            
            // Extract info from data-* attributes
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const technology = button.getAttribute('data-technology');
            const status = button.getAttribute('data-status');

            // Update the modal's content
            const modalBody = editModal.querySelector('.modal-body');
            
            modalBody.querySelector('#int_id').value = id;
            modalBody.querySelector('#name').value = name;
            modalBody.querySelector('#email').value = email;
            modalBody.querySelector('#int_technology').value = technology;
            
            // Set the dropdown value
            const statusSelect = modalBody.querySelector('#int_status');
            if (statusSelect) {
                statusSelect.value = status;
            }
        });
    }
});
</script>
@endpush

@push('scripts')
  <script>
  let timer;

  document.getElementById('searchInput').addEventListener('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(() => {
      document.getElementById('filterForm').submit();
    }, 500); // wait 500ms after typing
  });

  document.getElementById('statusFilter').addEventListener('change', function () {
    document.getElementById('filterForm').submit();
  });
</script>

<script>
  function downloadWithdrawCSV() {
    // Current filter values collect karein
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;

    // URL parameters banayein
    const params = new URLSearchParams({
        search: search,
        status: status
    });

    // Export route par redirect karein
    window.location.href = "{{ route('admin.withdraw.export') }}?" + params.toString();
}
</script>
@endpush







@endsection